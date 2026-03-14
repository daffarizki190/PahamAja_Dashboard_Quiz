<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\QuizController;
use App\Models\Quiz;
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
Route::prefix('admin')->name('admin.')->group(function () {
    // Quiz Management (CRUD)
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');
    Route::get('quizzes', [AdminController::class, 'index'])->name('quizzes.index');
    Route::get('quizzes/create', [AdminController::class, 'create'])->name('quizzes.create');
    Route::post('quizzes', [AdminController::class, 'store'])->name('quizzes.store');
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
    Route::get('employees/{employee}', [AdminController::class, 'employeeShow'])->name('employees.show');
    Route::put('employees/{employee}', [AdminController::class, 'employeeUpdate'])->name('employees.update');
    Route::delete('employees/{employee}', [AdminController::class, 'employeeDestroy'])->name('employees.destroy');

    // Participant Management
    Route::delete('quiz/{quiz:slug}/participant/{participant}', [AdminController::class, 'participantDestroy'])
        ->name('participant.destroy');

    // Temporary Seeding Route (Delete after use)
    Route::get('force-seed', function() {
        (new \Database\Seeders\EmployeeSeeder())->run();
        return "SEED_COMPLETED";
    });

    // Anti-Cheat Logging Route (AJAX)
    Route::post('quiz/{quiz:slug}/participant/{participant}/cheat', [QuizController::class, 'logCheatAttempt'])
        ->name('quiz.cheat');
});
