<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$answer = DB::connection('mongodb')->table('answers')->first();
echo "Answer Object: " . json_encode($answer) . "\n";

$participant = DB::connection('mongodb')->table('participants')->first();
echo "Participant Object: " . json_encode($participant) . "\n";
