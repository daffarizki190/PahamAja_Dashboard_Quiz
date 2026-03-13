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
        // Clear existing employees
        foreach(\App\Models\Employee::all() as $e) {
            $e->delete();
        }

        $employees = [
            ['name' => 'RIZAL MAULANA', 'position' => 'CPM', 'dept' => 'Management'],
            ['name' => 'RINA TRIANI USU', 'position' => 'ADM', 'dept' => 'Administration'],
            ['name' => 'ANISA PUTRI MISKIYAH', 'position' => 'ADM', 'dept' => 'Administration'],
            ['name' => 'IRVANDI MAULANA', 'position' => 'IT', 'dept' => 'Technical'],
            ['name' => 'MUHAMMAD AKMAL FERUZI', 'position' => 'SPV', 'dept' => 'Supervisory'],
            ['name' => 'AKHMAD NURYAMIN', 'position' => 'SPV', 'dept' => 'Supervisory'],
            ['name' => 'AMIN MUSLIM', 'position' => 'LDR', 'dept' => 'Leadership'],
            ['name' => 'SUSMANTO', 'position' => 'LDR', 'dept' => 'Leadership'],
            ['name' => 'DAFFA RIZKI ARIYANTO', 'position' => 'LDR', 'dept' => 'Leadership'],
            ['name' => 'MUHAMMAD ALI AKBAR', 'position' => 'LDR', 'dept' => 'Leadership'],
            ['name' => 'FADHUR ROHMAN', 'position' => 'CRO', 'dept' => 'Operations'],
            ['name' => 'AKHSANTI NAFA HAFIDZA YAMIN', 'position' => 'CRO', 'dept' => 'Operations'],
            ['name' => 'KEMAL BUDI FARAZZAN', 'position' => 'CRO', 'dept' => 'Operations'],
            ['name' => 'MARTGU PARDEDE', 'position' => 'CRO', 'dept' => 'Operations'],
            ['name' => 'MEYLIA', 'position' => 'CRO', 'dept' => 'Operations'],
            ['name' => 'MUCHAMAD KAMALUDIN', 'position' => 'CRO', 'dept' => 'Operations'],
            ['name' => 'MUHAMMAD RAFID PUTRA ANSAR', 'position' => 'CRO', 'dept' => 'Operations'],
            ['name' => 'NURUL AULIA', 'position' => 'CRO', 'dept' => 'Operations'],
            ['name' => 'SEPTIAN NUR HADI', 'position' => 'CRO', 'dept' => 'Operations'],
            ['name' => 'YAZID RIKIE RAMADHAN', 'position' => 'CRO', 'dept' => 'Operations'],
            ['name' => 'ALFAER GUSTI IMAM PRASETYO', 'position' => 'ATD', 'dept' => 'Attendant'],
            ['name' => 'ANDO FERDIANSAH', 'position' => 'ATD', 'dept' => 'Attendant'],
            ['name' => 'DAVID ARYA SYAHPUTRA', 'position' => 'ATD', 'dept' => 'Attendant'],
            ['name' => 'DILAN ALFAUL MAJID', 'position' => 'ATD', 'dept' => 'Attendant'],
            ['name' => 'DIMAS SATRIO ARADEA', 'position' => 'ATD', 'dept' => 'Attendant'],
            ['name' => 'JERI', 'position' => 'ATD', 'dept' => 'Attendant'],
            ['name' => 'KARAN NUGROHO', 'position' => 'ATD', 'dept' => 'Attendant'],
            ['name' => 'M ASAH KURNIAWAN', 'position' => 'ATD', 'dept' => 'Attendant'],
            ['name' => 'M ZAINAL MUTAQIN', 'position' => 'ATD', 'dept' => 'Attendant'],
            ['name' => 'MUCSAL', 'position' => 'ATD', 'dept' => 'Attendant'],
            ['name' => 'MUHAMAD PARIS', 'position' => 'ATD', 'dept' => 'Attendant'],
            ['name' => 'MUHAMAD RIDWAN', 'position' => 'ATD', 'dept' => 'Attendant'],
            ['name' => 'MUHAMMAD NURALAMSYAH', 'position' => 'ATD', 'dept' => 'Attendant'],
            ['name' => 'MUHLIS', 'position' => 'ATD', 'dept' => 'Attendant'],
            ['name' => 'RIZKI ALDITIA', 'position' => 'ATD', 'dept' => 'Attendant'],
        ];

        foreach ($employees as $index => $data) {
            Employee::create([
                'name' => $data['name'],
                'nim' => 'P-' . str_pad($index + 1, 2, '0', STR_PAD_LEFT), // Unique placeholder as requested
                'department' => $data['dept'],
                'position' => $data['position'],
                'status' => 'Active',
            ]);
        }
    }
}
