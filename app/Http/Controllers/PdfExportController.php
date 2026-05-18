<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfExportController extends Controller
{
    public function export(Quiz $quiz)
    {
        ini_set('max_execution_time', 120);
        ini_set('memory_limit', '512M');

        try {
            $allParticipants = $quiz->participants()->whereNotNull('score')->get();
            $internal = $allParticipants->whereNotNull('employee_id')->groupBy('employee_id')->map(fn($g) => $g->first());
            $public = $allParticipants->whereNull('employee_id')->groupBy('nim')->map(fn($g) => $g->first());
            $participants = $internal->concat($public)->sortByDesc('score')->values();
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
            
            $filename = "Laporan-Kuis-" . \Illuminate\Support\Str::slug($quiz->title) . "-" . now()->format('d-M-Y') . ".pdf";
            
            return response($pdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Content-Length' => strlen($pdf->output())
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("PDF Export Error: " . $e->getMessage());
            return back()->with('error', 'Gagal menghasilkan PDF: ' . $e->getMessage());
        }
    }
}
