<?php

namespace App\Exports;

use App\Models\Participant;
use App\Models\Quiz;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GlobalParticipantExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function collection()
    {
        return Participant::with(['quiz', 'employee'])
            ->whereNotNull('score')
            ->orderBy('quiz_id')
            ->orderByDesc('score')
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID Peserta',
            'Nama Karyawan',
            'NIM',
            'Judul Kuis',
            'Skor',
            'Status',
            'Percobaan Ke-',
            'Mulai',
            'Selesai',
            'Durasi',
        ];
    }

    /**
     * @param Participant $participant
     */
    public function map($participant): array
    {
        $duration = $participant->duration ?? '-';

        $status = ($participant->score >= ($participant->quiz->passing_score ?? 70)) ? 'LULU' : 'TIDAK LULUS';

        return [
            $participant->id,
            $participant->name,
            $participant->nim,
            $participant->quiz->title ?? 'N/A',
            $participant->score,
            $status,
            $participant->attempt,
            $participant->started_at?->format('d/m/Y H:i') ?? '-',
            $participant->finished_at?->format('d/m/Y H:i') ?? '-',
            $duration,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'E2E8F0']]],
        ];
    }
}
