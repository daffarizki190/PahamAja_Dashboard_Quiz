<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Employee;
use Illuminate\Support\Facades\DB;

set_time_limit(300); // 5 minutes

echo "CONNECTING TO: " . config('database.connections.pgsql.host') . "\n";

$rina = Employee::where('name', 'RINA TRIANI USU')->first();
echo "BEFORE UPDATE -> RINA NIM: " . ($rina->nim ?? 'N/A') . " (Position: " . ($rina->position ?? 'N/A') . ")\n";

$mappings = [
    'RINA TRIANI USU' => '01-2022070248',
    'ANISA PUTRI MISKIYAH' => '01-2024010152',
    'IRVANDI MAULANA' => '01-2024010341',
    'MUHAMMAD AKMAL FERUZI' => '01-2024010336',
    'AKHMAD NURYAMIN' => '01-2024010342',
    'MUHAMMAD RAFID PUTRA ANSAR' => '01-2023120273',
    'MUHAMAD RIDWAN' => '01-2024030017',
    'MUHLIS' => '01-2024010345',
];

foreach ($mappings as $name => $nim) {
    Employee::where('name', $name)->update(['nim' => $nim]);
}

Employee::where('position', 'ATD')->update(['position' => 'ATTD']);

$rina = Employee::where('name', 'RINA TRIANI USU')->first();
echo "AFTER UPDATE -> RINA NIM: " . ($rina->nim ?? 'N/A') . " (Position: " . ($rina->position ?? 'N/A') . ")\n";

$attdCount = Employee::where('position', 'ATTD')->count();
echo "Final ATTD Count: $attdCount\n";
