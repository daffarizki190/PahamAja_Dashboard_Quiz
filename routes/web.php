<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AiQuizController;
use App\Http\Controllers\QuizController;
use App\Models\Quiz;
use Database\Seeders\EmployeeSeeder;
use Illuminate\Support\Facades\Route;
use MongoDB\Driver\Manager;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

Route::get('/admin/login', [AdminAuthController::class, 'show'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

Route::get('/health/mongodb', function () {
    if (! app()->environment('local')) {
        abort(404);
    }

    $extensionOk = class_exists(Manager::class);

    try {
        Quiz::query()->limit(1)->get();

        return response()->json([
            'ok' => $extensionOk,
            'extension' => $extensionOk ? 'installed' : 'missing',
            'connection' => config('database.default'),
        ]);
    } catch (Throwable $e) {
        return response()->json([
            'ok' => false,
            'extension' => $extensionOk ? 'installed' : 'missing',
            'connection' => config('database.default'),
            'error' => $e->getMessage(),
        ], 500);
    }
});

// Quiz Engine Routes for Participants
Route::prefix('quiz')->name('quiz.')->group(function () {

    // Form to join the quiz (Enter Name & NIM)
    Route::get('{quiz:slug}', [QuizController::class, 'showJoinForm'])->name('join');

    // Process joining the quiz
    Route::post('{quiz:slug}/join', [QuizController::class, 'joinQuiz'])->name('join.process');

    // Page to take the quiz
    Route::get('{quiz:slug}/participant/{participant}/take', [QuizController::class, 'takeQuiz'])->name('take');

    // Submit Answers & Auto-Scoring Route
    Route::post('{quiz:slug}/participant/{participant}/answers', [QuizController::class, 'storeAnswer'])
        ->name('storeAnswer');

    // Result page after submission
    Route::get('{quiz:slug}/participant/{participant}/result', [QuizController::class, 'showResult'])->name('result');
});

// Admin Management & Dashboard Routes
Route::prefix('admin')->name('admin.')->middleware('admin.auth')->group(function () {
    // Quiz Management (CRUD)
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');
    Route::get('quizzes', [AdminController::class, 'index'])->name('quizzes.index');
    Route::get('quizzes/create', [AdminController::class, 'create'])->name('quizzes.create');
    Route::post('quizzes', [AdminController::class, 'store'])->name('quizzes.store');

    // AI Quiz Generation Routes
    Route::get('ai-quiz/create', [AiQuizController::class, 'aiCreate'])->name('quizzes.ai-create');
    Route::get('ai-quiz/generate', function () {
        return redirect()->route('admin.quizzes.ai-create');
    });
    Route::post('ai-quiz/generate', [AiQuizController::class, 'aiGenerate'])->name('quizzes.ai-generate');
    Route::post('ai-quiz/store', [AiQuizController::class, 'aiStore'])->name('quizzes.ai-store');

    Route::get('quizzes/{quiz:slug}', [AdminController::class, 'show'])->name('quizzes.show');

    Route::get('quizzes/{quiz:slug}/edit', [AdminController::class, 'edit'])->name('quizzes.edit');
    Route::patch('quizzes/{quiz:slug}', [AdminController::class, 'update'])->name('quizzes.update');
    Route::delete('quizzes/{quiz}', [AdminController::class, 'destroy'])->name('quizzes.destroy');

    // Real-time Monitoring Dashboard for a specific Quiz
    Route::get('quiz/{quiz:slug}/dashboard', [QuizController::class, 'showDashboard'])
        ->name('quiz.dashboard');

    // Export Excel Route
    Route::get('quiz/{quiz:slug}/export', [QuizController::class, 'exportExcel'])
        ->name('quiz.export');

    // Employee Master Data & Growth Analysis
    Route::get('employees', [AdminController::class, 'employeeIndex'])->name('employees.index');
    Route::post('employees', [AdminController::class, 'employeeStore'])->name('employees.store');
    Route::get('employees/export', [AdminController::class, 'employeeExport'])->name('employees.export');
    Route::get('employees/{employee}', [AdminController::class, 'employeeShow'])->name('employees.show');
    Route::put('employees/{employee}', [AdminController::class, 'employeeUpdate'])->name('employees.update');
    Route::delete('employees/{employee}', [AdminController::class, 'employeeDestroy'])->name('employees.destroy');

    // Participant Management
    Route::get('quiz/{quiz:slug}/participant/{participant}/answers', [AdminController::class, 'participantAnswers'])
        ->name('participant.answers');
    Route::delete('quiz/{quiz:slug}/participant/{participant}', [AdminController::class, 'participantDestroy'])
        ->name('participant.destroy');

    Route::get('force-seed', function () {
        if (! app()->environment('local')) {
            abort(404);
        }

        (new EmployeeSeeder)->run();

        return 'SEED_COMPLETED';
    })->name('force-seed');
});
