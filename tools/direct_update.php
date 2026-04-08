<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Employee;
use Illuminate\Support\Facades\DB;

echo "Starting Direct Database Update...\n";

$mappings = [
    'RIZAL MAULANA' => '2023070292',
    'RINA TRIANI USU' => '01-2022070248',
    'ANISA PUTRI MISKIYAH' => '01-2024010152',
    'IRVANDI MAULANA' => '01-2024010341',
    'MUHAMMAD AKMAL FERUZI' => '01-2024010336',
    'AKHMAD NURYAMIN' => '01-2024010342',
    'AMIN MUSLIM' => '01-2024010337',
    'SUSMANTO' => '01-2023010563',
    'DAFFA RIZKI ARIYANTO' => '01-2024060107',
    'MUHAMMAD ALI AKBAR' => '01-2023050326',
    'FADHUR ROHMAN' => '01-2023080364',
    'AKHSANTI NAFA HAFIDZA YAMIN' => '01-2025070064',
    'KEMAL BUDI FARAZZAN' => '01-2024030016',
    'MARTGU PARDEDE' => '01-2024010333',
    'MEYLIA' => '01-2024020261',
    'MUCHAMAD KAMALUDIN' => '01-2024010335',
    'MUHAMMAD RAFID PUTRA ANSAR' => '01-2023120273',
    'NURUL AULIA' => '01-2023010568',
    'SEPTIAN NUR HADI' => '01-2024030014',
    'YAZID RIKIE RAMADHAN' => '01-2023010392',
    'ALFAER GUSTI IMAM PRASETYO' => '01-2025020294',
    'ANDO FERDIANSAH' => '01-2024030015',
    'DAVID ARYA SYAHPUTRA' => '01-2025070102',
    'DILAN ALFAUL MAJID' => '01-2025100199',
    'DIMAS SATRIO ARADEA' => '01-2024040033',
    'JERI' => '01-2024010340',
    'KARAN NUGROHO' => '01-2024120342',
    'M ASAH KURNIAWAN' => '01-2024120343',
    'M ZAINAL MUTAQIN' => '01-2025120089',
    'MUCSAL' => '01-2024010343',
    'MUHAMAD PARIS' => '01-2023080339',
    'MUHAMAD RIDWAN' => '01-2024030017',
    'MUHAMMAD NURALAMSYAH' => '01-2026010239',
    'MUHLIS' => '01-2024010345',
    'RIZKI ALDITIA' => '01-2023110234',
];

try {
    DB::beginTransaction();
    
    foreach ($mappings as $name => $nim) {
        $updated = Employee::where('name', $name)->update(['nim' => $nim]);
        if ($updated) {
            echo "Updated $name -> $nim\n";
        } else {
            echo "Warning: $name not found or NIM already set.\n";
        }
    }

    // Update positions
    $atdCount = Employee::where('position', 'ATD')->count();
    $updatedAtd = Employee::where('position', 'ATD')->update(['position' => 'ATTD']);
    echo "Updated $updatedAtd positions from ATD to ATTD (was $atdCount)\n";

    DB::commit();
    echo "Transaction Committed Successfully!\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "Error: " . $e->getMessage() . "\n";
}
