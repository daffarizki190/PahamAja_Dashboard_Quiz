<?php

namespace App\Console\Commands;

use App\Models\Answer;
use App\Models\Employee;
use App\Models\Option;
use App\Models\Participant;
use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MigrateVerifikasiKendaraan extends Command
{
    protected $signature = 'migrate:verivikasi-kendaraan';

    protected $description = 'Migrasi quiz VERIVIKASI KENDARAAN KELUAR dari MongoDB ke PostgreSQL (tanpa hapus data yang ada).';

    // MongoDB ID dari quiz ini (ditemukan saat inspeksi Atlas)
    private string $targetQuizId = '69c8d8f237850a10f30de9a2';

    public function handle(): void
    {
        $this->info('Memulai migrasi quiz VERIVIKASI KENDARAAN KELUAR dari MongoDB → PostgreSQL...');

        // Sambung ke MongoDB
        $mongoConfig = config('database.connections.mongodb');

        try {
            $client  = new \MongoDB\Client($mongoConfig['dsn'] ?? env('MONGODB_URI'));
            $mongoDB = $client->selectDatabase($mongoConfig['database'] ?? env('MONGODB_DATABASE', 'dashboard_quis'));
        } catch (\Exception $e) {
            $this->error('Gagal terhubung ke MongoDB: ' . $e->getMessage());
            return;
        }

        $this->info('✓ Terhubung ke MongoDB.');

        // Cari quiz di MongoDB
        $mongoQuiz = $mongoDB->selectCollection('quizzes')->findOne([
            '_id' => new \MongoDB\BSON\ObjectId($this->targetQuizId),
        ]);

        if (! $mongoQuiz) {
            // Coba cari berdasarkan title jika ID tidak cocok
            $mongoQuiz = $mongoDB->selectCollection('quizzes')->findOne([
                'title' => ['$regex' => 'verivikasi', '$options' => 'i'],
            ]);
        }

        if (! $mongoQuiz) {
            $this->error('Quiz VERIVIKASI KENDARAAN KELUAR tidak ditemukan di MongoDB.');
            return;
        }

        $oldQuizId = (string) $mongoQuiz->_id;
        $this->info("✓ Quiz ditemukan di MongoDB: \"{$mongoQuiz->title}\" (ID: {$oldQuizId})");

        // Cek apakah quiz sudah ada di PostgreSQL
        $existing = Quiz::where('title', $mongoQuiz->title)->first();
        if ($existing) {
            $this->warn("Quiz \"{$mongoQuiz->title}\" sudah ada di PostgreSQL (ID: {$existing->id}). Proses dihentikan.");
            $this->info('Gunakan --force untuk menimpa data yang ada.');
            return;
        }

        // --- 1. Insert Quiz ---
        $newQuiz = Quiz::create([
            'title'         => $mongoQuiz->title ?? 'VERIVIKASI KENDARAAN KELUAR',
            'slug'          => Str::slug($mongoQuiz->title ?? 'verivikasi-kendaraan') . '-' . substr($oldQuizId, -5),
            'time_limit'    => $mongoQuiz->time_limit ?? 15,
            'passing_score' => $mongoQuiz->passing_score ?? 70,
            'created_at'    => $this->parseDate($mongoQuiz->created_at ?? null) ?? now(),
            'updated_at'    => $this->parseDate($mongoQuiz->updated_at ?? null) ?? now(),
        ]);

        $this->info("✓ Quiz berhasil dibuat di PostgreSQL (ID: {$newQuiz->id})");

        // Map ID lama → baru
        $questionMap = [];
        $optionMap   = [];

        // --- 2. Insert Questions ---
        $this->info('Migrasi pertanyaan...');
        $questionCount = 0;

        $questions = $mongoDB->selectCollection('questions')->find([
            'quiz_id' => $oldQuizId,
        ]);

        foreach ($questions as $question) {
            $oldQId = (string) $question->_id;

            $newQuestion = Question::create([
                'quiz_id'    => $newQuiz->id,
                'text'       => $question->text ?? '',
                'created_at' => $this->parseDate($question->created_at ?? null) ?? now(),
                'updated_at' => $this->parseDate($question->updated_at ?? null) ?? now(),
            ]);

            $questionMap[$oldQId] = $newQuestion->id;
            $questionCount++;
        }

        $this->info("✓ {$questionCount} pertanyaan berhasil dipindahkan.");

        // --- 3. Insert Options ---
        $this->info('Migrasi pilihan jawaban...');
        $optionCount = 0;

        foreach (array_keys($questionMap) as $oldQId) {
            $options = $mongoDB->selectCollection('options')->find([
                'question_id' => $oldQId,
            ]);

            foreach ($options as $option) {
                $oldOptId = (string) $option->_id;

                $newOption = Option::create([
                    'question_id' => $questionMap[$oldQId],
                    'text'        => $option->text ?? '',
                    'is_correct'  => $option->is_correct ?? false,
                    'created_at'  => $this->parseDate($option->created_at ?? null) ?? now(),
                    'updated_at'  => $this->parseDate($option->updated_at ?? null) ?? now(),
                ]);

                $optionMap[$oldOptId] = $newOption->id;
                $optionCount++;
            }
        }

        $this->info("✓ {$optionCount} pilihan jawaban berhasil dipindahkan.");

        // --- 4. Insert Participants & Answers ---
        $this->info('Migrasi peserta dan jawaban...');
        $participantCount = 0;
        $answerCount      = 0;

        $participants = $mongoDB->selectCollection('participants')->find([
            'quiz_id' => $oldQuizId,
        ]);

        foreach ($participants as $participant) {
            $oldParticipantId = (string) $participant->_id;
            $employee = Employee::where('nim', $participant->nim ?? '')->first();

            $newParticipant = Participant::create([
                'quiz_id'     => $newQuiz->id,
                'employee_id' => $employee?->id,
                'name'        => $participant->name ?? 'Tidak Diketahui',
                'nim'         => $participant->nim ?? '',
                'score'       => isset($participant->score) ? (int) $participant->score : null,
                'attempt'     => isset($participant->attempt) ? (int) $participant->attempt : 1,
                'created_at'  => $this->parseDate($participant->created_at ?? null) ?? now(),
                'updated_at'  => $this->parseDate($participant->updated_at ?? null) ?? now(),
            ]);

            $participantCount++;

            // Migrasi jawaban peserta ini
            $answers = $mongoDB->selectCollection('answers')->find([
                'participant_id' => $oldParticipantId,
            ]);

            foreach ($answers as $answer) {
                $oldOptId = (string) ($answer->option_id ?? '');
                $oldQId   = (string) ($answer->question_id ?? '');

                if (! isset($questionMap[$oldQId]) || ! isset($optionMap[$oldOptId])) {
                    continue;
                }

                Answer::create([
                    'participant_id' => $newParticipant->id,
                    'question_id'    => $questionMap[$oldQId],
                    'option_id'      => $optionMap[$oldOptId],
                    'created_at'     => $this->parseDate($answer->created_at ?? null) ?? now(),
                    'updated_at'     => $this->parseDate($answer->updated_at ?? null) ?? now(),
                ]);

                $answerCount++;
            }
        }

        $this->info("✓ {$participantCount} peserta berhasil dipindahkan.");
        $this->info("✓ {$answerCount} jawaban berhasil dipindahkan.");

        $this->newLine();
        $this->info('✅ Migrasi selesai! Quiz "' . $newQuiz->title . '" sudah tersedia di Supabase.');
        $this->info("   Jumlah soal    : {$questionCount}");
        $this->info("   Jumlah peserta : {$participantCount}");
        $this->info("   Jumlah jawaban : {$answerCount}");
    }

    private function parseDate(mixed $date): ?string
    {
        if (! $date) {
            return null;
        }

        if ($date instanceof \MongoDB\BSON\UTCDateTime) {
            return $date->toDateTime()->format('Y-m-d H:i:s');
        }

        if (is_string($date)) {
            return date('Y-m-d H:i:s', strtotime($date));
        }

        if ($date instanceof \stdClass && isset($date->{'$date'})) {
            return date('Y-m-d H:i:s', $date->{'$date'} / 1000);
        }

        return null;
    }
}
