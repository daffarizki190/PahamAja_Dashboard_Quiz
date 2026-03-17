<?php

namespace App\Http\Controllers;

use App\Exports\QuizExport;
use App\Models\Answer;
use App\Models\Employee;
use App\Models\Participant;
use App\Models\Quiz;
use App\Services\NameMatchingService;
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
        $sessionKey = "quiz_in_progress.{$quiz->id}";
        $participantId = session($sessionKey);

        if ($participantId) {
            $participant = Participant::where('id', $participantId)
                ->where('quiz_id', $quiz->id)
                ->first();

            if ($participant && is_null($participant->score)) {
                return redirect()->route('quiz.take', ['quiz' => $quiz->slug, 'participant' => $participant->id]);
            }

            session()->forget($sessionKey);
        }

        return view('quiz.join', compact('quiz'));
    }

    /**
     * Register the participant and redirect to the quiz taking page.
     */
    public function joinQuiz(Request $request, Quiz $quiz, NameMatchingService $matchingService)
    {
        $request->validate([
            'nim' => 'required|string|max:50',
        ]);

        $nik = trim((string) $request->input('nim'));

        $employee = Employee::where('nim', $nik)
            ->where('status', 'Active')
            ->first();

        if (! $employee) {
            $inactive = Employee::where('nim', $nik)
                ->where('status', 'Inactive')
                ->first();

            if ($inactive) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Maaf, NIK tersebut tidak aktif.');
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Maaf, NIK tersebut tidak terdaftar sebagai peserta.');
        }

        $inProgress = Participant::where('quiz_id', $quiz->id)
            ->where('employee_id', $employee->id)
            ->whereNull('score')
            ->first();

        if ($inProgress) {
            session()->put("quiz_in_progress.{$quiz->id}", (string) $inProgress->id);

            return redirect()->route('quiz.take', ['quiz' => $quiz->slug, 'participant' => $inProgress->id]);
        }

        $attemptNumber = Participant::where('quiz_id', $quiz->id)
            ->where('employee_id', $employee->id)
            ->count() + 1;

        $participant = Participant::create([
            'quiz_id' => $quiz->id,
            'employee_id' => $employee->id,
            'name' => $employee->name,
            'nim' => $employee->nim,
            'attempt' => $attemptNumber,
        ]);

        session()->put("quiz_in_progress.{$quiz->id}", (string) $participant->id);

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

        session()->put("quiz_in_progress.{$quiz->id}", (string) $participant->id);

        // Eager load questions, options, and existing answers; apply deterministic randomization per participant
        $quiz->load('questions.options');
        $pid = (string) $participant->id;
        $quiz->setRelation('questions', $quiz->questions->sortBy(function ($q) use ($pid) {
            return sha1($pid.'|'.$q->id);
        })->values());
        $quiz->questions->each(function ($q) use ($pid) {
            $q->setRelation('options', $q->options->sortBy(function ($o) use ($pid) {
                return sha1($pid.'|'.$o->id);
            })->values());
        });

        $selected = $participant->answers()->get(['question_id', 'option_id'])->pluck('option_id', 'question_id');

        return view('quiz.take', compact('quiz', 'participant', 'selected'));
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

        if (count($request->answers) !== $totalQuestions) {
            abort(422, 'Semua soal harus dijawab.');
        }

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

        session()->forget("quiz_in_progress.{$quiz->id}");

        return redirect()->route('quiz.result', ['quiz' => $quiz->slug, 'participant' => $participant->id])
            ->with('success', 'Quiz submitted successfully!');
    }

    /**
     * Autosave a single answer while quiz is in progress.
     */
    public function autosaveAnswer(Request $request, Quiz $quiz, Participant $participant)
    {
        if ($participant->quiz_id !== $quiz->id) {
            abort(403);
        }

        if (! is_null($participant->score)) {
            return response()->json(['ok' => false, 'message' => 'Quiz already submitted'], 400);
        }

        $request->validate([
            'question_id' => 'required',
            'option_id' => 'required',
        ]);

        $quiz->load('questions.options');
        $questions = $quiz->questions->keyBy('id');
        $options = $quiz->questions->pluck('options')->flatten()->keyBy('id');

        $q = $questions->get($request->question_id);
        $o = $options->get($request->option_id);

        if (! $q || ! $o || (string) $o->question_id !== (string) $q->id) {
            return response()->json(['ok' => false, 'message' => 'Invalid mapping'], 422);
        }

        $participant->answers()->updateOrCreate(
            ['question_id' => $q->id],
            ['option_id' => $o->id]
        );

        return response()->json(['ok' => true]);
    }

    /**
     * Show the results page.
     */
    public function showResult(Quiz $quiz, Participant $participant)
    {
        if ($participant->quiz_id !== $quiz->id) {
            abort(403);
        }

        if (! is_null($participant->score)) {
            session()->forget("quiz_in_progress.{$quiz->id}");
        }

        $attempts = Participant::where('quiz_id', $quiz->id)
            ->where('employee_id', $participant->employee_id)
            ->whereNotNull('score')
            ->orderBy('created_at')
            ->get(['score', 'created_at', 'attempt']);

        return view('quiz.result', compact('quiz', 'participant', 'attempts'));
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

        return view('admin.dashboard', compact('quiz', 'participants', 'chartData', 'avgScore', 'inProgressCount', 'questionAnalytics'));
    }
}
