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
            'title' => 'Ujian Akhir Semester Web Development',
            'slug' => Str::slug('Ujian Akhir Semester Web Development'),
            'time_limit' => 60,
        ]);

        // 3. Create Questions & Options
        $questionsData = [
            [
                'text' => 'Apa kepanjangan dari HTML?',
                'options' => [
                    ['text' => 'Hyper Text Markup Language', 'is_correct' => true],
                    ['text' => 'Hyperlinks and Text Markup Language', 'is_correct' => false],
                    ['text' => 'Home Tool Markup Language', 'is_correct' => false],
                ],
            ],
            [
                'text' => 'Framework CSS yang menggunakan utility-first approach adalah?',
                'options' => [
                    ['text' => 'Bootstrap', 'is_correct' => false],
                    ['text' => 'Tailwind CSS', 'is_correct' => true],
                    ['text' => 'Bulma', 'is_correct' => false],
                ],
            ],
            [
                'text' => 'Di mana letak file routing utama (web) pada Laravel 11?',
                'options' => [
                    ['text' => 'app/routes.php', 'is_correct' => false],
                    ['text' => 'routes/web.php', 'is_correct' => true],
                    ['text' => 'config/routes.php', 'is_correct' => false],
                ],
            ],
            [
                'text' => 'Library PHP yang populer untuk integrasi QR Code di Laravel adalah?',
                'options' => [
                    ['text' => 'simplesoftwareio/simple-qrcode', 'is_correct' => true],
                    ['text' => 'bacon/bacon-qr-code', 'is_correct' => false],
                    ['text' => 'chillerlan/php-qrcode', 'is_correct' => false],
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
