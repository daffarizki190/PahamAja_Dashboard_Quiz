<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    /**
     * Display a listing of all quizzes.
     */
    public function index()
    {
        $quizzes = Quiz::latest()->get();

        return view('admin.quizzes.index', compact('quizzes'));
    }

    /**
     * Store a newly created employee in storage.
     */
    public function employeeStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nim' => 'required|string|unique:employees,nim',
            'department' => 'required|string|max:255',
            'position' => 'required|string|max:255',
        ]);

        \App\Models\Employee::create($request->all() + ['status' => 'Active']);

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
        $quiz->questions()->each(function($question) {
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
        $quiz->questions()->each(function($question) {
            $question->options()->delete();
            $question->delete();
        });
        
        $quiz->delete();

        return redirect()->route('admin.quizzes.index')->with('success', 'Quiz deleted successfully!');
    }

    /**
     * Display a listing of all registered employees.
     */
    public function employeeIndex()
    {
        $employees = \App\Models\Employee::latest()->get();
        return view('admin.employees.index', compact('employees'));
    }

    /**
     * Display growth analysis for a specific employee.
     */
    public function employeeShow(\App\Models\Employee $employee)
    {
        $participations = \App\Models\Participant::where('employee_id', $employee->id)
            ->with('quiz')
            ->whereNotNull('score')
            ->get();

        // Growth data for Chart.js
        $chartData = [
            'labels' => $participations->pluck('quiz.title')->toArray(),
            'scores' => $participations->pluck('score')->toArray(),
        ];

        return view('admin.employees.show', compact('employee', 'participations', 'chartData'));
    }

    /**
     * Update the specified employee in storage.
     */
    public function employeeUpdate(Request $request, \App\Models\Employee $employee)
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

    /**
     * Remove the specified employee from storage.
     */
    public function employeeDestroy(\App\Models\Employee $employee)
    {
        $employee->delete();
        return redirect()->route('admin.employees.index')->with('success', 'Employee removed from master list.');
    }

    /**
     * Remove a specific participant from a quiz result.
     */
    public function participantDestroy(Quiz $quiz, \App\Models\Participant $participant)
    {
        $participant->delete();
        return back()->with('success', 'Participant record deleted.');
    }
}
