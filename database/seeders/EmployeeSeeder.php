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
            ['name' => 'RIZAL MAULANA', 'position' => 'CPM', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
            ['name' => 'RINA TRIANI USU', 'position' => 'ADM', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
            ['name' => 'ANISA PUTRI MISKIYAH', 'position' => 'ADM', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
            ['name' => 'IRVANDI MAULANA', 'position' => 'IT', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
            ['name' => 'MUHAMMAD AKMAL FERUZI', 'position' => 'SPV', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
            ['name' => 'AKHMAD NURYAMIN', 'position' => 'SPV', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
            ['name' => 'AMIN MUSLIM', 'position' => 'LDR', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
            ['name' => 'SUSMANTO', 'position' => 'LDR', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
            ['name' => 'DAFFA RIZKI ARIYANTO', 'position' => 'LDR', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
            ['name' => 'MUHAMMAD ALI AKBAR', 'position' => 'LDR', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
            ['name' => 'FADHUR ROHMAN', 'position' => 'CRO', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
            ['name' => 'AKHSANTI NAFA HAFIDZA YAMIN', 'position' => 'CRO', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
            ['name' => 'KEMAL BUDI FARAZZAN', 'position' => 'CRO', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
            ['name' => 'MARTGU PARDEDE', 'position' => 'CRO', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
            ['name' => 'MEYLIA', 'position' => 'CRO', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
            ['name' => 'MUCHAMAD KAMALUDIN', 'position' => 'CRO', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
            ['name' => 'MUHAMMAD RAFID PUTRA ANSAR', 'position' => 'CRO', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
            ['name' => 'NURUL AULIA', 'position' => 'CRO', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
            ['name' => 'SEPTIAN NUR HADI', 'position' => 'CRO', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
            ['name' => 'YAZID RIKIE RAMADHAN', 'position' => 'CRO', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
            ['name' => 'ALFAER GUSTI IMAM PRASETYO', 'position' => 'ATD', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
            ['name' => 'ANDO FERDIANSAH', 'position' => 'ATD', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
            ['name' => 'DAVID ARYA SYAHPUTRA', 'position' => 'ATD', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
            ['name' => 'DILAN ALFAUL MAJID', 'position' => 'ATD', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
            ['name' => 'DIMAS SATRIO ARADEA', 'position' => 'ATD', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
            ['name' => 'JERI', 'position' => 'ATD', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
            ['name' => 'KARAN NUGROHO', 'position' => 'ATD', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
            ['name' => 'M ASAH KURNIAWAN', 'position' => 'ATD', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
            ['name' => 'M ZAINAL MUTAQIN', 'position' => 'ATD', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
            ['name' => 'MUCSAL', 'position' => 'ATD', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
            ['name' => 'MUHAMAD PARIS', 'position' => 'ATD', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
            ['name' => 'MUHAMAD RIDWAN', 'position' => 'ATD', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
            ['name' => 'MUHAMMAD NURALAMSYAH', 'position' => 'ATD', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
            ['name' => 'MUHLIS', 'position' => 'ATD', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],
            ['name' => 'RIZKI ALDITIA', 'position' => 'ATD', 'dept' => 'PT. CENTREPARK CITRA CORPORA'],

        ];

        foreach ($employees as $index => $data) {
            Employee::updateOrCreate(
                ['name' => $data['name']], // Unique check
                [
                    'nim' => 'P-'.str_pad($index + 1, 2, '0', STR_PAD_LEFT),
                    'department' => $data['dept'],
                    'position' => $data['position'],
                    'status' => 'Active',
                ]
            );
        }
    }
}
