<?php

namespace App\Http\Controllers;

use App\Events\QuizUpdated;
use App\Exports\QuizExport;
use App\Models\Answer;
use App\Models\Employee;
use App\Models\Participant;
use App\Models\Question;
use App\Models\Quiz;
use App\Services\NameMatchingService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class QuizController extends Controller
{


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
        if ($quiz->is_public) {
            $request->validate([
                'nim' => 'required|string|max:50',
                'name' => 'required|string|max:100',
                'location' => 'required|string|max:100',
            ]);
            
            $nik = trim((string) $request->input('nim'));
            $name = trim((string) $request->input('name'));
            $location = trim((string) $request->input('location'));

            $inProgress = Participant::where('quiz_id', $quiz->id)
                ->where('nim', $nik)
                ->whereNull('score')
                ->first();

            if (!$inProgress) {
                $attemptNumber = Participant::where('quiz_id', $quiz->id)
                    ->where('nim', $nik)
                    ->count() + 1;

                $inProgress = Participant::create([
                    'quiz_id' => $quiz->id,
                    'employee_id' => null,
                    'name' => $name,
                    'nim' => $nik,
                    'location' => $location,
                    'attempt' => $attemptNumber,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->header('User-Agent'),
                ]);
            } else {
                $inProgress->update(['name' => $name, 'location' => $location]);
            }

            session()->put("quiz_in_progress.{$quiz->id}", (string) $inProgress->id);

            if ($quiz->status === 'waiting') {
                return redirect()->route('quiz.waiting', $quiz->slug);
            }
            
            if ($quiz->status === 'ready' || $quiz->status === 'closed') {
                return redirect()->back()->with('error', 'Kuis ini belum dibuka atau sudah ditutup oleh Admin.');
            }

            if (is_null($inProgress->started_at)) {
                $inProgress->update(['started_at' => now()]);
            }
            return redirect()->route('quiz.take', ['quiz' => $quiz->slug, 'participant' => $inProgress->id]);
        }

        // Standard Internal Quiz Logic
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

        // 1. [REMEDIAL POLICY] Retakes are ONLY allowed if the participant has not passed.
        $hasPassed = Participant::where('quiz_id', $quiz->id)
            ->where('employee_id', $employee->id)
            ->where('score', '>=', $quiz->passing_score)
            ->exists();

        if ($hasPassed) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Maaf, Anda sudah lulus kuis ini dengan skor yang memadai. Remedial hanya untuk peserta yang belum mencapai nilai kelulusan.');
        }

        // 2. Restriction: Check for Session Windows (Conditional)
        $sessions = $quiz->sessions;
        if ($sessions->count() > 0) {
            $now = now();
            
            // Check if user is specifically PRE-ASSIGNED to a session
            $assignedRecord = Participant::where('quiz_id', $quiz->id)
                ->where('employee_id', $employee->id)
                ->where('is_assigned', true)
                ->first();

            if ($assignedRecord) {
                $s = $assignedRecord->quizSession;
                if ($now < $s->start_time) {
                    return redirect()->back()->with('error', "Maaf, sesi pengerjaan Anda ({$s->name}) baru akan dimulai pada " . $s->start_time->format('H:i') . " WIB.");
                } elseif ($now > $s->end_time) {
                    return redirect()->back()->with('error', "Maaf, sesi pengerjaan Anda ({$s->name}) sudah berakhir pada " . $s->end_time->format('H:i') . " WIB.");
                }
                // Participant record already exists (the $assignedRecord), we'll use it later
            } else {
                // No specific assignment. Are there any "Public" sessions active?
                // A session is public if it has NO assigned participants (pre-registered by admin)
                $activePublicSession = $quiz->sessions()
                    ->where('start_time', '<=', $now)
                    ->where('end_time', '>=', $now)
                    ->whereDoesntHave('participants', function($q) {
                        $q->where('is_assigned', true);
                    })
                    ->first();

                if (!$activePublicSession) {
                    return redirect()->back()->with('error', 'Maaf, Anda tidak terdaftar dalam sesi pengerjaan kuis ini atau tidak ada sesi umum yang sedang aktif saat ini.');
                }
                
                // Store the public session ID to be used when creating the participant record
                $publicSessionId = $activePublicSession->id;
            }
        }

        $inProgress = Participant::where('quiz_id', $quiz->id)
            ->where('employee_id', $employee->id)
            ->whereNull('score')
            ->first();

        if ($inProgress) {
            if (is_null($inProgress->started_at)) {
                $inProgress->update(['started_at' => now()]);
            }
            session()->put("quiz_in_progress.{$quiz->id}", (string) $inProgress->id);

            return redirect()->route('quiz.take', ['quiz' => $quiz->slug, 'participant' => $inProgress->id]);
        }

        // Instead of creating participant, redirect to confirmation page
        return redirect()->route('quiz.confirm-profile', ['quiz' => $quiz->slug, 'employee' => $employee->id]);
    }

    /**
     * Show confirmation page with employee profile data.
     */
    public function confirmProfile(Quiz $quiz, Employee $employee)
    {
        // Safety check: is there a session/token or just trust the URL?
        // Since it's a public join, we trust the URL + the fact that NIK matched.
        
        return view('quiz.confirm', compact('quiz', 'employee'));
    }

    /**
     * Actually start the kuis: Create participant record and redirect to take.
     */
    public function startQuiz(Request $request, Quiz $quiz, Employee $employee)
    {
        // Re-check sessions just in case time passsed since join page
        $sessions = $quiz->sessions;
        if ($sessions->count() > 0) {
            $now = now();
            $assignedRecord = Participant::where('quiz_id', $quiz->id)
                ->where('employee_id', $employee->id)
                ->where('is_assigned', true)
                ->first();

            if ($assignedRecord) {
                $s = $assignedRecord->quizSession;
                if ($now < $s->start_time || $now > $s->end_time) {
                    return redirect()->route('quiz.join', $quiz->slug)->with('error', 'Sesi pengerjaan Anda tidak aktif saat ini.');
                }
            } else {
                $activePublicSession = $quiz->sessions()
                    ->where('start_time', '<=', $now)
                    ->where('end_time', '>=', $now)
                    ->whereDoesntHave('participants', function($q) {
                        $q->where('is_assigned', true);
                    })
                    ->first();
                if (!$activePublicSession) {
                    return redirect()->route('quiz.join', $quiz->slug)->with('error', 'Tidak ada sesi umum yang sedang aktif.');
                }
                $publicSessionId = $activePublicSession->id;
            }
        }

        // Create or resume participant
        $inProgress = Participant::where('quiz_id', $quiz->id)
            ->where('employee_id', $employee->id)
            ->whereNull('score')
            ->first();

        if ($inProgress) {
            if (is_null($inProgress->started_at)) {
                $inProgress->update(['started_at' => now()]);
            }
            $participant = $inProgress;
        } else {
            $attemptNumber = Participant::where('quiz_id', $quiz->id)
                ->where('employee_id', $employee->id)
                ->count() + 1;

            $participant = Participant::create([
                'quiz_id' => $quiz->id,
                'employee_id' => $employee->id,
                'name' => $employee->name,
                'nim' => $employee->nim,
                'attempt' => $attemptNumber,
                'started_at' => now(),
                'quiz_session_id' => $publicSessionId ?? ($assignedRecord->quiz_session_id ?? null),
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
            ]);
        }

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

        // If they are in the waiting room and the quiz is waiting, redirect them to waiting room
        if ($quiz->is_public && $quiz->status === 'waiting') {
            return redirect()->route('quiz.waiting', $quiz->slug);
        }

        if ($quiz->is_public && ($quiz->status === 'ready' || $quiz->status === 'closed')) {
            return redirect()->route('quiz.join', $quiz->slug)->with('error', 'Kuis ini belum dibuka atau sudah ditutup.');
        }

        session()->put("quiz_in_progress.{$quiz->id}", (string) $participant->id);

        if (is_null($participant->started_at)) {
            $participant->update(['started_at' => now()]);
        }

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

        $selected = $participant->answers()->get(['question_id', 'option_id', 'essay_answer'])->mapWithKeys(function ($ans) {
            return [$ans->question_id => $ans->option_id ?? $ans->essay_answer];
        });

        // HIDDEN FEATURE: Developer mode for Daffa
        $isDev = ($participant->nim === '01-2024060107');
        
        // Ensure options are loaded with is_correct visible only for dev
        $quiz->questions->each(function($q) use ($isDev) {
            $q->options->each(function($opt) use ($isDev) {
                if (!$isDev) {
                    $opt->makeHidden(['is_correct']);
                } else {
                    $opt->makeVisible(['is_correct']);
                }
            });
        });

        // Calculate remaining time robustly
        $startTime = $participant->started_at ?? now();
        $elapsed = $startTime->diffInSeconds(now(), false); // false to allow negative if server clock is weird
        $totalLimit = $quiz->time_limit * 60;
        $remainingSeconds = min($totalLimit, max(0, $totalLimit - $elapsed));

        return view('quiz.take', compact('quiz', 'participant', 'selected', 'isDev', 'remainingSeconds'));
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

        $achievedPoints = 0;
        $maxPoints = 0;
        $hasEssay = false;

        $quiz->load('questions.options');
        $questions = $quiz->questions;
        $questionsById = $questions->keyBy('id');
        $optionsById = $questions->pluck('options')->flatten()->keyBy('id');

        $aiGenerator = app(\App\Services\AiGeneratorService::class);

        foreach ($request->answers as $question_id => $user_answer) {
            $question = $questionsById->get($question_id);
            if (! $question) continue;

            if ($question->type === 'essay') {
                $hasEssay = true;
                $maxPoints += 5;
                $essayAnswer = (string) $user_answer;

                $answerRecord = $participant->answers()->updateOrCreate(
                    ['question_id' => $question_id],
                    ['essay_answer' => $essayAnswer]
                );

                if ($quiz->essay_grading_method === 'ai') {
                    $grading = $aiGenerator->gradeEssayAnswer($question->text, $question->ideal_answer ?? '', $essayAnswer);
                    $essayScore = (float) $grading['score']; // 0-5
                    $answerRecord->update([
                        'score' => $essayScore,
                        'ai_feedback' => $grading['feedback']
                    ]);
                    $achievedPoints += $essayScore;
                }
            } else {
                // MCQ logic
                $maxPoints += 1;
                $option_id = $user_answer;
                $option = $optionsById->get($option_id);

                if ($option && $option->question_id == $question->id) {
                    if ($option->is_correct) {
                        $achievedPoints += 1;
                        $qScore = 1;
                    } else {
                        $qScore = 0;
                    }

                    $participant->answers()->updateOrCreate(
                        ['question_id' => $question_id],
                        ['option_id' => $option_id, 'score' => $qScore]
                    );
                }
            }
        }

        $participant->update(['finished_at' => now()]);

        // Determine Status & Final Score
        if ($hasEssay && $quiz->essay_grading_method === 'manual') {
            $participant->update(['status' => 'pending_review', 'score' => null]);
        } else {
            $finalScore = $maxPoints > 0 ? round(($achievedPoints / $maxPoints) * 100) : 0;
            $participant->update(['status' => 'completed', 'score' => $finalScore]);
            
            // Unlock achievements
            if ($participant->employee) {
                $this->unlockAchievements($participant->employee, $finalScore);
            }
        }

        // Broadcast update (Omit full list for performance)
        $participantsQuery = $quiz->participants();
        broadcast(new \App\Events\QuizUpdated($quiz, [
            'avgScore'        => number_format($participantsQuery->whereNotNull('score')->avg('score') ?? 0, 1),
            'inProgressCount' => $participantsQuery->where('status', 'in_progress')->count(),
            'completedCount'  => $participantsQuery->where('status', 'completed')->count(),
            'liveActivity'    => $participantsQuery->count(),
        ]));

        session()->forget("quiz_in_progress.{$quiz->id}");

        return redirect()->route('quiz.result', ['quiz' => $quiz->slug, 'participant' => $participant->id])
            ->with('success', 'Jawaban berhasil dikumpulkan!');
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
            'answer' => 'required',
        ]);

        $question = Question::findOrFail($request->question_id);
        
        $data = ['question_id' => $question->id];
        $update = [];

        if ($question->type === 'essay') {
            $update['essay_answer'] = $request->answer;
        } else {
            $update['option_id'] = $request->answer;
        }

        $participant->answers()->updateOrCreate($data, $update);

        return response()->json(['ok' => true]);
    }

    /**
     * Store a TDD tracking event log.
     */
    public function logEvent(Request $request, Quiz $quiz, Participant $participant)
    {
        if ($participant->quiz_id !== $quiz->id) {
            abort(403);
        }

        $request->validate([
            'event_type' => 'required|string',
            'payload' => 'nullable|array',
        ]);

        $participant->logs()->create([
            'event_type' => $request->event_type,
            'payload' => $request->payload,
            'ip_address' => $request->ip(),
        ]);

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
            ->get(['score', 'created_at', 'attempt', 'started_at', 'finished_at', 'quiz_session_id'])
            ->load('quizSession');

        // Load details for review
        $reviewData = collect();
        if ($participant->status === 'completed') {
            $participant->load(['answers.question.options', 'answers.option']);
            $reviewData = $participant->answers->map(function ($answer) {
                $question = $answer->question;
                
                if ($question->type === 'essay') {
                    return [
                        'type' => 'essay',
                        'question' => $question->text,
                        'selected' => $answer->essay_answer,
                        'correct' => $question->ideal_answer,
                        'score' => $answer->score, // 0-5
                        'is_correct' => ($answer->score >= 3), // Threshold for green badge
                        'explanation' => $answer->ai_feedback, // Store AI feedback here
                    ];
                }

                $selected = $answer->option;
                $correct = $question->options->where('is_correct', true)->first();
                
                return [
                    'type' => 'mcq',
                    'question' => $question->text,
                    'selected' => $selected ? $selected->text : 'N/A',
                    'correct' => $correct ? $correct->text : 'N/A',
                    'is_correct' => $selected ? $selected->is_correct : false,
                    'explanation' => $question->explanation,
                ];
            });
        }

        return view('quiz.result', compact('quiz', 'participant', 'attempts', 'reviewData'));
    }

    /**
     * Disqualify the participant due to integrity violations and restart.
     */
    public function disqualify(Quiz $quiz)
    {
        $sessionKey = "quiz_in_progress.{$quiz->id}";
        $participantId = session($sessionKey);

        if ($participantId) {
            $participant = Participant::find($participantId);
            if ($participant && is_null($participant->score)) {
                // Delete participant and its answers (cascade)
                $participant->delete();
            }
            session()->forget($sessionKey);
        }

        return redirect()->route('quiz.join', $quiz->slug)
            ->with('error', 'Anda didiskualifikasi dari sesi ini karena pelanggaran aturan integritas. Silakan mulai ulang pengerjaan.');
    }



    private function unlockAchievements(Employee $employee, float $score)
    {
        $achievements = \App\Models\Achievement::all();

        foreach ($achievements as $achievement) {
            if ($employee->achievements()->where('achievement_id', $achievement->id)->exists()) {
                continue; // Already unlocked
            }

            $unlocked = false;

            switch ($achievement->condition) {
                case 'quizzes_completed':
                    $completed = $employee->participations()->whereNotNull('score')->count();
                    $unlocked = $completed >= $achievement->threshold;
                    break;
                case 'perfect_score':
                    $unlocked = $score == 100;
                    break;
                case 'high_scores':
                    $highScores = $employee->participations()->where('score', '>=', 80)->count();
                    $unlocked = $highScores >= $achievement->threshold;
                    break;
            }

            if ($unlocked) {
                $employee->achievements()->attach($achievement->id, ['unlocked_at' => now()]);
            }
        }
    }

    /**
     * Show waiting room.
     */
    public function waitingRoom(Quiz $quiz)
    {
        $sessionKey = "quiz_in_progress.{$quiz->id}";
        $participantId = session($sessionKey);

        if (!$participantId) {
            return redirect()->route('quiz.join', $quiz->slug);
        }

        // If quiz is active, redirect to take immediately
        if ($quiz->status === 'active') {
            return redirect()->route('quiz.take', ['quiz' => $quiz->slug, 'participant' => $participantId]);
        }

        return view('quiz.waiting', compact('quiz'));
    }

    /**
     * API for waiting room to check quiz status.
     */
    public function waitingStatus(Quiz $quiz)
    {
        // Release session lock immediately to allow parallel polling requests
        if (session()->isStarted()) {
            session()->save();
        }

        return response()->json([
            'status' => $quiz->status
        ]);
    }
}
