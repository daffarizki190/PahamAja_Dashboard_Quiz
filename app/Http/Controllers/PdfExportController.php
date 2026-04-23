<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfExportController extends Controller
{
    public function export(Quiz $quiz)
    {
        $allParticipants = $quiz->participants()
            ->whereNotNull('score')
            ->get();

        // 🟢 Best Score Consolidation for PDF
        $participants = $allParticipants
            ->groupBy('employee_id')
            ->map(function ($group) {
                return $group->sort(function ($a, $b) {
                    if ($a->score !== $b->score) return $b->score <=> $a->score;
                    return $b->updated_at <=> $a->updated_at;
                })->first();
            })
            ->sortByDesc('score')
            ->values();

        $finished = $participants;
        $avgScore = round($finished->avg('score') ?? 0, 1);
        $passed = $finished->where('score', '>=', $quiz->passing_score)->count();
        $failed = $finished->where('score', '<', $quiz->passing_score)->count();
        $passRate = $finished->count() > 0 ? round(($passed / $finished->count()) * 100) : 0;
        $inProgress = $quiz->participants()->whereNull('score')->count();

        $low = $finished->whereBetween('score', [0, 50])->count();
        $mid = $finished->whereBetween('score', [51, 75])->count();
        $high = $finished->whereBetween('score', [76, 100])->count();

        $pdf = Pdf::loadView('admin.pdf.quiz-report', compact(
            'quiz', 'participants', 'finished',
            'avgScore', 'passed', 'failed', 'passRate',
            'inProgress', 'low', 'mid', 'high'
        ))->setPaper('a4', 'portrait');

        $filename = 'Laporan-'.str_replace(' ', '-', $quiz->title).'-'.now()->format('Ymd').'.pdf';

        return $pdf->download($filename);
    }
}
