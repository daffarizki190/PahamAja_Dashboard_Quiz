<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$quizzes = DB::connection('pgsql')->table('quizzes')->pluck('title');
echo "Current Quizzes in PGSQL:\n";
foreach ($quizzes as $q) {
    echo " - $q\n";
}
echo "Total: " . count($quizzes) . "\n";
