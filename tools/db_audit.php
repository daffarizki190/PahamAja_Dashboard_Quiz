<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Quiz;
use App\Models\Employee;
use App\Models\Participant;

echo "--- DATABASE AUDIT ---\n";
echo "Total Quizzes: " . Quiz::count() . "\n";
echo "Quizzes: " . Quiz::pluck('title')->implode(', ') . "\n";
echo "Total Employees: " . Employee::count() . "\n";
echo "Employees with History: " . Participant::whereNotNull('score')->distinct('nim')->count() . " unique individuals\n";
echo "Rizal Maulana NIK: " . (Employee::where('name', 'RIZAL MAULANA')->first()->nim ?? 'NOT FOUND') . "\n";
echo "--- END AUDIT ---\n";
