<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

$app->make(Kernel::class)->bootstrap();

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;

echo "--- MONGODB DATA CHECK ---\n";
try {
    $pCount = DB::connection('mongodb')->table('participants')->count();
    $aCount = DB::connection('mongodb')->table('answers')->count();
    $eCount = DB::connection('mongodb')->table('employees')->count();

    echo "Participants: $pCount\n";
    echo "Answers: $aCount\n";
    echo "Employees: $eCount\n";

    if ($pCount > 0) {
        $sampleP = DB::connection('mongodb')->table('participants')->first();
        echo 'Sample Participant: '.json_encode($sampleP)."\n";
    }
} catch (Exception $e) {
    echo 'Error: '.$e->getMessage()."\n";
}
