<?php

namespace App\Exports;

use App\Models\Participant;
use App\Models\Quiz;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class QuizExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithTitle, WithEvents
{
    protected $quiz;

    public function __construct(Quiz $quiz)
    {
        $this->quiz = $quiz;
    }

    public function collection()
    {
        $participants = Participant::where('quiz_id', $this->quiz->id)
            ->whereNotNull('score')
            ->orderByDesc('score')
            ->get();

        // Consolidate to unique attempts (highest score first)
        $unique = $participants->groupBy(fn($p) => $p->employee_id ?? $p->nim)->map(fn($g) => $g->first());

        $data = collect();
        foreach ($unique->values() as $index => $p) {
            $status = $p->score >= $this->quiz->passing_score ? 'LULUS' : 'TIDAK LULUS';
            $data->push([
                $index + 1,
                $p->name,
                "'" . $p->nim,
                $p->location ?? '-',
                $p->score,
                $p->updated_at ? $p->updated_at->format('d/m/Y H:i') : '-',
                $status
            ]);
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            ['LAPORAN HASIL KUIS PAHAMAJA'],
            ['Judul Kuis:', $this->quiz->title],
            ['Passing Score:', $this->quiz->passing_score . '%'],
            ['Total Peserta:', $this->collection()->count() . ' Orang'],
            [''],
            ['No', 'Nama Lengkap', 'NIK / NIM', 'Lokasi', 'Skor Akhir', 'Waktu Selesai', 'Status Kelulusan']
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Title Styling
            1    => ['font' => ['bold' => true, 'size' => 16, 'color' => ['argb' => 'FFFFFFFF']]],
            2    => ['font' => ['bold' => true]],
            3    => ['font' => ['bold' => true]],
            4    => ['font' => ['bold' => true]],
            
            // Table Header Styling
            6    => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'color' => ['argb' => 'FF4F46E5'] // Indigo color
                ],
            ],
        ];
    }

    public function title(): string
    {
        return 'Laporan Kuis';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                $highestCol = 'G'; // A to G

                // Merge Title Row and style background
                $sheet->mergeCells('A1:G1');
                $sheet->getStyle('A1:G1')->getFill()
                      ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                      ->getStartColor()->setARGB('FF7C3AED'); // Purple color
                $sheet->getStyle('A1:G1')->getAlignment()->setHorizontal('center');

                // Apply Borders to the Table Data
                if ($highestRow >= 6) {
                    $sheet->getStyle('A6:' . $highestCol . $highestRow)->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                'color' => ['argb' => 'FFCCCCCC'],
                            ],
                        ],
                    ]);
                    
                    // Align Center for No, Skor, Status
                    $sheet->getStyle('A6:A' . $highestRow)->getAlignment()->setHorizontal('center');
                    $sheet->getStyle('E6:E' . $highestRow)->getAlignment()->setHorizontal('center');
                    $sheet->getStyle('G6:G' . $highestRow)->getAlignment()->setHorizontal('center');
                    
                    // Conditional Formatting for Status
                    for ($row = 7; $row <= $highestRow; $row++) {
                        $statusValue = $sheet->getCell('G' . $row)->getValue();
                        if ($statusValue === 'LULUS') {
                            $sheet->getStyle('G' . $row)->getFont()->getColor()->setARGB('FF10B981'); // Emerald
                            $sheet->getStyle('G' . $row)->getFont()->setBold(true);
                        } elseif ($statusValue === 'TIDAK LULUS') {
                            $sheet->getStyle('G' . $row)->getFont()->getColor()->setARGB('FFEF4444'); // Red
                            $sheet->getStyle('G' . $row)->getFont()->setBold(true);
                        }
                    }
                }
            },
        ];
    }
}
