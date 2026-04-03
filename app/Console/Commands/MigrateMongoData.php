<?php

namespace App\Console\Commands;

use App\Models\Answer;
use App\Models\Employee;
use App\Models\Option;
use App\Models\Participant;
use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Client;

class MigrateMongoData extends Command
{
    protected $signature = 'migrate:mongo';

    protected $description = 'Migrasikan data dari MongoDB ke PostgreSQL menggunakan raw MongoDB PHP client.';

    public function handle(): void
    {
        $this->info('Memulai migrasi data MongoDB → PostgreSQL...');

        $mongoConfig = config('database.connections.mongodb');

        if (! $mongoConfig) {
            $this->error('Konfigurasi koneksi MongoDB tidak ditemukan.');

            return;
        }

        try {
            $client = new Client($mongoConfig['dsn'] ?? env('MONGODB_URI'));
            $mongoDB = $client->selectDatabase($mongoConfig['database'] ?? env('MONGODB_DATABASE', 'dashboard_quis'));
        } catch (\Exception $e) {
            $this->error('Gagal terhubung ke MongoDB: '.$e->getMessage());

            return;
        }

        $this->warn('Menghapus semua data quiz di PostgreSQL sebelum migrasi...');
        DB::statement('TRUNCATE TABLE answers, options, questions, participants, quizzes CASCADE');
        $this->info('PostgreSQL sudah bersih.');

        // Map dari MongoDB ObjectID ke PostgreSQL integer ID
        $quizMap = [];
        $questionMap = [];
        $optionMap = [];
        $participantMap = [];

        // --- 1. Kuis ---
        $this->info('Migrasi kuis...');
        $quizCount = 0;

        foreach ($mongoDB->selectCollection('quizzes')->find() as $quiz) {
            $oldId = (string) $quiz->_id;

            $newQuiz = Quiz::create([
                'title' => $quiz->title ?? 'Tanpa Judul',
                'slug' => Str::slug($quiz->title ?? 'quiz').'-'.substr($oldId, -5),
                'time_limit' => $quiz->time_limit ?? 60,
                'passing_score' => $quiz->passing_score ?? 70,
                'created_at' => $this->parseDate($quiz->created_at ?? null) ?? now(),
                'updated_at' => $this->parseDate($quiz->updated_at ?? null) ?? now(),
            ]);

            $quizMap[$oldId] = $newQuiz->id;
            $quizCount++;
        }

        $this->info("✓ {$quizCount} kuis berhasil dipindahkan.");

        // --- 2. Pertanyaan ---
        $this->info('Migrasi pertanyaan...');
        $questionCount = 0;

        foreach ($mongoDB->selectCollection('questions')->find() as $question) {
            $oldId = (string) $question->_id;
            $oldQuizId = (string) ($question->quiz_id ?? '');

            if (! isset($quizMap[$oldQuizId])) {
                continue;
            }

            $newQuestion = Question::create([
                'quiz_id' => $quizMap[$oldQuizId],
                'text' => $question->text ?? '',
                'created_at' => $this->parseDate($question->created_at ?? null) ?? now(),
                'updated_at' => $this->parseDate($question->updated_at ?? null) ?? now(),
            ]);

            $questionMap[$oldId] = $newQuestion->id;
            $questionCount++;
        }

        $this->info("✓ {$questionCount} pertanyaan berhasil dipindahkan.");

        // --- 3. Pilihan Jawaban ---
        $this->info('Migrasi pilihan jawaban...');
        $optionCount = 0;

        foreach ($mongoDB->selectCollection('options')->find() as $option) {
            $oldId = (string) $option->_id;
            $oldQuestionId = (string) ($option->question_id ?? '');

            if (! isset($questionMap[$oldQuestionId])) {
                continue;
            }

            $newOption = Option::create([
                'question_id' => $questionMap[$oldQuestionId],
                'text' => $option->text ?? '',
                'is_correct' => $option->is_correct ?? false,
                'created_at' => $this->parseDate($option->created_at ?? null) ?? now(),
                'updated_at' => $this->parseDate($option->updated_at ?? null) ?? now(),
            ]);

            $optionMap[$oldId] = $newOption->id;
            $optionCount++;
        }

        $this->info("✓ {$optionCount} pilihan jawaban berhasil dipindahkan.");

        // --- 4. Peserta ---
        $this->info('Migrasi peserta...');
        $participantCount = 0;
        $skippedCount = 0;

        foreach ($mongoDB->selectCollection('participants')->find() as $participant) {
            $oldId = (string) $participant->_id;
            $oldQuizId = (string) ($participant->quiz_id ?? '');

            if (! isset($quizMap[$oldQuizId])) {
                $skippedCount++;

                continue;
            }

            $employee = Employee::where('nim', $participant->nim ?? '')->first();

            $newParticipant = Participant::create([
                'quiz_id' => $quizMap[$oldQuizId],
                'employee_id' => $employee?->id,
                'name' => $participant->name ?? 'Tidak Diketahui',
                'nim' => $participant->nim ?? '',
                'score' => isset($participant->score) ? (int) $participant->score : null,
                'attempt' => isset($participant->attempt) ? (int) $participant->attempt : 1,
                'created_at' => $this->parseDate($participant->created_at ?? null) ?? now(),
                'updated_at' => $this->parseDate($participant->updated_at ?? null) ?? now(),
            ]);

            $participantMap[$oldId] = $newParticipant->id;
            $participantCount++;
        }

        $this->info("✓ {$participantCount} peserta berhasil dipindahkan. ({$skippedCount} dilewati karena kuis tidak ditemukan)");

        // --- 5. Jawaban ---
        $this->info('Migrasi jawaban...');
        $answerCount = 0;

        foreach ($mongoDB->selectCollection('answers')->find() as $answer) {
            $oldParticipantId = (string) ($answer->participant_id ?? '');
            $oldQuestionId = (string) ($answer->question_id ?? '');
            $oldOptionId = (string) ($answer->option_id ?? '');

            if (
                ! isset($participantMap[$oldParticipantId]) ||
                ! isset($questionMap[$oldQuestionId]) ||
                ! isset($optionMap[$oldOptionId])
            ) {
                continue;
            }

            Answer::create([
                'participant_id' => $participantMap[$oldParticipantId],
                'question_id' => $questionMap[$oldQuestionId],
                'option_id' => $optionMap[$oldOptionId],
                'created_at' => $this->parseDate($answer->created_at ?? null) ?? now(),
                'updated_at' => $this->parseDate($answer->updated_at ?? null) ?? now(),
            ]);

            $answerCount++;
        }

        $this->info("✓ {$answerCount} jawaban berhasil dipindahkan.");

        $this->newLine();
        $this->info('✅ Migrasi selesai!');
    }

    /**
     * Mengkonversi berbagai format tanggal MongoDB ke string 'Y-m-d H:i:s'.
     */
    private function parseDate(mixed $date): ?string
    {
        if (! $date) {
            return null;
        }

        if ($date instanceof UTCDateTime) {
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
