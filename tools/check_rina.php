<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Employee;

$emp = Employee::where('name', 'RINA TRIANI USU')->first();
if ($emp) {
    echo "RINA TRIANI USU DATA:\n";
    echo "NIM: " . $emp->nim . "\n";
    echo "Position: " . $emp->position . "\n";
    echo "Dept: " . $emp->department . "\n";
} else {
    echo "RINA TRIANI USU NOT FOUND\n";
}

$all = Employee::count();
$attd = Employee::where('position', 'ATTD')->count();
echo "Total Employees: $all\n";
echo "Total ATTD: $attd\n";
