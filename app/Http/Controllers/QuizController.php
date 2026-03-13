<?php

namespace App\Http\Controllers;

use App\Exports\QuizExport;
use App\Models\Participant;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class QuizController extends Controller
{
    /**
     * Export quiz results to Excel.
     */
    public function exportExcel(Quiz $quiz)
    {
        return Excel::download(new QuizExport($quiz->id, $quiz->passing_score), "Hasil-Kuis-{$quiz->slug}.xlsx");
    }

    /**
     * Show the form for a participant to join the quiz (enter Name and NIM).
     */
    public function showJoinForm(Quiz $quiz)
    {
        return view('quiz.join', compact('quiz'));
    }

    /**
     * Register the participant and redirect to the quiz taking page.
     */
    public function joinQuiz(Request $request, Quiz $quiz, \App\Services\NameMatchingService $matchingService)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nim' => 'required|string|max:50',
        ]);

        // "AI" Name & NIM Integrity Check
        $employee = $matchingService->matchParticipant($request->name, $request->nim);

        // Check if participant already exists for this quiz based on NIM or EmployeeID
        $participant = Participant::where('quiz_id', $quiz->id)
            ->where(function($query) use ($request, $employee) {
                $query->where('nim', $request->nim);
                if ($employee) {
                    $query->orWhere('employee_id', $employee->id);
                }
            })
            ->first();

        if (!$participant) {
            $participant = Participant::create([
                'quiz_id' => $quiz->id,
                'employee_id' => $employee?->id,
                'name' => $employee ? $employee->name : $request->name, // Use registered name if matched
                'nim' => $employee ? $employee->nim : $request->nim,   // Use registered NIM if matched
            ]);
        }

        // If they already have a score, they've finished.
        if (! is_null($participant->score)) {
            return redirect()->route('quiz.result', ['quiz' => $quiz->slug, 'participant' => $participant->id])
                ->with('error', 'You have already completed this quiz.');
        }

        return redirect()->route('quiz.take', ['quiz' => $quiz->slug, 'participant' => $participant->id]);
    }

    /**
     * Show the quiz questions to the participant.
     */
    public function takeQuiz(Quiz $quiz, Participant $participant)
    {
        // Ensure the participant belongs to this quiz
        if ($participant->quiz_id !== $quiz->id) {
            abort(403, 'Unauthorized access to this quiz.');
        }

        // If they already have a score, they've finished.
        if (! is_null($participant->score)) {
            return redirect()->route('quiz.result', ['quiz' => $quiz->slug, 'participant' => $participant->id]);
        }

        // Eager load questions and options to minimize distinct queries
        $quiz->load('questions.options');

        return view('quiz.take', compact('quiz', 'participant'));
    }

    /**
     * Store answers submitted by a participant and calculate the score automatically.
     */
    public function storeAnswer(Request $request, Quiz $quiz, Participant $participant)
    {
        $request->validate([
            'answers' => 'required|array',
        ]);

        if ($participant->quiz_id !== $quiz->id) {
            abort(403);
        }

        if (! is_null($participant->score)) {
            return redirect()->route('quiz.result', ['quiz' => $quiz->slug, 'participant' => $participant->id]);
        }

        $correctCount = 0;
        $quiz->load('questions.options');
        $questions = $quiz->questions;
        $totalQuestions = $questions->count();
        $questionsById = $questions->keyBy('id');

        $optionsById = $questions
            ->pluck('options')
            ->flatten()
            ->keyBy('id');

        foreach ($request->answers as $question_id => $option_id) {
            $question = $questionsById->get($question_id);
            if (! $question) {
                abort(422, 'Jawaban tidak valid.');
            }

            $option = $optionsById->get($option_id);
            if (! $option || $option->question_id !== $question->id) {
                abort(422, 'Jawaban tidak valid.');
            }

            if ($option->is_correct) {
                $correctCount++;
            }

            // Record answer for audit
            $participant->answers()->updateOrCreate(
                ['question_id' => $question_id],
                ['option_id' => $option_id]
            );
        }

        // Final score on scale 0-100
        $finalScore = $totalQuestions > 0 ? round(($correctCount / $totalQuestions) * 100) : 0;

        $participant->update(['score' => $finalScore]);

        return redirect()->route('quiz.result', ['quiz' => $quiz->slug, 'participant' => $participant->id])
            ->with('success', 'Quiz submitted successfully!');
    }

    /**
     * Log cheat attempts (tab switching) via AJAX.
     */
    public function logCheatAttempt(Request $request, Quiz $quiz, Participant $participant)
    {
        if ($participant->quiz_id === $quiz->id) {
            $participant->increment('cheat_attempts');

            return response()->json(['status' => 'success', 'attempts' => $participant->cheat_attempts]);
        }

        return response()->json(['status' => 'error'], 403);
    }

    /**
     * Show the results page.
     */
    public function showResult(Quiz $quiz, Participant $participant)
    {
        if ($participant->quiz_id !== $quiz->id) {
            abort(403);
        }

        return view('quiz.result', compact('quiz', 'participant'));
    }

    /**
     * Display the Admin Dashboard with Advanced Stats.
     */
    public function showDashboard(Quiz $quiz)
    {
        $participants = $quiz->participants()->get();
        $participants = $participants
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

        return view('admin.dashboard', compact('quiz', 'participants', 'chartData', 'avgScore', 'inProgressCount'));
    }
}
