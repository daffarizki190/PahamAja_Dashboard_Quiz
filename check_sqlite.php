<?php

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

$app->make(Kernel::class)->bootstrap();

try {
    $quizzes = DB::connection('sqlite')->table('quizzes')->get();
    echo 'Found '.count($quizzes)." quizzes in SQLite.\n";
    foreach ($quizzes as $q) {
        echo ' - '.($q->title ?? 'No Title').' (Slug: '.($q->slug ?? 'no-slug').")\n";
    }
} catch (Exception $e) {
    echo 'Error connecting to SQLite: '.$e->getMessage()."\n";
}
