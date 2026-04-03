<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

$app->make(Kernel::class)->bootstrap();

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;

$quiz = DB::connection('mongodb')->table('quizzes')->first();
echo 'Quiz Object Keys: '.implode(', ', array_keys((array) $quiz))."\n";
if (isset($quiz->_id)) {
    echo "_id exists\n";
}
if (isset($quiz->id)) {
    echo "id exists\n";
}
echo 'Full Object: '.json_encode($quiz)."\n";
