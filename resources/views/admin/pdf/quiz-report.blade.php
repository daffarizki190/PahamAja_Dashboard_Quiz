<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Kuis - {{ $quiz->title }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 11px; color: #1e293b; background: #fff; }

        .header { background: #1e293b; color: #fff; padding: 24px 32px; margin-bottom: 24px; }
        .header-top { display: flex; justify-content: space-between; align-items: flex-start; }
        .brand { font-size: 10px; font-weight: bold; opacity: 0.6; letter-spacing: 2px; text-transform: uppercase; margin-bottom: 6px; }
        .title { font-size: 20px; font-weight: bold; letter-spacing: -0.5px; }
        .meta { font-size: 10px; opacity: 0.6; margin-top: 4px; }
        .badge { background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); 
                 padding: 6px 14px; border-radius: 8px; text-align: center; }
        .badge-label { font-size: 9px; opacity: 0.6; text-transform: uppercase; letter-spacing: 1px; }
        .badge-value { font-size: 22px; font-weight: bold; margin-top: 2px; }

        .stats-grid { display: flex; gap: 12px; margin: 0 32px 24px; }
        .stat-card { flex: 1; border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px 16px; }
        .stat-label { font-size: 9px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8; margin-bottom: 6px; }
        .stat-value { font-size: 22px; font-weight: bold; color: #0f172a; }
        .stat-sub { font-size: 10px; color: #64748b; margin-top: 2px; }

        .section { margin: 0 32px 24px; }
        .section-title { font-size: 13px; font-weight: bold; color: #0f172a; margin-bottom: 12px; 
                         padding-bottom: 8px; border-bottom: 2px solid #e2e8f0; }

        .dist-bar { display: flex; gap: 8px; margin-bottom: 16px; }
        .dist-item { flex: 1; border-radius: 8px; padding: 12px; text-align: center; }
        .dist-item.low  { background: #fff1f2; border: 1px solid #fecdd3; }
        .dist-item.mid  { background: #fffbeb; border: 1px solid #fde68a; }
        .dist-item.high { background: #f0fdf4; border: 1px solid #bbf7d0; }
        .dist-num { font-size: 24px; font-weight: bold; }
        .dist-item.low  .dist-num { color: #e11d48; }
        .dist-item.mid  .dist-num { color: #d97706; }
        .dist-item.high .dist-num { color: #16a34a; }
        .dist-label { font-size: 9px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 2px; }
        .dist-desc { font-size: 9px; color: #64748b; }

        table { width: 100%; border-collapse: collapse; }
        thead tr { background: #f8fafc; }
        th { padding: 10px 12px; text-align: left; font-size: 9px; font-weight: bold; 
             text-transform: uppercase; letter-spacing: 1px; color: #64748b; 
             border-bottom: 2px solid #e2e8f0; }
        td { padding: 10px 12px; border-bottom: 1px solid #f1f5f9; font-size: 11px; }
        tr:nth-child(even) td { background: #f8fafc; }

        .rank-badge { display: inline-block; width: 26px; height: 26px; border-radius: 6px;
                      text-align: center; line-height: 26px; font-weight: bold; font-size: 11px; }
        .rank-1 { background: #fef3c7; color: #d97706; }
        .rank-2 { background: #f1f5f9; color: #475569; }
        .rank-3 { background: #fff7ed; color: #ea580c; }
        .rank-n { color: #94a3b8; font-style: italic; }

        .score-chip { display: inline-block; background: #0f172a; color: #fff; 
                      padding: 3px 10px; border-radius: 20px; font-weight: bold; font-size: 10px; }
        .score-null { display: inline-block; background: #fef3c7; color: #d97706; 
                      padding: 3px 10px; border-radius: 20px; font-size: 9px; font-weight: bold; }

        .status-lulus    { color: #16a34a; font-weight: bold; font-size: 10px; }
        .status-gagal    { color: #dc2626; font-weight: bold; font-size: 10px; }
        .status-progress { color: #d97706; font-size: 10px; }

        .footer { margin: 32px 32px 16px; padding-top: 16px; border-top: 1px solid #e2e8f0;
                  display: flex; justify-content: space-between; font-size: 9px; color: #94a3b8; }
    </style>
</head>
<body>

<div class="header">
    <div class="header-top">
        <div>
            <div class="brand">PahamAja · Laporan Hasil Kuis</div>
            <div class="title">{{ $quiz->title }}</div>
            <div class="meta">Dicetak: {{ now()->locale('id')->translatedFormat('d F Y, H:i') }} &nbsp;|&nbsp; Nilai Kelulusan: {{ $quiz->passing_score }} &nbsp;|&nbsp; Durasi: {{ $quiz->time_limit }} menit</div>
        </div>
        <div class="badge">
            <div class="badge-label">Total Peserta</div>
            <div class="badge-value">{{ $participants->count() }}</div>
        </div>
    </div>
</div>

{{-- Stats --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">Rata-rata Nilai</div>
        <div class="stat-value">{{ $avgScore }}%</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Peserta Lulus</div>
        <div class="stat-value" style="color:#16a34a;">{{ $passed }}</div>
        <div class="stat-sub">dari {{ $finished->count() }} selesai ({{ $passRate }}%)</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Tidak Lulus</div>
        <div class="stat-value" style="color:#dc2626;">{{ $failed }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Sedang Mengerjakan</div>
        <div class="stat-value" style="color:#d97706;">{{ $inProgress }}</div>
    </div>
</div>

{{-- Score Distribution --}}
<div class="section">
    <div class="section-title">Sebaran Nilai</div>
    <div class="dist-bar">
        <div class="dist-item low">
            <div class="dist-label" style="color:#e11d48;">Low</div>
            <div class="dist-num">{{ $low }}</div>
            <div class="dist-desc">Nilai 0 – 50</div>
        </div>
        <div class="dist-item mid">
            <div class="dist-label" style="color:#d97706;">Mid</div>
            <div class="dist-num">{{ $mid }}</div>
            <div class="dist-desc">Nilai 51 – 75</div>
        </div>
        <div class="dist-item high">
            <div class="dist-label" style="color:#16a34a;">High</div>
            <div class="dist-num">{{ $high }}</div>
            <div class="dist-desc">Nilai 76 – 100</div>
        </div>
    </div>
</div>

{{-- Participant Table --}}
<div class="section">
    <div class="section-title">Data Peserta ({{ $participants->count() }} orang)</div>
    <table>
        <thead>
            <tr>
                <th style="width:50px;">No</th>
                <th>Nama</th>
                <th>NIK</th>
                <th style="width:80px;">Nilai</th>
                <th style="width:90px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($participants as $i => $p)
            <tr>
                <td>
                    @if($i === 0 && !is_null($p->score))
                        <span class="rank-badge rank-1">1</span>
                    @elseif($i === 1 && !is_null($p->score))
                        <span class="rank-badge rank-2">2</span>
                    @elseif($i === 2 && !is_null($p->score))
                        <span class="rank-badge rank-3">3</span>
                    @else
                        <span class="rank-n">#{{ $i + 1 }}</span>
                    @endif
                </td>
                <td><strong>{{ $p->name }}</strong></td>
                <td style="color:#64748b;">{{ $p->nim }}</td>
                <td>
                    @if(!is_null($p->score))
                        <span class="score-chip">{{ $p->score }}</span>
                    @else
                        <span class="score-null">Mengerjakan</span>
                    @endif
                </td>
                <td>
                    @if(!is_null($p->score))
                        @if($p->score >= $quiz->passing_score)
                            <span class="status-lulus">✓ LULUS</span>
                        @else
                            <span class="status-gagal">✗ TIDAK LULUS</span>
                        @endif
                    @else
                        <span class="status-progress">⏳ Belum Selesai</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="footer">
    <span>PahamAja Dashboard &mdash; Laporan otomatis</span>
    <span>{{ now()->locale('id')->translatedFormat('d/m/Y H:i') }}</span>
</div>

</body>
</html>
