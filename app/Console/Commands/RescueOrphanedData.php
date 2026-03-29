<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RescueOrphanedData extends Command
{
    protected $signature = 'rescue:orphans';
    protected $description = 'Rescue orphaned participants from MongoDB to a CSV file';

    public function handle()
    {
        $this->info("Menyuntikkan data partisipan lama ke dalam Dashboard Web...");

        try {
            $mongo = DB::connection('mongodb');
            
            // Get all valid quizzes that exist in MongoDB
            $existingQuizzes = clone $mongo->table('quizzes')->get();
            $validQuizIds = [];
            foreach ($existingQuizzes as $q) {
                $q = (array) $q;
                $validQuizIds[] = (string) ($q['_id'] ?? '');
            }

            // Create or get the Archive Quiz in Postgres
            $archiveQuiz = \App\Models\Quiz::firstOrCreate(
                ['slug' => 'arsip-kuis-dihapus'],
                [
                    'title' => '📦 ARSIP: Data Kuis Terhapus',
                    'time_limit' => 60,
                    'passing_score' => 0,
                ]
            );

            // Bersihkan data lama jika script ini dijalankan ulang
            \App\Models\Participant::where('quiz_id', $archiveQuiz->id)->delete();

            $orphans = [];
            $participants = $mongo->table('participants')->get();

            foreach ($participants as $part) {
                $part = (array) $part;
                $quizId = (string) ($part['quiz_id'] ?? '');

                // If quiz doesn't exist anymore, this is an orphaned participant
                if (!in_array($quizId, $validQuizIds)) {
                    $scoreRaw = $part['score'] ?? null;
                    $score = is_numeric($scoreRaw) ? (int)$scoreRaw : null;
                    
                    \App\Models\Participant::create([
                        'quiz_id' => $archiveQuiz->id,
                        'name' => (string) ($part['name'] ?? 'Unknown') . ' (Eks ID: ' . substr($quizId, 0, 4) . ')',
                        'nim' => (string) ($part['nim'] ?? 'Unknown'),
                        'score' => $score,
                        'created_at' => $this->parseMongoDate($part['created_at'] ?? null),
                        'updated_at' => $this->parseMongoDate($part['updated_at'] ?? null),
                    ]);
                    $orphans[] = $part;
                }
            }

            if (count($orphans) === 0) {
                $this->info("Tidak ada data nilai yang terbuang.");
                return;
            }

            $this->info("\nBERHASIL! " . count($orphans) . " data berhantu telah dimasukkan ke kotak 'ARSIP' di Dashboard Web.");

        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
        }
    }

    private function parseMongoDate($date)
    {
        if (!$date) return 'N/A';
        if ($date instanceof \MongoDB\BSON\UTCDateTime) {
            return $date->toDateTime()->setTimezone(new \DateTimeZone('Asia/Jakarta'))->format('Y-m-d H:i:s');
        }
        if (is_string($date)) {
            return date('Y-m-d H:i:s', strtotime($date));
        }
        return 'N/A';
    }
}
