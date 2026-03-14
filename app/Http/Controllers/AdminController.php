<?php

namespace App\Http\Controllers;

use App\Exports\EmployeeGrowthExport;
use App\Models\Employee;
use App\Models\Participant;
use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class AdminController extends Controller
{
    /**
     * Display a listing of all quizzes.
     */
    public function index()
    {
        $quizzes = Quiz::query()->latest()->get();

        $quizIds = $quizzes->pluck('id')->all();
        $driver = $quizzes->first()?->getConnection()->getDriverName();

        if (count($quizIds) > 0) {
            if ($driver === 'mongodb') {
                $participantCounts = collect(Participant::raw(function ($collection) use ($quizIds) {
                    return $collection->aggregate([
                        ['$match' => ['quiz_id' => ['$in' => $quizIds]]],
                        ['$group' => ['_id' => '$quiz_id', 'count' => ['$sum' => 1]]],
                    ]);
                }))->mapWithKeys(function ($row) {
                    return [(string) $row->_id => (int) $row->count];
                });

                $questionCounts = collect(Question::raw(function ($collection) use ($quizIds) {
                    return $collection->aggregate([
                        ['$match' => ['quiz_id' => ['$in' => $quizIds]]],
                        ['$group' => ['_id' => '$quiz_id', 'count' => ['$sum' => 1]]],
                    ]);
                }))->mapWithKeys(function ($row) {
                    return [(string) $row->_id => (int) $row->count];
                });
            } else {
                $participantCounts = Participant::query()
                    ->whereIn('quiz_id', $quizIds)
                    ->select('quiz_id', DB::raw('count(*) as aggregate_count'))
                    ->groupBy('quiz_id')
                    ->pluck('aggregate_count', 'quiz_id')
                    ->map(function ($c) {
                        return (int) $c;
                    });

                $questionCounts = Question::query()
                    ->whereIn('quiz_id', $quizIds)
                    ->select('quiz_id', DB::raw('count(*) as aggregate_count'))
                    ->groupBy('quiz_id')
                    ->pluck('aggregate_count', 'quiz_id')
                    ->map(function ($c) {
                        return (int) $c;
                    });
            }

            $quizzes->each(function (Quiz $quiz) use ($participantCounts, $questionCounts) {
                $id = (string) $quiz->id;
                $quiz->setAttribute('participants_count', (int) ($participantCounts[$id] ?? 0));
                $quiz->setAttribute('questions_count', (int) ($questionCounts[$id] ?? 0));
            });
        }

        $stats = [
            'quizzes' => $quizzes->count(),
            'questions' => Question::count(),
            'employees' => Employee::count(),
            'participants' => Participant::count(),
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

    /**
     * Display the specified quiz.
     */
    public function show(Quiz $quiz)
    {
        $quiz->load('questions.options');

        return view('admin.quizzes.show', compact('quiz'));
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
            'questions.*.text' => 'required|string',
            'questions.*.options' => 'required|array|min:2',
            'questions.*.options.*.text' => 'required|string',
            'questions.*.correct_option' => 'required|integer',
        ]);

        $quiz->update([
            'title' => $request->title,
            'time_limit' => $request->time_limit,
            'passing_score' => $request->passing_score,
        ]);

        // Simple update: delete existing questions and recreate
        // In a production app, you might want to match IDs to prevent data loss for participants
        $quiz->questions()->each(function ($question) {
            $question->options()->delete();
            $question->delete();
        });

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

        return redirect()->route('admin.quizzes.show', $quiz->slug)->with('success', 'Quiz updated successfully!');
    }

    /**
     * Remove the specified quiz from storage.
     */
    public function destroy(Quiz $quiz)
    {
        $quiz->questions()->each(function ($question) {
            $question->options()->delete();
            $question->delete();
        });

        $quiz->delete();

        return redirect()->route('admin.quizzes.index')->with('success', 'Quiz deleted successfully!');
    }

    public function employeeIndex()
    {
        $employees = Employee::latest()->get();

        $participations = Participant::whereNotNull('score')
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
            ->with('quiz')
            ->whereNotNull('score')
            ->get();

        $chartData = [
            'labels' => $participations->pluck('quiz.title')->toArray(),
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
        $employee->delete();

        return redirect()->route('admin.employees.index')->with('success', 'Employee removed from master list.');
    }

    /**
     * Remove a specific participant from a quiz result.
     */
    public function participantDestroy(Quiz $quiz, Participant $participant)
    {
        $participant->delete();

        return back()->with('success', 'Participant record deleted.');
    }
}
