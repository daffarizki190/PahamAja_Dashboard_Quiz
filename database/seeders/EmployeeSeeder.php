<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employees = [
            ['name' => 'RIZAL MAULANA', 'nim' => '01-2023070292', 'position' => 'CPM', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
            ['name' => 'RINA TRIANI USU', 'nim' => '01-2022070248', 'position' => 'ADM', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
            ['name' => 'ANISA PUTRI MISKIYAH', 'nim' => '01-2024010152', 'position' => 'ADM', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
            ['name' => 'IRVANDI MAULANA', 'nim' => '01-2024010341', 'position' => 'IT', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
            ['name' => 'MUHAMMAD AKMAL FERUZI', 'nim' => '01-2024010336', 'position' => 'SPV', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
            ['name' => 'AKHMAD NURYAMIN', 'nim' => '01-2024010342', 'position' => 'SPV', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
            ['name' => 'AMIN MUSLIM', 'nim' => '01-2024010337', 'position' => 'LDR', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
            ['name' => 'SUSMANTO', 'nim' => '01-2023010563', 'position' => 'LDR', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
            ['name' => 'DAFFA RIZKI ARIYANTO', 'nim' => '01-2024060107', 'position' => 'LDR', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
            ['name' => 'MUHAMMAD ALI AKBAR', 'nim' => '01-2023050326', 'position' => 'LDR', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
            ['name' => 'FADHUR ROHMAN', 'nim' => '01-2023080364', 'position' => 'CRO', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
            ['name' => 'AKHSANTI NAFA HAFIDZA YAMIN', 'nim' => '01-2025070064', 'position' => 'CRO', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
            ['name' => 'KEMAL BUDI FARAZZAN', 'nim' => '01-2024030016', 'position' => 'CRO', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
            ['name' => 'MARTGU PARDEDE', 'nim' => '01-2024010333', 'position' => 'CRO', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
            ['name' => 'MEYLIA', 'nim' => '01-2024020261', 'position' => 'CRO', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
            ['name' => 'MUCHAMAD KAMALUDIN', 'nim' => '01-2024010335', 'position' => 'CRO', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
            ['name' => 'MUHAMMAD RAFID PUTRA ANSAR', 'nim' => '01-2023120273', 'position' => 'CRO', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
            ['name' => 'NURUL AULIA', 'nim' => '01-2023010568', 'position' => 'CRO', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
            ['name' => 'SEPTIAN NUR HADI', 'nim' => '01-2024030014', 'position' => 'CRO', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
            ['name' => 'YAZID RIKIE RAMADHAN', 'nim' => '01-2023010392', 'position' => 'CRO', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
            ['name' => 'ALFAER GUSTI IMAM PRASETYO', 'nim' => '01-2025020294', 'position' => 'ATTD', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
            ['name' => 'ANDO FERDIANSAH', 'nim' => '01-2024030015', 'position' => 'ATTD', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
            ['name' => 'DAVID ARYA SYAHPUTRA', 'nim' => '01-2025070102', 'position' => 'ATTD', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
            ['name' => 'DILAN ALFAUL MAJID', 'nim' => '01-2025100199', 'position' => 'ATTD', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
            ['name' => 'DIMAS SATRIO ARADEA', 'nim' => '01-2024040033', 'position' => 'ATTD', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
            ['name' => 'JERI', 'nim' => '01-2024010340', 'position' => 'ATTD', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
            ['name' => 'KARAN NUGROHO', 'nim' => '01-2024120342', 'position' => 'ATTD', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
            ['name' => 'M ASAH KURNIAWAN', 'nim' => '01-2024120343', 'position' => 'ATTD', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
            ['name' => 'M ZAINAL MUTAQIN', 'nim' => '01-2025120089', 'position' => 'ATTD', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
            ['name' => 'MUCSAL', 'nim' => '01-2024010343', 'position' => 'ATTD', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
            ['name' => 'MUHAMAD PARIS', 'nim' => '01-2023080339', 'position' => 'ATTD', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
            ['name' => 'MUHAMAD RIDWAN', 'nim' => '01-2024030017', 'position' => 'ATTD', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
            ['name' => 'MUHAMMAD NURALAMSYAH', 'nim' => '01-2026010239', 'position' => 'ATTD', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
            ['name' => 'MUHLIS', 'nim' => '01-2024010345', 'position' => 'ATTD', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
            ['name' => 'RIZKI ALDITIA', 'nim' => '01-2023110234', 'position' => 'ATTD', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
        ];

        foreach ($employees as $index => $data) {
            $nim = $data['nim'] ?? 'P-'.str_pad($index + 1, 2, '0', STR_PAD_LEFT);
            
            Employee::updateOrCreate(
                ['name' => $data['name']], // Unique check by name
                [
                    'nim' => $nim,
                    'department' => $data['dept'],
                    'position' => $data['position'],
                    'status' => 'Active',
                ]
            );
        }
    }
}
