<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Employee;

$employees = Employee::orderBy('id')->get(['name', 'nim']);

echo "=== EMPLOYEE NIK VERIFICATION ===\n";
foreach ($employees as $e) {
    echo str_pad($e->name, 30) . " | " . str_pad($e->nim, 15) . " | " . $e->position . "\n";
}
echo "Total: " . $employees->count() . "\n";
