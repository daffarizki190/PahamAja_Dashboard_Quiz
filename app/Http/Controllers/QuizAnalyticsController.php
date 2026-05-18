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
        if (session()->isStarted()) session()->save();
        
        $filename = "Laporan-Kuis-" . \Illuminate\Support\Str::slug($quiz->title) . "-" . now()->format('d-M-Y') . ".xlsx";
        
        return Excel::download(new QuizExport($quiz), $filename);
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
                // Find the best attempt (Highest score + Fastest time)
                return $group->sort(function ($a, $b) {
                    // 1. Higher score wins
                    if ($a->score !== $b->score) return $b->score <=> $a->score;

                    // 2. Faster completion wins (if both have scores)
                    if ($a->score !== null && $b->score !== null) {
                        $aDur = ($a->finished_at && $a->started_at) ? $a->finished_at->diffInSeconds($a->started_at) : 999999;
                        $bDur = ($b->finished_at && $b->started_at) ? $b->finished_at->diffInSeconds($b->started_at) : 999999;
                        if ($aDur !== $bDur) return $aDur <=> $bDur;
                    }

                    // 3. Most recent first
                    return $b->updated_at <=> $a->updated_at;
                })->first();
            })
            ->sort(function ($a, $b) {
                $aScoreIsNull = is_null($a->score);
                $bScoreIsNull = is_null($b->score);

                // In-progress at the bottom
                if ($aScoreIsNull !== $bScoreIsNull) {
                    return $aScoreIsNull <=> $bScoreIsNull;
                }

                if (! $aScoreIsNull && ! $bScoreIsNull) {
                    // 1. Higher score wins
                    if ($a->score !== $b->score) {
                        return $b->score <=> $a->score;
                    }

                    // 2. Faster completion wins
                    $aDur = ($a->finished_at && $a->started_at) ? $a->finished_at->diffInSeconds($a->started_at) : 999999;
                    $bDur = ($b->finished_at && $b->started_at) ? $b->finished_at->diffInSeconds($b->started_at) : 999999;
                    if ($aDur !== $bDur) return $aDur <=> $bDur;
                }

                // 3. Most recent first
                return $b->updated_at <=> $a->updated_at;
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
                    $duration = $p->duration;
                        
                    return [
                        'id' => $p->id,
                        'name' => $p->name,
                        'nim' => $p->nim,
                        'score' => $p->score,
                        'is_passing' => $p->score >= $quiz->passing_score,
                        'session' => $p->quizSession ? $p->quizSession->name : '-',
                        'duration' => $duration,
                        'location' => $p->location,
                    ];
                })->toArray(),
            ]);
        }

        $unparticipatedEmployees = Employee::where('status', 'Active')
            ->whereNotIn('id', $participants->pluck('employee_id')->filter())
            ->get();

        // Location-based stats for public quizzes
        $locationStats = collect();
        if ($quiz->is_public) {
            $locationStats = $participants
                ->whereNotNull('location')
                ->groupBy('location')
                ->map(function ($group) {
                    return [
                        'count' => $group->count(),
                        'avg_score' => round($group->avg('score'), 1),
                    ];
                })
                ->sortByDesc('count');
        }

        return view('admin.dashboard', compact(
            'quiz', 'participants', 'chartData', 'avgScore', 
            'inProgressCount', 'questionAnalytics', 'unparticipatedEmployees',
            'locationStats'
        ));
    }
}
