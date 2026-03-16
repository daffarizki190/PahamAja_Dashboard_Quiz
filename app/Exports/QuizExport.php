<?php

namespace App\Exports;

use App\Models\Participant;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class QuizExport implements FromCollection, WithHeadings, WithMapping
{
    protected $quizId;

    protected $passingScore;

    public function __construct($quizId, $passingScore)
    {
        $this->quizId = $quizId;
        $this->passingScore = $passingScore;
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        return Participant::where('quiz_id', $this->quizId)
            ->whereNotNull('score')
            ->orderBy('score', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Nama Peserta',
            'NIM',
            'Skor',
            'Waktu Pengerjaan',
            'Status (Lulus/Tidak Lulus)',
        ];
    }

    /**
     * @var Participant
     */
    public function map($participant): array
    {
        return [
            $participant->name,
            $participant->nim,
            $participant->score,
            $participant->updated_at->format('Y-m-d H:i:s'),
            $participant->score >= $this->passingScore ? 'Lulus' : 'Tidak Lulus',
        ];
    }
}
