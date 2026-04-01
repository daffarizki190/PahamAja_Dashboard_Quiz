<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "--- CHECKING SQLITE ---\n";
try {
    $sqlitePath = database_path('database.sqlite');
    // Forcing path manually to avoid .env override
    config(['database.connections.recovery_sqlite' => [
        'driver' => 'sqlite',
        'database' => $sqlitePath,
        'prefix' => '',
    ]]);
    
    $quizzes = DB::connection('recovery_sqlite')->table('quizzes')->get();
    echo "Found " . count($quizzes) . " quizzes in database.sqlite.\n";
    foreach ($quizzes as $q) {
        echo " - " . ($q->title ?? 'Untitled') . " (ID: " . ($q->id ?? '?') . ")\n";
    }
} catch (\Exception $e) {
    echo "SQLite Check Failed: " . $e->getMessage() . "\n";
}

echo "\n--- CHECKING MONGODB ---\n";
try {
    // Check if MongoDB collection exists and has data
    // Using raw manager if connection fails
    $mongoConn = DB::connection('mongodb');
    $quizzes = $mongoConn->table('quizzes')->get();
    echo "Found " . count($quizzes) . " quizzes in MongoDB.\n";
    foreach ($quizzes as $q) {
        $title = $q['title'] ?? ($q['name'] ?? 'Untitled');
        echo " - " . $title . " (ID: " . (string)$q['_id'] . ")\n";
    }
} catch (\Exception $e) {
    echo "MongoDB Check Failed: " . $e->getMessage() . "\n";
}

echo "\n--- CHECKING CURRENT PGSQL (SUPABASE) ---\n";
try {
    $quizzes = DB::connection('pgsql')->table('quizzes')->get();
    echo "Found " . count($quizzes) . " quizzes in PGSQL.\n";
    foreach ($quizzes as $q) {
        echo " - " . ($q->title ?? 'Untitled') . " (ID: " . ($q->id ?? '?') . ")\n";
    }
} catch (\Exception $e) {
    echo "PGSQL Check Failed: " . $e->getMessage() . "\n";
}
