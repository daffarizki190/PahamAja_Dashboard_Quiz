<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\Option;

echo "Starting Emergency Data Restoration from MongoDB to PostgreSQL...\n";

try {
    $mongoQuizzes = DB::connection('mongodb')->table('quizzes')->get();
    echo "Found " . count($mongoQuizzes) . " quizzes in MongoDB.\n";

    DB::connection('pgsql')->beginTransaction();

    foreach ($mongoQuizzes as $mQuiz) {
        $title = $mQuiz->title ?? ($mQuiz->name ?? 'Untitled');
        $slug = $mQuiz->slug ?? \Illuminate\Support\Str::slug($title);
        $mId = (string) $mQuiz->id;
        
        echo "Restoring Quiz: $title...\n";

        // Create Quiz in PGSQL
        $quiz = Quiz::updateOrCreate(
            ['slug' => $slug],
            [
                'title' => $title,
                'time_limit' => $mQuiz->time_limit ?? 60,
                'passing_score' => $mQuiz->passing_score ?? 70,
            ]
        );

        // Try to find questions for this quiz in MongoDB
        $mQuestions = [];
        if (isset($mQuiz->questions) && is_array($mQuiz->questions)) {
            $mQuestions = $mQuiz->questions;
        } else {
            // Check questions collection
            $mQuestions = DB::connection('mongodb')->table('questions')
                ->where('quiz_id', $mId)
                ->get();
        }

        foreach ($mQuestions as $mQuestion) {
            $qText = $mQuestion->text ?? ($mQuestion->question ?? ($mQuestion->question_text ?? 'Untitled Question'));
            $mQId = (string) ($mQuestion->id ?? '');

            echo "  - Restoring Question: " . substr($qText, 0, 50) . "...\n";

            $question = Question::create([
                'quiz_id' => $quiz->id,
                'text' => $qText,
            ]);

            // Restore Options
            $mOptions = [];
            if (isset($mQuestion->options) && is_array($mQuestion->options)) {
                $mOptions = $mQuestion->options;
            } else {
                // Check options collection
                $mOptions = DB::connection('mongodb')->table('options')
                    ->where('question_id', $mQId)
                    ->get();
            }

            foreach ($mOptions as $mOption) {
                // Determine if correct
                $isCorrect = false;
                if (isset($mOption->is_correct)) {
                    $isCorrect = (bool) $mOption->is_correct;
                } elseif (isset($mOption->correct)) {
                    $isCorrect = (bool) $mOption->correct;
                }

                Option::create([
                    'question_id' => $question->id,
                    'text' => $mOption->text ?? ($mOption->option ?? ($mOption->option_text ?? 'Option')),
                    'is_correct' => $isCorrect ? 1 : 0,
                ]);
            }
        }
    }

    DB::connection('pgsql')->commit();
    echo "\nSuccess! Data restoration complete.\n";

} catch (\Exception $e) {
    DB::connection('pgsql')->rollBack();
    echo "\nError during restoration: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
