<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "Connecting to database...\n";
    $connected = DB::connection()->getPdo();
    echo "Connected successfully!\n";
    
    $tables = DB::select("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'");
    echo "Tables found: " . count($tables) . "\n";
    foreach ($tables as $table) {
        echo "- " . $table->table_name . "\n";
    }
    
    $quizCount = DB::table('quizzes')->count();
    echo "Quizzes: " . $quizCount . "\n";

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
