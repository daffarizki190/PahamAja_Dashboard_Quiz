<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AiInsightController;
use App\Http\Controllers\AiQuizController;
use App\Http\Controllers\DevController;
use App\Http\Controllers\PdfExportController;
use App\Http\Controllers\QuizController;
use App\Models\Quiz;
use Database\Seeders\EmployeeSeeder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


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
Route::post('/dev/logout', [AdminAuthController::class, 'logout'])->name('dev.logout');

// Redirect old dev login to unified login
Route::get('/dev/login', function () {
    return redirect()->route('admin.login');
});

Route::prefix('dev')->name('dev.')->middleware(['dev.auth', 'nocache'])->group(function () {
    Route::get('/', [DevController::class, 'health'])->name('health');
    Route::get('/health.json', [DevController::class, 'healthJson'])->name('health.json');
});

Route::get('/health/zip', function () {
    if (! app()->environment('local')) {
        abort(404);
    }

    return response()->json([
        'ziparchive' => class_exists('ZipArchive'),
        'extension_loaded_zip' => extension_loaded('zip'),
        'gd_imagecreatefromstring' => function_exists('imagecreatefromstring'),
        'extension_loaded_gd' => extension_loaded('gd'),
        'php_version' => PHP_VERSION,
        'sapi' => PHP_SAPI,
        'ini_loaded' => php_ini_loaded_file(),
    ]);
});

// Quiz Engine Routes for Participants
Route::prefix('quiz')->name('quiz.')->middleware('nocache')->group(function () {

    // Form to join the quiz (Enter Name & NIM)
    Route::get('{quiz:slug}', [QuizController::class, 'showJoinForm'])->name('join');

    // Process joining the quiz
    Route::post('{quiz:slug}/join', [QuizController::class, 'joinQuiz'])->name('join.process');

    // Page to take the quiz
    Route::get('{quiz:slug}/participant/{participant}/take', [QuizController::class, 'takeQuiz'])->name('take');

    // Submit Answers & Auto-Scoring Route
    Route::post('{quiz:slug}/participant/{participant}/answers', [QuizController::class, 'storeAnswer'])
        ->name('storeAnswer');

    // Autosave single answer
    Route::post('{quiz:slug}/participant/{participant}/autosave', [QuizController::class, 'autosaveAnswer'])
        ->name('autosave');

    // Result page after submission
    Route::get('{quiz:slug}/participant/{participant}/result', [QuizController::class, 'showResult'])->name('result');
});

// Admin Management & Dashboard Routes
Route::prefix('admin')->name('admin.')->middleware(['admin.auth', 'nocache'])->group(function () {
    // Quiz Management (CRUD)
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');
    Route::get('quizzes', [AdminController::class, 'index'])->name('quizzes.index');
    Route::get('quizzes/import', [AdminController::class, 'import'])->name('quizzes.import');
    Route::post('quizzes/import', [AdminController::class, 'importStore'])->name('quizzes.import.store');
    Route::get('quizzes/create', [AdminController::class, 'create'])->name('quizzes.create');
    Route::post('quizzes', [AdminController::class, 'store'])->name('quizzes.store');

    // AI Quiz Generation Routes
    Route::get('ai-quiz/create', [AiQuizController::class, 'aiCreate'])->name('quizzes.ai-create');
    Route::get('ai-quiz/models', [AiQuizController::class, 'aiModels'])->name('quizzes.ai-models');
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

    // Export PDF Route
    Route::get('quiz/{quiz:slug}/export-pdf', [PdfExportController::class, 'export'])
        ->name('quiz.export-pdf');

    // AI Insight Route
    Route::post('quiz/{quiz:slug}/ai-insights', [AiInsightController::class, 'generate'])
        ->name('quiz.ai-insights');

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

    // Quiz Session Management
    Route::post('quizzes/{quiz:slug}/sessions', [AdminController::class, 'storeSession'])->name('quizzes.sessions.store');
    Route::delete('sessions/{session}', [AdminController::class, 'destroySession'])->name('sessions.destroy');
    Route::post('sessions/{session}/assign', [AdminController::class, 'assignParticipants'])->name('sessions.assign');

});

Route::get('force-seed', function (Request $request) {
    // Security: Allow in local or with a specific token in production
    $token = $request->query('token');
    $secret = 'PahamAjaSeed2026';

    if (! app()->environment('local') && $token !== $secret) {
        abort(403, 'Unauthorized seed attempt.');
    }

    // DELETED destructive truncate logic to protect Quiz, Question, Participant, and Employee data.
    // The seeder now uses updateOrCreate() for total safety.
    if ($request->query('clear')) {
        // Achievement is the only 'safe' thing to reset if absolutely requested.
        \App\Models\Achievement::truncate();
    }

    try {
        // Run full database seeder
        (new \Database\Seeders\DatabaseSeeder)->run();

        return response()->json([
            'status' => 'success',
            'message' => 'PahamAja dashboard SAFE-SYNC completed. No data was deleted.',
            'environment' => app()->environment(),
            'cleared_metadata' => (bool) $request->query('clear'),
        ]);
    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error("FORCE-SEED FAILED: " . $e->getMessage());
        return response()->json([
            'status' => 'partial_success_or_error',
            'error_message' => $e->getMessage(),
            'message' => 'The sync process encountered a technical error but may have partially succeeded. Check the dashboard.',
        ], 500);
    }
})->name('force-seed');
