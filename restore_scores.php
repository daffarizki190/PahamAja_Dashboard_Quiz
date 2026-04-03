<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

$app->make(Kernel::class)->bootstrap();

use App\Models\Answer;
use App\Models\Employee;
use App\Models\Option;
use App\Models\Participant;
use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;

echo "Starting Emergency Data Restoration (Phase 2): Scores & Participants...\n";

try {
    DB::connection('pgsql')->beginTransaction();

    // 1. Restore Employees
    echo "Restoring Employees...\n";
    $mongoEmployees = DB::connection('mongodb')->table('employees')->get();
    foreach ($mongoEmployees as $mEmp) {
        Employee::updateOrCreate(
            ['nim' => $mEmp->nim],
            [
                'name' => $mEmp->name,
                'department' => $mEmp->department ?? 'General',
                'position' => $mEmp->position ?? 'Staff',
                'status' => $mEmp->status ?? 'Active',
            ]
        );
    }
    echo 'Restored '.count($mongoEmployees)." employees.\n";

    // 2. Prepare Mappings (Mongo ID -> PGSQL ID)
    echo "Building ID Mappings...\n";
    $quizMap = []; // Mongo Quiz ID -> PGSQL Quiz ID
    $questionMap = []; // Mongo Question ID -> PGSQL Question ID
    $optionMap = []; // Mongo Option ID -> PGSQL Option ID
    $participantMap = []; // Mongo Participant ID -> PGSQL Participant ID

    $mongoQuizzes = DB::connection('mongodb')->table('quizzes')->get();
    foreach ($mongoQuizzes as $mQuiz) {
        $title = $mQuiz->title ?? ($mQuiz->name ?? '');
        $pgQuiz = Quiz::where('title', $title)->first();
        if ($pgQuiz) {
            $quizMap[(string) $mQuiz->id] = $pgQuiz->id;

            // Map Questions & Options for this Quiz
            $mQuestions = DB::connection('mongodb')->table('questions')->where('quiz_id', (string) $mQuiz->id)->get();
            foreach ($mQuestions as $mQ) {
                $qText = $mQ->text ?? ($mQ->question ?? ($mQ->question_text ?? ''));
                $pgQ = Question::where('quiz_id', $pgQuiz->id)->where('text', $qText)->first();
                if ($pgQ) {
                    $questionMap[(string) $mQ->id] = $pgQ->id;

                    $mOptions = DB::connection('mongodb')->table('options')->where('question_id', (string) $mQ->id)->get();
                    foreach ($mOptions as $mO) {
                        $oText = $mO->text ?? ($mO->option ?? ($mO->option_text ?? ''));
                        $pgO = Option::where('question_id', $pgQ->id)->where('text', $oText)->first();
                        if ($pgO) {
                            $optionMap[(string) $mO->id] = $pgO->id;
                        }
                    }
                }
            }
        }
    }

    // 3. Restore Participants
    echo "Restoring Participants...\n";
    $mongoParticipants = DB::connection('mongodb')->table('participants')->get();
    foreach ($mongoParticipants as $mPart) {
        $pgQuizId = $quizMap[(string) $mPart->quiz_id] ?? null;
        if (! $pgQuizId) {
            continue;
        }

        $pgEmpId = null;
        if ($mPart->nim) {
            $emp = Employee::where('nim', $mPart->nim)->first();
            $pgEmpId = $emp ? $emp->id : null;
        }

        $participant = Participant::updateOrCreate(
            [
                'quiz_id' => $pgQuizId,
                'nim' => $mPart->nim,
                'attempt' => $mPart->attempt ?? 1,
            ],
            [
                'employee_id' => $pgEmpId,
                'name' => $mPart->name,
                'score' => $mPart->score ?? 0,
            ]
        );
        $participantMap[(string) $mPart->id] = $participant->id;
    }
    echo 'Restored '.count($participantMap)." participants.\n";

    // 4. Restore Answers
    echo "Restoring Answers...\n";
    $mongoAnswers = DB::connection('mongodb')->table('answers')->get();
    $aCount = 0;
    foreach ($mongoAnswers as $mAns) {
        $pgPartId = $participantMap[(string) $mAns->participant_id] ?? null;
        $pgQId = $questionMap[(string) $mAns->question_id] ?? null;
        $pgOId = $optionMap[(string) $mAns->option_id] ?? null;

        if ($pgPartId && $pgQId && $pgOId) {
            Answer::updateOrCreate(
                [
                    'participant_id' => $pgPartId,
                    'question_id' => $pgQId,
                ],
                [
                    'option_id' => $pgOId,
                ]
            );
            $aCount++;
        }
    }
    echo "Restored $aCount answers.\n";

    DB::connection('pgsql')->commit();
    echo "\nSuccess! Score restoration complete.\n";

} catch (Exception $e) {
    DB::connection('pgsql')->rollBack();
    echo "\nError during restoration: ".$e->getMessage()."\n";
    echo $e->getTraceAsString()."\n";
}
