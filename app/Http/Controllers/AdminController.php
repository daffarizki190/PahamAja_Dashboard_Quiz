<?php

namespace App\Http\Controllers;

use App\Exports\EmployeeGrowthExport;
use App\Models\Answer;
use App\Models\Employee;
use App\Models\Participant;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\QuizSession;
use App\Services\QuizImportService;
use App\Services\AvatarStorageService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class AdminController extends Controller
{
    /**
     * Menampilkan daftar semua kuis beserta statistik peserta dan jumlah soal.
     */
    public function index()
    {
        $quizzes = Quiz::query()->latest()->get();

        $quizIds = $quizzes->pluck('id')->all();

        if (count($quizIds) > 0) {
            $participantCounts = Participant::query()
                ->whereIn('quiz_id', $quizIds)
                ->select('quiz_id', DB::raw('count(*) as total'))
                ->groupBy('quiz_id')
                ->pluck('total', 'quiz_id')
                ->map(fn ($c) => (int) $c);

            $questionCounts = Question::query()
                ->whereIn('quiz_id', $quizIds)
                ->select('quiz_id', DB::raw('count(*) as total'))
                ->groupBy('quiz_id')
                ->pluck('total', 'quiz_id')
                ->map(fn ($c) => (int) $c);

            $quizzes->each(function (Quiz $quiz) use ($participantCounts, $questionCounts) {
                $quiz->setAttribute('participants_count', $participantCounts[$quiz->id] ?? 0);
                $quiz->setAttribute('questions_count', $questionCounts[$quiz->id] ?? 0);
            });
        }

        $stats = [
            'quizzes' => $quizzes->count(),
            'questions' => Question::count(),
            'employees' => Employee::count(),
            'participants' => Participant::whereHas('quiz')->count(),
        ];

        // Global Leaderboard (Top 5 based on average score)
        $topEmployees = Employee::where('status', 'Active')
            ->whereHas('participations', function($q) {
                $q->whereNotNull('score');
            })
            ->with(['participations' => function($q) {
                $q->whereNotNull('score');
            }])
            ->get()
            ->map(function($emp) {
                $bestScores = $emp->participations->groupBy('quiz_id')->map(function($p) {
                    return $p->max('score');
                });
                $emp->avg_score = $bestScores->count() > 0 ? round($bestScores->avg(), 1) : 0;
                $emp->quizzes_taken = $bestScores->count();
                return $emp;
            })
            ->sortByDesc('avg_score')
            ->take(5)
            ->values();

        return view('admin.quizzes.index', compact('quizzes', 'stats', 'topEmployees'));
    }

    public function employeeStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nim' => 'required|string|unique:employees,nim',
            'department' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $data = $request->all();
        $data['status'] = 'Active';

        if ($request->hasFile('avatar')) {
            $avatarService = new AvatarStorageService();
            $data['avatar'] = $avatarService->upload($request->file('avatar'));
        }

        Employee::create($data);

        return back()->with('success', 'Employee registered successfully!');
    }

    /**
     * Show the form for creating a new quiz.
     */
    public function create()
    {
        return view('admin.quizzes.create');
    }

    public function import()
    {
        return view('admin.quizzes.import');
    }

    public function importStore(Request $request, QuizImportService $importer)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'time_limit' => 'required|integer|min:1',
            'passing_score' => 'required|integer|min:0|max:100',
            'file' => 'required|file|mimes:csv,json|max:10240',
        ]);

        try {
            $questions = $importer->parseUploadedQuestions($request->file('file'));

            $quiz = Quiz::create([
                'title' => $request->title,
                'slug' => Str::slug($request->title).'-'.Str::random(5),
                'time_limit' => $request->time_limit,
                'passing_score' => $request->passing_score,
            ]);

            foreach ($questions as $qData) {
                $question = $quiz->questions()->create([
                    'text' => $qData['text'],
                ]);

                foreach ($qData['options'] as $oData) {
                    $question->options()->create([
                        'text' => $oData['text'],
                        'is_correct' => $oData['is_correct'] === true,
                    ]);
                }
            }

            return redirect()->route('admin.quizzes.index')->with('success', 'Import berhasil. Kuis dibuat.');
        } catch (Exception $e) {
            return back()->with('error', 'Import gagal: '.$e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified quiz with management and analytics.
     */
    public function show(Quiz $quiz)
    {
        // 1. Eager Load with selective columns
        $quiz->load(['questions' => function($q) {
            $q->with('options');
        }]);
        
        $sessions = QuizSession::where('quiz_id', $quiz->id)->orderBy('start_time')->get();
        $employees = Employee::where('status', 'Active')->select('id', 'name', 'avatar')->get();

        // 2. Optimized Participant Loading
        $allParticipants = $quiz->participants()
            ->with('employee:id,name,avatar')
            ->orderBy('score', 'desc')
            ->orderBy('updated_at', 'asc') // Faster duration as tie-breaker
            ->get();
        
        // Group Internal by employee_id, Public by nim (Unique Participants)
        $internalRaw = $allParticipants->whereNotNull('employee_id')->groupBy('employee_id')->map(fn($group) => $group->first());
        $publicRaw = $allParticipants->whereNull('employee_id')->groupBy('nim')->map(fn($group) => $group->first());
        
        $participants = $internalRaw->concat($publicRaw);

        // 3. Separate Public vs Internal (Basic Filter)
        $internalParticipants = $participants->filter(fn($p) => !is_null($p->employee_id));
        $publicParticipants = $participants->filter(fn($p) => is_null($p->employee_id));

        $sorter = function ($a, $b) {
            if (is_null($a->score) && is_null($b->score)) return 0;
            if (is_null($a->score)) return 1;
            if (is_null($b->score)) return -1;
            if ($a->score !== $b->score) return $b->score <=> $a->score;
            return ($a->updated_at?->getTimestamp() ?? 0) <=> ($b->updated_at?->getTimestamp() ?? 0);
        };

        $sortedPublic = $publicParticipants->sort($sorter)->values();
        $sortedInternal = $internalParticipants->sort($sorter)->values();

        // Format durations
        $sortedPublic->each(fn($p) => $p->duration_formatted = $p->duration ?? '--:--');
        $sortedInternal->each(fn($p) => $p->duration_formatted = $p->duration ?? '--:--');

        // 4. Correct Summary Analytics
        $totalUnique = $participants->count();
        $finishedParticipants = $participants->whereNotNull('score');
        $avgScore = $finishedParticipants->avg('score') ?? 0;
        $inProgressCount = $participants->whereNull('score')->count();

        // 5. Optimized Question Analytics (Single Pass)
        $finishedIds = $finishedParticipants->pluck('id')->toArray();
        $answersCount = Answer::whereIn('participant_id', $finishedIds)
            ->selectRaw('question_id, COUNT(*) as total, SUM(score) as correct')
            ->groupBy('question_id')
            ->get()
            ->keyBy('question_id');

        $questionAnalytics = $quiz->questions->map(function ($q) use ($answersCount, $finishedParticipants) {
            $stat = $answersCount->get($q->id);
            $total = $stat ? $stat->total : 0;
            $correct = $stat ? $stat->correct : 0;
            return [
                'text' => $q->text,
                'answered' => $total,
                'participants' => $finishedParticipants->count(),
                'correct_rate' => $total > 0 ? round(($correct / $total) * 100, 1) : 0,
            ];
        });

        $chartData = [
            'labels' => ['0-50', '51-75', '76-100'],
            'data' => [
                $finishedParticipants->whereBetween('score', [0, 50])->count(),
                $finishedParticipants->whereBetween('score', [51, 75])->count(),
                $finishedParticipants->whereBetween('score', [76, 100])->count(),
            ]
        ];

        return view('admin.quizzes.show', compact(
            'quiz', 'sessions', 'employees', 'sortedPublic', 'sortedInternal', 
            'avgScore', 'inProgressCount', 'questionAnalytics', 'chartData', 'totalUnique'
        ));
    }

    public function storeSession(Request $request, Quiz $quiz)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
        ]);

        $quiz->sessions()->create($request->all());

        return back()->with('success', 'Sesi pengerjaan berhasil ditambahkan.');
    }

    public function destroySession(QuizSession $session)
    {
        $session->delete();
        return back()->with('success', 'Sesi pengerjaan berhasil dihapus.');
    }

    public function assignParticipants(Request $request, QuizSession $session)
    {
        $request->validate([
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'exists:employees,id',
        ]);

        $quizId = $session->quiz_id;
        $employeeIds = $request->employee_ids;

        DB::transaction(function () use ($quizId, $session, $employeeIds) {
            foreach ($employeeIds as $empId) {
                $employee = Employee::find($empId);
                
                // Pre-create participant record if it doesn't exist for this quiz
                // or update it if it's already there but not finished.
                Participant::updateOrCreate(
                    [
                        'quiz_id' => $quizId,
                        'employee_id' => $empId,
                        'score' => null, // Only for those who haven't finished
                    ],
                    [
                        'quiz_session_id' => $session->id,
                        'name' => $employee->name,
                        'nim' => $employee->nim,
                        'is_assigned' => DB::raw('TRUE'),
                    ]
                );
            }
        });

        return back()->with('success', 'Peserta berhasil ditugaskan ke sesi ini.');
    }

    /**
     * Show the form for editing the specified quiz.
     */
    public function edit(Quiz $quiz)
    {
        $quiz->load('questions.options');

        return view('admin.quizzes.edit', compact('quiz'));
    }

    /**
     * Store a newly created quiz in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'time_limit' => 'required|integer|min:1',
            'passing_score' => 'required|integer|min:0|max:100',
            'is_public' => 'nullable|boolean',
            'questions' => 'required|array|min:1',
            'questions.*.text' => 'required|string',
            'questions.*.options' => 'required|array|min:2',
            'questions.*.options.*.text' => 'required|string',
            'questions.*.correct_option' => 'required|integer',
        ]);

        $quiz = Quiz::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title).'-'.Str::random(5),
            'time_limit' => $request->time_limit,
            'passing_score' => $request->passing_score,
            'is_public' => $request->has('is_public'),
            'status' => $request->has('is_public') ? 'waiting' : 'ready',
        ]);

        foreach ($request->questions as $qData) {
            $question = $quiz->questions()->create([
                'text' => $qData['text'],
            ]);

            foreach ($qData['options'] as $oIndex => $oData) {
                $question->options()->create([
                    'text' => $oData['text'],
                    'is_correct' => ($oIndex == $qData['correct_option']),
                ]);
            }
        }

        return redirect()->route('admin.quizzes.index')->with('success', 'Quiz created successfully!');
    }

    /**
     * Update the specified quiz in storage.
     */
    public function update(Request $request, Quiz $quiz)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'time_limit' => 'required|integer|min:1',
            'passing_score' => 'required|integer|min:0|max:100',
            'is_public' => 'nullable|boolean',
            'questions' => 'required|array|min:1',
            'questions.*.id' => 'nullable|integer',
            'questions.*.text' => 'required|string',
            'questions.*.options' => 'required|array|min:2',
            'questions.*.options.*.id' => 'nullable|integer',
            'questions.*.options.*.text' => 'required|string',
            'questions.*.correct_option' => 'required|integer',
        ]);

        DB::transaction(function () use ($request, $quiz) {
            $quiz->update([
                'title' => $request->title,
                'time_limit' => $request->time_limit,
                'passing_score' => $request->passing_score,
                'is_public' => $request->has('is_public'),
                'status' => ($request->has('is_public') && $quiz->status === 'ready') ? 'waiting' : $quiz->status,
            ]);

            $quiz->participants()->delete();

            // Sync Questions
            $submittedQuestionIds = collect($request->questions)->pluck('id')->filter()->all();

            // Delete questions not in request (database cascade will handle options/answers)
            $quiz->questions()->whereNotIn('id', $submittedQuestionIds)->delete();

            foreach ($request->questions as $qData) {
                // Find existing or create new question
                $question = isset($qData['id'])
                    ? $quiz->questions()->find($qData['id'])
                    : null;

                if ($question) {
                    $question->update([
                        'text' => $qData['text'],
                        'explanation' => $qData['explanation'] ?? null
                    ]);
                } else {
                    $question = $quiz->questions()->create([
                        'text' => $qData['text'],
                        'explanation' => $qData['explanation'] ?? null
                    ]);
                }

                // Sync Options
                $submittedOptionIds = collect($qData['options'])->pluck('id')->filter()->all();

                // Delete existing options not in request
                $question->options()->whereNotIn('id', $submittedOptionIds)->delete();

                foreach ($qData['options'] as $oIndex => $oData) {
                    $isCorrect = ((int) $oIndex === (int) $qData['correct_option']);

                    $option = isset($oData['id'])
                        ? $question->options()->find($oData['id'])
                        : null;

                    if ($option) {
                        $option->update([
                            'text' => $oData['text'],
                            'is_correct' => $isCorrect,
                        ]);
                    } else {
                        $question->options()->create([
                            'text' => $oData['text'],
                            'is_correct' => $isCorrect,
                        ]);
                    }
                }
            }
        });

        return redirect()->route('admin.quizzes.show', $quiz->slug)->with('success', 'Quiz updated successfully!');
    }

    /**
     * Remove the specified quiz from storage.
     */
    public function destroy(Quiz $quiz)
    {
        // Database cascades handle cleaning up questions, options, participants, and answers
        $quiz->delete();

        return redirect()->route('admin.quizzes.index')->with('success', 'Quiz and all associated records deleted successfully!');
    }

    public function employeeIndex()
    {
        $employees = Employee::latest()->get();

        $participations = Participant::whereNotNull('score')
            ->whereHas('quiz')
            ->whereHas('employee')
            ->get(['nim', 'score', 'updated_at']);

        $statsByNim = $participations
            ->groupBy('nim')
            ->map(function ($items) {
                // Group by quiz first to find the best score for each quiz
                $bestByQuiz = $items->groupBy('quiz_id')->map(function($qItems) {
                    return $qItems->sortByDesc('score')->first();
                });

                $scores = $bestByQuiz->pluck('score')->values();
                $allScores = $items->pluck('score')->values();

                $attempts = $allScores->count();
                $avg = $scores->count() > 0 ? round($scores->avg(), 1) : 0.0;
                $highest = $scores->count() > 0 ? (float) $scores->max() : null;
                $lastAt = $items->sortByDesc('updated_at')->first()?->updated_at;

                return [
                    'attempts' => $attempts,
                    'avg' => $avg,
                    'last' => $highest, // PRIORITIZE BEST SCORE
                    'delta' => null, 
                    'last_at' => $lastAt,
                ];
            });

        $growthRows = $employees
            ->map(function (Employee $employee) use ($statsByNim) {
                $stats = $statsByNim->get($employee->nim, [
                    'attempts' => 0,
                    'avg' => 0.0,
                    'last' => null,
                    'delta' => null,
                    'last_at' => null,
                ]);

                return [
                    'id' => (string) $employee->id,
                    'name' => (string) $employee->name,
                    'nim' => (string) $employee->nim,
                    'department' => (string) $employee->department,
                    'position' => (string) $employee->position,
                    'avatar' => $employee->avatar,
                    'avatar_url' => avatar_url($employee->avatar),
                    'attempts' => (int) $stats['attempts'],
                    'avg' => (float) $stats['avg'],
                    'last' => $stats['last'],
                    'delta' => $stats['delta'],
                    'last_at' => $stats['last_at'],
                ];
            })
            ->sortByDesc('avg')
            ->values();

        return view('admin.employees.index', compact('employees', 'growthRows', 'statsByNim'));
    }

    public function employeeExport()
    {
        return Excel::download(new EmployeeGrowthExport, 'Growth-Reports.xlsx');
    }

    public function employeeShow(Employee $employee)
    {
        $participations = Participant::where('employee_id', $employee->id)
            ->whereHas('quiz')
            ->with('quiz')
            ->whereNotNull('score')
            ->get()
            ->groupBy('quiz_id')
            ->map(function ($group) {
                return $group->sortByDesc('score')->first();
            });

        $chartData = [
            'labels' => $participations->pluck('quiz.title')->map(function ($title) {
                $words = preg_split('/[\s\-]+/', $title);
                $initials = array_map(fn ($w) => strtoupper(substr($w, 0, 1)), array_slice($words, 0, 4));
                return implode('', $initials);
            })->toArray(),
            'fullLabels' => $participations->pluck('quiz.title')->toArray(),
            'scores' => $participations->pluck('score')->toArray(),
        ];

        $completedQuizIds = $participations->pluck('quiz_id')->toArray();
        $uncompletedQuizzes = Quiz::whereNotIn('id', $completedQuizIds)->orderBy('created_at', 'desc')->get();

        return view('admin.employees.show', compact('employee', 'participations', 'chartData', 'uncompletedQuizzes'));
    }

    public function employeeUpdate(Request $request, Employee $employee)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'status' => 'required|string|in:Active,Inactive',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $data = $request->only(['name', 'department', 'position', 'status']);

        if ($request->hasFile('avatar')) {
            $avatarService = new AvatarStorageService();
            // Delete old avatar
            $avatarService->delete($employee->avatar);
            $data['avatar'] = $avatarService->upload($request->file('avatar'));
        }

        $employee->update($data);

        return back()->with('success', 'Employee updated successfully!');
    }

    public function employeeDestroy(Employee $employee)
    {
        // Delete avatar from storage
        (new AvatarStorageService())->delete($employee->avatar);
        
        // Delete related participant records and answers
        foreach ($employee->participations as $participation) {
            $participation->answers()->delete();
            $participation->delete();
        }

        // Detach achievements
        $employee->achievements()->detach();

        // Delete the employee
        $employee->delete();

        return redirect()->route('admin.employees.index')->with('success', 'Karyawan beserta seluruh riwayat nilainya berhasil dihapus secara permanen.');
    }

    public function participantAnswers(Quiz $quiz, Participant $participant)
    {
        if ($participant->quiz_id !== $quiz->id) {
            abort(403);
        }

        $quiz->load('questions.options');
        $participant->load('logs');
        $answers = Answer::where('participant_id', $participant->id)->get();
        $answersByQuestion = $answers->keyBy('question_id');

        $rows = $quiz->questions->map(function ($question) use ($answersByQuestion) {
            $answer = $answersByQuestion->get($question->id);
            $selectedOption = $answer ? $question->options->firstWhere('id', $answer->option_id) : null;
            $correctOption = $question->options->firstWhere('is_correct', true);

            return [
                'question' => (string) $question->text,
                'explanation' => (string) $question->explanation,
                'selected' => $selectedOption?->text ?? $answer?->essay_answer,
                'correct' => $correctOption?->text ?? $question->ideal_answer,
                'is_correct' => $selectedOption && $correctOption ? ((string) $selectedOption->id === (string) $correctOption->id) : ($answer ? (float)$answer->score > 0 : null),
                'ai_feedback' => $answer?->ai_feedback,
            ];
        })->values();

        $logs = $participant->logs()->latest()->get();

        return view('admin.participants.answers', compact('quiz', 'participant', 'rows', 'logs'));
    }

    /**
     * Remove a specific participant from a quiz result.
     */
    public function participantDestroy(Quiz $quiz, Participant $participant)
    {
        $participant->answers()->delete();
        $participant->delete();

        return back()->with('success', 'Participant record deleted.');
    }

    /**
     * Show manual review page for essay questions.
     */
    public function reviewEssay(Quiz $quiz, Participant $participant)
    {
        if ($participant->quiz_id !== $quiz->id) abort(403);
        
        $participant->load(['answers.question', 'answers.option']);
        $essayAnswers = $participant->answers->filter(fn($a) => $a->question->type === 'essay');

        return view('admin.quizzes.review', compact('quiz', 'participant', 'essayAnswers'));
    }

    /**
     * Store manual review scores.
     */
    public function storeReview(Request $request, Quiz $quiz, Participant $participant)
    {
        $request->validate([
            'scores' => 'required|array',
            'scores.*' => 'required|numeric|min:0|max:5',
            'feedbacks' => 'nullable|array',
        ]);

        DB::transaction(function() use ($request, $participant) {
            foreach ($request->scores as $answerId => $score) {
                $answer = Answer::find($answerId);
                if ($answer && $answer->participant_id == $participant->id) {
                    $answer->update([
                        'score' => $score,
                        'ai_feedback' => $request->feedbacks[$answerId] ?? null
                    ]);
                }
            }

            // Recalculate Final Score
            $achieved = 0;
            $max = 0;
            $participant->load('answers.question');
            
            foreach ($participant->answers as $ans) {
                if ($ans->question->type === 'essay') {
                    $max += 5;
                } else {
                    $max += 1;
                }
                $achieved += (float) $ans->score;
            }

            $finalScore = $max > 0 ? round(($achieved / $max) * 100) : 0;
            $participant->update([
                'score' => $finalScore,
                'status' => 'completed'
            ]);
        });

        return redirect()->route('admin.quizzes.show', $quiz->slug)->with('success', 'Penilaian esai berhasil disimpan!');
    }
}
