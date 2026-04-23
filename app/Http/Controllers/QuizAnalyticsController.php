<?php

namespace App\Http\Controllers;

use App\Exports\QuizExport;
use App\Models\Answer;
use App\Models\Employee;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class QuizAnalyticsController extends Controller
{
    /**
     * Export quiz results to Excel.
     */
    public function exportExcel(Quiz $quiz)
    {
        return Excel::download(new QuizExport($quiz), "Hasil-Kuis-{$quiz->slug}.xlsx");
    }

    /**
     * Display the Admin Dashboard with Advanced Stats.
     */
    public function showDashboard(Quiz $quiz)
    {
        // 🟢 PRE-CONSOLIDATION LOGIC: 
        // We track all attempts, but for the Leaderboard/Report, we only take the HIGHEST score per employee.
        $allParticipants = $quiz->participants()->with('employee')->get();
        
        $participants = $allParticipants
            ->groupBy('employee_id')
            ->map(function ($group) {
                // Find the best attempt (Highest score)
                // If scores are equal, take the most recent one
                return $group->sort(function ($a, $b) {
                    if ($a->score !== $b->score) return $b->score <=> $a->score;
                    return $b->updated_at <=> $a->updated_at;
                })->first();
            })
            ->sort(function ($a, $b) {
                $aScoreIsNull = is_null($a->score);
                $bScoreIsNull = is_null($b->score);

                if ($aScoreIsNull !== $bScoreIsNull) {
                    return $aScoreIsNull <=> $bScoreIsNull;
                }

                if (! $aScoreIsNull && ! $bScoreIsNull) {
                    if ($a->score !== $b->score) {
                        return $b->score <=> $a->score;
                    }
                }

                $aUpdatedAt = $a->updated_at?->getTimestamp() ?? 0;
                $bUpdatedAt = $b->updated_at?->getTimestamp() ?? 0;

                return $aUpdatedAt <=> $bUpdatedAt;
            })
            ->values();

        // Advanced Stats Logic
        $finishedParticipants = $participants->whereNotNull('score');
        $avgScore = $finishedParticipants->avg('score') ?? 0;
        $inProgressCount = $participants->whereNull('score')->count();

        // Score Distribution for Chart.js
        $dist = [
            'low' => $finishedParticipants->whereBetween('score', [0, 50])->count(),
            'mid' => $finishedParticipants->whereBetween('score', [51, 75])->count(),
            'high' => $finishedParticipants->whereBetween('score', [76, 100])->count(),
        ];

        $chartData = [
            'labels' => ['0-50 (Low)', '51-75 (Mid)', '76-100 (High)'],
            'scores' => [$dist['low'], $dist['mid'], $dist['high']],
        ];

        $quiz->load('questions.options');
        $finishedIds = $finishedParticipants->pluck('id')->all();
        $answers = count($finishedIds) > 0
            ? Answer::whereIn('participant_id', $finishedIds)->get()
            : collect();

        $answersByQuestion = $answers->groupBy('question_id');

        $questionAnalytics = $quiz->questions->map(function ($question) use ($finishedParticipants, $answersByQuestion) {
            $correctOption = $question->options->firstWhere('is_correct', true);
            $questionAnswers = $answersByQuestion->get($question->id, collect());

            $answeredCount = $questionAnswers->count();
            $correctCount = $correctOption
                ? $questionAnswers->where('option_id', $correctOption->id)->count()
                : 0;

            $distribution = $questionAnswers->countBy('option_id');
            $topOptionId = $distribution->sortDesc()->keys()->first();
            $topOptionText = $topOptionId
                ? (string) optional($question->options->firstWhere('id', $topOptionId))->text
                : null;

            return [
                'id' => (string) $question->id,
                'text' => (string) $question->text,
                'answered' => $answeredCount,
                'participants' => $finishedParticipants->count(),
                'correct' => $correctCount,
                'correct_rate' => $answeredCount > 0 ? round(($correctCount / $answeredCount) * 100, 1) : null,
                'top_option' => $topOptionText,
            ];
        })->values();

        if (request()->wantsJson()) {
            return response()->json([
                'avgScore' => number_format($avgScore, 1),
                'inProgressCount' => $inProgressCount,
                'completedCount' => $participants->whereNotNull('score')->count(),
                'liveActivity' => $participants->count(),
                'participants' => $participants->map(function ($p) use ($quiz) {
                    $duration = ($p->started_at && $p->finished_at) 
                        ? $p->finished_at->diff($p->started_at)->format('%im %ss') 
                        : null;
                        
                    return [
                        'id' => $p->id,
                        'name' => $p->name,
                        'nim' => $p->nim,
                        'score' => $p->score,
                        'is_passing' => $p->score >= $quiz->passing_score,
                        'session' => $p->quizSession ? $p->quizSession->name : '-',
                        'duration' => $duration,
                    ];
                })->toArray(),
            ]);
        }

        $unparticipatedEmployees = Employee::where('status', 'Active')
            ->whereNotIn('id', $participants->pluck('employee_id')->filter())
            ->get();

        return view('admin.dashboard', compact('quiz', 'participants', 'chartData', 'avgScore', 'inProgressCount', 'questionAnalytics', 'unparticipatedEmployees'));
    }
}
