<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

$app->make(Kernel::class)->bootstrap();

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;

$quizzes = DB::connection('pgsql')->table('quizzes')->pluck('title');
echo "Current Quizzes in PGSQL:\n";
foreach ($quizzes as $q) {
    echo " - $q\n";
}
echo 'Total: '.count($quizzes)."\n";
