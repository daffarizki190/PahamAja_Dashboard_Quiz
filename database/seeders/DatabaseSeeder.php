<?php

namespace Database\Seeders;

use App\Models\Answer;
use App\Models\Employee;
use App\Models\Option;
use App\Models\Participant;
use App\Models\Question;
use App\Models\Quiz;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Run the base seeders for PahamAja
        $this->call([
            EmployeeSeeder::class,
            AchievementSeeder::class,
        ]);

        // 2. Create a Sample Quiz
        $quiz = Quiz::create([
            'title' => 'Pemahaman Standar Pelayanan PahamAja',
            'slug' => 'pemahaman-standar-pelayanan',
            'time_limit' => 30,
            'passing_score' => 70,
        ]);

        // 3. Create Questions & Options
        $questionsData = [
            [
                'text' => 'Apa nilai utama (Core Value) dari PahamAja dalam melayani pelanggan?',
                'options' => [
                    ['text' => 'Kecepatan Tanpa Batas', 'is_correct' => false],
                    ['text' => 'Empati dan Solutif', 'is_correct' => true],
                    ['text' => 'Kekakuan Prosedur', 'is_correct' => false],
                    ['text' => 'Hanya Keuntungan', 'is_correct' => false],
                ],
            ],
            [
                'text' => 'Bagaimana prosedur penanganan complain yang benar di PahamAja?',
                'options' => [
                    ['text' => 'Dengarkan, Catat, dan Berikan Solusi Segera', 'is_correct' => true],
                    ['text' => 'Abaikan jika pelanggan marah', 'is_correct' => false],
                    ['text' => 'Langsung alihkan ke pimpinan tanpa bertanya', 'is_correct' => false],
                    ['text' => 'Minta pelanggan menunggu selamanya', 'is_correct' => false],
                ],
            ],
            [
                'text' => 'Manakah yang merupakan elemen dari "Premium Hospitality" di PahamAja?',
                'options' => [
                    ['text' => 'Menyapa dengan senyum dan menyebut nama', 'is_correct' => true],
                    ['text' => 'Tidak perlu menyapa pelanggan', 'is_correct' => false],
                    ['text' => 'Menggunakan pakaian bebas', 'is_correct' => false],
                    ['text' => 'Datang terlambat ke pertemuan', 'is_correct' => false],
                ],
            ],
            [
                'text' => 'Berapa batas waktu (SLA) maksimal untuk respon pertama terhadap tiket bantuan?',
                'options' => [
                    ['text' => '24 Jam', 'is_correct' => false],
                    ['text' => '1 Jam', 'is_correct' => false],
                    ['text' => '15 Menit', 'is_correct' => true],
                    ['text' => '1 Minggu', 'is_correct' => false],
                ],
            ],
        ];

        $createdQuestions = [];
        foreach ($questionsData as $qData) {
            $question = Question::create([
                'quiz_id' => $quiz->id,
                'text' => $qData['text'],
            ]);
            $createdQuestions[] = $question;

            foreach ($qData['options'] as $optData) {
                Option::create([
                    'question_id' => $question->id,
                    'text' => $optData['text'],
                    'is_correct' => $optData['is_correct'],
                ]);
            }
        }

        // 4. Create Participants based on the REAL employees
        $employees = Employee::all();

        foreach ($employees as $employee) {
            // Randomly determine if participant has finished the quiz (80% chance)
            $isFinished = rand(1, 100) <= 80;

            $participant = Participant::create([
                'quiz_id' => $quiz->id,
                'employee_id' => $employee->id,
                'name' => $employee->name,
                'nim' => $employee->nim,
                'score' => null, // Intentionally null for unfinished
            ]);

            if ($isFinished) {
                $score = 0;
                foreach ($createdQuestions as $question) {
                    $options = $question->options;

                    // Random probability of getting the answer right (e.g., 85% chance)
                    $isCorrectGuess = rand(1, 100) <= 85;

                    if ($isCorrectGuess) {
                        $selectedOption = $options->where('is_correct', true)->first();
                    } else {
                        $selectedOption = $options->where('is_correct', false)->random();
                    }

                    Answer::create([
                        'participant_id' => $participant->id,
                        'question_id' => $question->id,
                        'option_id' => $selectedOption->id,
                    ]);

                    if ($selectedOption->is_correct) {
                        $score++;
                    }
                }

                // Update final score based on correct answers
                $totalQuestions = count($createdQuestions);
                $participant->update([
                    'score' => round(($score / $totalQuestions) * 100),
                ]);
            }
        }
    }
}
