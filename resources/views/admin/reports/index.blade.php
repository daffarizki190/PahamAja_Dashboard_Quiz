@extends('layouts.app')

@section('title', 'Laporan Keseluruhan – PahamAja')
@section('meta_description', 'Pratinjau dan ekspor laporan assessment karyawan secara global')
@section('search_placeholder', 'Cari data peserta...')

@section('head_extra')
<style>
    .report-stat-card {
        background: #fff; border: 1px solid #E8E6F0; border-radius: 20px;
        padding: 22px 24px; display: flex; align-items: center; gap: 18px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.03); transition: all 0.2s;
    }
    .report-stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,0.08); }
    .report-stat-icon {
        width: 52px; height: 52px; border-radius: 14px;
        display: flex; align-items: center; justify-content: center; font-size: 22px;
    }
    
    .table-container {
        background: #fff; border: 1px solid #E8E6F0; border-radius: 24px;
        overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,0.03);
    }
    
    .btn-export {
        padding: 12px 24px; border-radius: 14px; font-size: 14px; font-weight: 800;
        display: inline-flex; align-items: center; gap: 10px; transition: all 0.2s;
        text-decoration: none; border: none; cursor: pointer;
    }
    .btn-excel { background: #10B981; color: #fff; box-shadow: 0 4px 14px rgba(16,185,129,0.3); }
    .btn-excel:hover { background: #059669; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(16,185,129,0.4); }
    
    .btn-pdf { background: #EF4444; color: #fff; box-shadow: 0 4px 14px rgba(239,68,68,0.3); }
    .btn-pdf:hover { background: #DC2626; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(239,68,68,0.4); }

    .pagination-custom { display: flex; justify-content: space-between; align-items: center; padding: 20px 24px; background: #F9F8FD; border-top: 1px solid #E8E6F0; }
</style>
@endsection

@section('content')
<div class="fade-up">
    <!-- Header with Export Actions -->
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:32px; flex-wrap:wrap; gap:20px;">
        <div>
            <h1 style="font-size:28px; font-weight:900; color:#1E1B4B; margin:0;">Laporan Performa Global</h1>
            <p style="font-size:14px; color:#6B7280; font-weight:500; margin-top:4px;">Pratinjau data terbaru sebelum melakukan ekspor.</p>
        </div>
        <div style="display:flex; gap:12px;">
            <a href="{{ route('admin.reports.global-excel') }}" class="btn-export btn-excel">
                <i class="fa-solid fa-file-excel"></i> Ekspor Excel
            </a>
            <a href="{{ route('admin.reports.global-pdf') }}" class="btn-export btn-pdf">
                <i class="fa-solid fa-file-pdf"></i> Ekspor PDF
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap:24px; margin-bottom:32px;">
        <div class="report-stat-card">
            <div class="report-stat-icon" style="background:rgba(124,58,237,0.1); color:#7C3AED;">
                <i class="fa-solid fa-users-viewfinder"></i>
            </div>
            <div>
                <div style="font-size:12px; font-weight:700; color:#6B7280; text-transform:uppercase; letter-spacing:0.05em;">Total Skor Terdata</div>
                <div style="font-size:24px; font-weight:900; color:#1E1B4B;">{{ number_format($totalStats['total_participants']) }}</div>
            </div>
        </div>
        
        <div class="report-stat-card delay-1">
            <div class="report-stat-icon" style="background:rgba(16,185,129,0.1); color:#10B981;">
                <i class="fa-solid fa-star"></i>
            </div>
            <div>
                <div style="font-size:12px; font-weight:700; color:#6B7280; text-transform:uppercase; letter-spacing:0.05em;">Rata-rata Skor</div>
                <div style="font-size:24px; font-weight:900; color:#1E1B4B;">{{ $totalStats['avg_score'] }}</div>
            </div>
        </div>

        <div class="report-stat-card delay-2">
            <div class="report-stat-icon" style="background:rgba(59,130,246,0.1); color:#3B82F6;">
                <i class="fa-solid fa-clipboard-list"></i>
            </div>
            <div>
                <div style="font-size:12px; font-weight:700; color:#6B7280; text-transform:uppercase; letter-spacing:0.05em;">Jumlah Kuis</div>
                <div style="font-size:24px; font-weight:900; color:#1E1B4B;">{{ $totalStats['total_quizzes'] }}</div>
            </div>
        </div>
    </div>

    <!-- Data Preview Table -->
    <div class="table-container delay-2" style="animation-fill-mode: forwards;">
        <div style="padding:24px; border-bottom:1px solid #E8E6F0; display:flex; justify-content:space-between; align-items:center;">
            <h3 style="font-size:16px; font-weight:800; color:#1E1B4B; margin:0;">Pratinjau Data Peserta</h3>
            <span style="font-size:12px; font-weight:700; color:#7C3AED; background:rgba(124,58,237,0.08); padding:4px 12px; border-radius:20px;">
                Menampilkan 15 data terbaru
            </span>
        </div>
        
        <div style="overflow-x:auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Peserta</th>
                        <th>Kuis</th>
                        <th>Skor</th>
                        <th>Status</th>
                        <th>Tanggal Selesai</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($participants as $p)
                    <tr>
                        <td>
                            <div style="font-weight:800; color:#1E1B4B;">{{ $p->name }}</div>
                            <div style="font-size:11px; color:#9CA3AF; font-weight:600;">ID: {{ $p->nim }}</div>
                        </td>
                        <td>
                            <div style="font-weight:700; color:#4B5563;">{{ optional($p->quiz)->title ?? 'N/A' }}</div>
                        </td>
                        <td>
                            <div style="font-size:18px; font-weight:900; color:{{ $p->score >= (optional($p->quiz)->passing_score ?? 70) ? '#059669' : '#DC2626' }}">
                                {{ $p->score }}
                            </div>
                        </td>
                        <td>
                            @php $passing = optional($p->quiz)->passing_score ?? 70; @endphp
                            @if($p->score >= $passing)
                                <span class="badge badge-green">LULUS</span>
                            @else
                                <span class="badge badge-red">GAGAL</span>
                            @endif
                        </td>
                        <td>
                            <div style="font-size:12px; color:#6B7280; font-weight:600;">
                                <i class="fa-regular fa-clock" style="margin-right:4px;"></i>
                                {{ $p->updated_at ? $p->updated_at->format('d M Y, H:i') : '—' }}
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align:center; padding:60px 20px;">
                            <div style="font-size:40px; margin-bottom:12px;">📭</div>
                            <div style="font-size:14px; color:#9CA3AF; font-weight:600;">Belum ada data nilai kuis yang tersedia.</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($participants->hasPages())
        <div class="pagination-custom">
            <div style="font-size:13px; color:#6B7280; font-weight:600;">
                Menampilkan {{ $participants->firstItem() }} - {{ $participants->lastItem() }} dari {{ $participants->total() }} data
            </div>
            <div style="display:flex; gap:8px;">
                @if($participants->onFirstPage())
                    <span style="opacity:0.5; cursor:not-allowed;" class="btn btn-ghost">Sebelumnya</span>
                @else
                    <a href="{{ $participants->previousPageUrl() }}" class="btn btn-ghost">Sebelumnya</a>
                @endif

                @if($participants->hasMorePages())
                    <a href="{{ $participants->nextPageUrl() }}" class="btn btn-primary" style="padding:8px 16px;">Selanjutnya</a>
                @else
                    <span style="opacity:0.5; cursor:not-allowed;" class="btn btn-primary" style="padding:8px 16px;">Selanjutnya</span>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
