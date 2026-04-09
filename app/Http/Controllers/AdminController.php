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
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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

        return view('admin.quizzes.index', compact('quizzes', 'stats'));
    }

    public function employeeStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nim' => 'required|string|unique:employees,nim',
            'department' => 'required|string|max:255',
            'position' => 'required|string|max:255',
        ]);

        Employee::create($request->all() + ['status' => 'Active']);

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
     * Display the specified quiz.
     */
    public function show(Quiz $quiz)
    {
        $quiz->load(['questions.options', 'participants.quizSession']);
        
        $sessions = QuizSession::where('quiz_id', $quiz->id)->orderBy('start_time')->get();
        $employees = Employee::where('status', 'Active')->orderBy('name')->get();

        return view('admin.quizzes.show', compact('quiz', 'sessions', 'employees'));
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
                        'is_assigned' => true,
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
                    $question->update(['text' => $qData['text']]);
                } else {
                    $question = $quiz->questions()->create(['text' => $qData['text']]);
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
                $sorted = $items->sortBy(function ($p) {
                    return $p->updated_at?->getTimestamp() ?? 0;
                })->values();

                $scores = $sorted->pluck('score')->filter(function ($s) {
                    return ! is_null($s);
                })->values();

                $attempts = $scores->count();
                $avg = $attempts > 0 ? round($scores->avg(), 1) : 0.0;
                $last = $attempts > 0 ? (float) $scores->last() : null;
                $prev = $attempts > 1 ? (float) $scores->get($attempts - 2) : null;
                $delta = (! is_null($last) && ! is_null($prev)) ? round($last - $prev, 1) : null;
                $lastAt = $sorted->last()?->updated_at;

                return [
                    'attempts' => $attempts,
                    'avg' => $avg,
                    'last' => $last,
                    'delta' => $delta,
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
            ->get();

        $chartData = [
            'labels' => $participations->pluck('quiz.title')->map(function ($title) {
                // Buat kode singkat: ambil huruf pertama tiap kata (maks 4 kata), uppercase
                $words = preg_split('/[\s\-]+/', $title);
                $words = array_filter($words); // hapus kosong
                $initials = array_map(fn ($w) => strtoupper(substr($w, 0, 1)), array_slice($words, 0, 4));

                return implode('', $initials);
            })->toArray(),
            'fullLabels' => $participations->pluck('quiz.title')->toArray(),
            'scores' => $participations->pluck('score')->toArray(),
        ];

        return view('admin.employees.show', compact('employee', 'participations', 'chartData'));
    }

    public function employeeUpdate(Request $request, Employee $employee)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'status' => 'required|string|in:Active,Inactive',
        ]);

        $employee->update($request->only(['name', 'department', 'position', 'status']));

        return back()->with('success', 'Employee updated successfully!');
    }

    public function employeeDestroy(Employee $employee)
    {
        // Database nullOnDelete() handles participants. Historical records stay, but linked to NULL employee ID.
        $employee->delete();

        return redirect()->route('admin.employees.index')->with('success', 'Employee removed. Historical records preserved.');
    }

    public function participantAnswers(Quiz $quiz, Participant $participant)
    {
        if ($participant->quiz_id !== $quiz->id) {
            abort(403);
        }

        $quiz->load('questions.options');
        $answers = Answer::where('participant_id', $participant->id)->get();
        $answersByQuestion = $answers->keyBy('question_id');

        $rows = $quiz->questions->map(function ($question) use ($answersByQuestion) {
            $answer = $answersByQuestion->get($question->id);
            $selectedOption = $answer ? $question->options->firstWhere('id', $answer->option_id) : null;
            $correctOption = $question->options->firstWhere('is_correct', true);

            return [
                'question' => (string) $question->text,
                'selected' => $selectedOption?->text,
                'correct' => $correctOption?->text,
                'is_correct' => $selectedOption && $correctOption ? ((string) $selectedOption->id === (string) $correctOption->id) : null,
            ];
        })->values();

        return view('admin.participants.answers', compact('quiz', 'participant', 'rows'));
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
}
