<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Participant;
use App\Models\Quiz;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HistoryRestorationSeeder extends Seeder
{
    /**
     * Run the database seeds (SAFE TOTAL RESTORATION with UNIQUE SLUGS).
     */
    public function run(): void
    {
        $csvFile = storage_path('app/public/Nilai_Kuis_Dihapus.csv');
        if (!file_exists($csvFile)) {
            $this->command->error("CSV file not found at $csvFile");
            return;
        }

        $handle = fopen($csvFile, 'r');
        $header = fgetcsv($handle); // Skip header

        // Dictionary to track attempts per employee per quiz to calculate effort
        $attempts = [];

        DB::beginTransaction();
        try {
            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) < 5) continue;

                $name = trim($row[0]);
                $nim = trim($row[1]);
                $scoreValue = trim($row[2]);
                $attemptDate = trim($row[3]);
                $oldQuizId = trim($row[4]);

                // 1. Identify Target Quiz (Match the UNIQUE Professional Slugs)
                $targetSlug = match($oldQuizId) {
                    '69bba7910e362145ea008862' => 'sop-penanganan-masalah-dan-insiden-profesional',
                    '69bcda97d57f54079e05db02' => 'penanganan-keluhan-pelanggan-profesional',
                    default => 'historical-' . $oldQuizId
                };

                $quiz = Quiz::where('slug', $targetSlug)->first();

                // If it's a historical quiz or professional and doesn't exist yet, create the shell
                if (!$quiz) {
                    $quiz = Quiz::updateOrCreate(
                        ['slug' => $targetSlug],
                        [
                            'title' => str_starts_with($targetSlug, 'historical-') 
                                ? 'Historical Assessment (' . strtoupper(substr($oldQuizId, 0, 8)) . ')'
                                : 'Professional Assessment Restored',
                            'time_limit' => 60,
                            'passing_score' => 70,
                        ]
                    );
                }

                // 2. Find matching employee (Strict NIK matching)
                $employee = Employee::where('nim', $nim)->first();
                if (!$employee) {
                    $employee = Employee::where('name', 'ILIKE', $name)->first();
                }

                if (!$employee) {
                    continue; 
                }

                // 3. Determine attempt number dynamically
                $key = $employee->id . '-' . $quiz->id;
                $attempts[$key] = ($attempts[$key] ?? 0) + 1;

                // 4. Create or Update Participant record
                try {
                    Participant::updateOrCreate(
                        [
                            'quiz_id' => $quiz->id,
                            'employee_id' => $employee->id,
                            'attempt' => $attempts[$key]
                        ],
                        [
                            'name' => $employee->name,
                            'nim' => $employee->nim,
                            'score' => is_numeric($scoreValue) ? (int)$scoreValue : null,
                            'created_at' => ($attemptDate !== 'N/A' && !empty($attemptDate)) ? date('Y-m-d H:i:s', strtotime($attemptDate)) : now(),
                        ]
                    );
                } catch (\Exception $e) {
                    // Skip individual record failures to ensure the rest are restored
                    continue;
                }
            }
            DB::commit();
            $this->command->info('Historical scores restored safely to professional containers.');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Restoration aborted: ' . $e->getMessage());
        }

        fclose($handle);
    }
}
