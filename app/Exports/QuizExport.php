<?php

namespace App\Exports;

use App\Models\Participant;
use App\Models\Quiz;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class QuizExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
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

        $avgScore = $participants->avg('score') ?? 0;
        $passed = $participants->where('score', '>=', $this->quiz->passing_score)->count();
        $failed = $participants->count() - $passed;

        // Build a collection with Summary Header first
        $data = collect([
            ['RINGKASAN LAPORAN KUIS'],
            ['Judul Kuis', $this->quiz->title],
            ['Passing Score', $this->quiz->passing_score . '%'],
            ['Total Peserta Selesai', $participants->count()],
            ['Rata-rata Nilai', number_format($avgScore, 1) . '%'],
            ['Lulus', $passed],
            ['Tidak Lulus', $failed],
            [''], // Spacer
            ['DAFTAR DETAIL PESERTA'],
            ['No', 'Nama Peserta', 'NIM', 'Skor', 'Pekerjaan Selesai Pada', 'Status']
        ]);

        foreach ($participants as $index => $p) {
            $data->push([
                $index + 1,
                $p->name,
                $p->nim,
                $p->score,
                $p->updated_at->format('Y-m-d H:i:s'),
                $p->score >= $this->quiz->passing_score ? 'Lulus' : 'Tidak Lulus'
            ]);
        }

        return $data;
    }

    public function headings(): array
    {
        return []; // Headings are part of the collection for custom layout
    }

    public function map($row): array
    {
        return $row;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'B' => 30,
            'C' => 20,
            'D' => 15,
            'E' => 25,
            'F' => 15,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4F46E5'], 'font' => ['color' => ['rgb' => 'FFFFFF']]]],
            2 => ['font' => ['bold' => true]],
            3 => ['font' => ['bold' => true]],
            4 => ['font' => ['bold' => true]],
            5 => ['font' => ['bold' => true]],
            6 => ['font' => ['bold' => true]],
            7 => ['font' => ['bold' => true]],
            9 => ['font' => ['bold' => true, 'size' => 12]],
            10 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '1E293B']]
            ],
        ];
    }
}
