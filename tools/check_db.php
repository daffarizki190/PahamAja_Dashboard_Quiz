<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Quiz;
use App\Models\Question;
use App\Models\Participant;
use App\Models\Employee;

try {
    echo "Quizzes: " . Quiz::count() . "\n";
    echo "Questions: " . Question::count() . "\n";
    echo "Employees: " . Employee::count() . "\n";
    echo "Participants: " . Participant::count() . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
