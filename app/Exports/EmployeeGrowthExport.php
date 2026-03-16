<?php

namespace App\Exports;

use App\Models\Employee;
use App\Models\Participant;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EmployeeGrowthExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $employees = Employee::latest()->get();
        $participations = Participant::whereNotNull('score')
            ->get(['nim', 'score', 'updated_at']);

        $statsByNim = $participations
            ->groupBy('nim')
            ->map(function ($items) {
                $sorted = $items->sortBy(function ($p) {
                    return $p->updated_at?->getTimestamp() ?? 0;
                })->values();

                $scores = $sorted->pluck('score')->filter(function ($s) {
                    return ! is_null($s);
                })->values();

                $attempts = $scores->count();
                $avg = $attempts > 0 ? round($scores->avg(), 1) : 0.0;
                $last = $attempts > 0 ? (float) $scores->last() : null;
                $prev = $attempts > 1 ? (float) $scores->get($attempts - 2) : null;
                $delta = (! is_null($last) && ! is_null($prev)) ? round($last - $prev, 1) : null;
                $lastAt = $sorted->last()?->updated_at;

                return [
                    'attempts' => $attempts,
                    'avg' => $avg,
                    'last' => $last,
                    'delta' => $delta,
                    'last_at' => $lastAt,
                ];
            });

        $rows = $employees
            ->map(function (Employee $employee) use ($statsByNim) {
                $stats = $statsByNim->get($employee->nim, [
                    'attempts' => 0,
                    'avg' => 0.0,
                    'last' => null,
                    'delta' => null,
                    'last_at' => null,
                ]);

                return [
                    (string) $employee->name,
                    (string) $employee->nim,
                    (string) $employee->department,
                    (string) $employee->position,
                    (int) $stats['attempts'],
                    (float) $stats['avg'],
                    $stats['last'],
                    $stats['delta'],
                    $stats['last_at']?->format('Y-m-d H:i:s'),
                ];
            })
            ->sortByDesc(function ($row) {
                return $row[5] ?? 0;
            })
            ->values();

        return new Collection($rows);
    }

    public function headings(): array
    {
        return [
            'Nama',
            'NIM',
            'Department',
            'Position',
            'Attempts',
            'Average Score',
            'Last Score',
            'Delta',
            'Last Attempt At',
        ];
    }
}
