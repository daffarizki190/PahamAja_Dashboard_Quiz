<?php

namespace App\Http\Controllers;

use App\Exports\GlobalParticipantExport;
use App\Models\Participant;
use App\Models\Quiz;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class GlobalReportController extends Controller
{
    public function index()
    {
        $participants = Participant::withTrashed()->with(['quiz' => fn($q) => $q->withTrashed(), 'employee' => fn($q) => $q->withTrashed()])
            ->whereNotNull('score')
            ->orderByDesc('updated_at')
            ->paginate(15);

        $totalStats = [
            'total_participants' => Participant::withTrashed()->whereNotNull('score')->count(),
            'avg_score' => round(Participant::withTrashed()->whereNotNull('score')->avg('score') ?? 0, 1),
            'total_quizzes' => Quiz::withTrashed()->count(),
        ];

        return view('admin.reports.index', compact('participants', 'totalStats'));
    }

    public function exportExcel()
    {
        return Excel::download(new GlobalParticipantExport, 'Laporan-Keseluruhan-Peserta-'.now()->format('Ymd').'.xlsx');
    }

    public function exportPdf()
    {
        $participants = Participant::withTrashed()->with(['quiz' => fn($q) => $q->withTrashed()])
            ->whereNotNull('score')
            ->orderByDesc('created_at')
            ->get();

        $quizzes = Quiz::withTrashed()->withCount(['participants' => function($q) {
            $q->withTrashed()->whereNotNull('score');
        }])->get();

        $totalFinished = $participants->count();
        $avgScore = round($participants->avg('score') ?? 0, 1);
        
        // Stats for Chart
        $stats = [
            'passed' => 0,
            'failed' => 0,
            'ranges' => [
                '0-50' => 0,
                '51-75' => 0,
                '76-100' => 0
            ]
        ];

        foreach ($participants as $p) {
            $passing = $p->quiz->passing_score ?? 70;
            if ($p->score >= $passing) {
                $stats['passed']++;
            } else {
                $stats['failed']++;
            }

            if ($p->score <= 50) $stats['ranges']['0-50']++;
            elseif ($p->score <= 75) $stats['ranges']['51-75']++;
            else $stats['ranges']['76-100']++;
        }

        $pdf = Pdf::loadView('admin.pdf.global-report', compact(
            'participants', 'quizzes', 'totalFinished', 'avgScore', 'stats'
        ))->setPaper('a4', 'portrait');

        return $pdf->download('Laporan-Keseluruhan-PahamAja-'.now()->format('Ymd').'.pdf');
    }
}
