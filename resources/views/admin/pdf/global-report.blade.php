<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Keseluruhan - PahamAja</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; line-height: 1.5; font-size: 12px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #4f46e5; padding-bottom: 10px; }
        .header h1 { margin: 0; color: #4f46e5; font-size: 24px; }
        .header p { margin: 5px 0; color: #666; }
        
        .summary-grid { width: 100%; margin-bottom: 30px; border-collapse: collapse; }
        .summary-card { background: #f8fafc; padding: 15px; border-radius: 8px; border: 1px solid #e2e8f0; text-align: center; }
        .summary-card h3 { margin: 0; color: #64748b; font-size: 10px; text-transform: uppercase; letter-spacing: 1px; }
        .summary-card p { margin: 10px 0 0; font-size: 24px; font-weight: bold; color: #0f172a; }

        .section-title { font-size: 16px; font-weight: bold; margin-bottom: 15px; color: #1e293b; border-left: 4px solid #4f46e5; padding-left: 10px; }
        
        /* CSS Chart Styles */
        .chart-container { background: #fff; padding: 20px; border-radius: 8px; border: 1px solid #e2e8f0; margin-bottom: 30px; }
        .bar-row { margin-bottom: 15px; }
        .bar-label { font-size: 10px; font-weight: bold; margin-bottom: 5px; color: #475569; }
        .bar-wrapper { height: 20px; background: #f1f5f9; border-radius: 4px; overflow: hidden; position: relative; }
        .bar-fill { height: 100%; background: #4f46e5; border-radius: 4px; }
        .bar-value { position: absolute; right: 8px; top: 2px; font-size: 10px; font-weight: bold; color: #fff; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background: #4f46e5; color: #fff; text-align: left; padding: 8px; font-size: 10px; text-transform: uppercase; }
        td { padding: 8px; border-bottom: 1px solid #e2e8f0; font-size: 10px; }
        tr:nth-child(even) { background: #f8fafc; }
        
        .status-tag { padding: 2px 6px; border-radius: 4px; font-size: 9px; font-weight: bold; text-transform: uppercase; }
        .status-pass { background: #dcfce7; color: #166534; }
        .status-fail { background: #fee2e2; color: #991b1b; }

        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 9px; color: #94a3b8; padding-bottom: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>PAHAMAJA ENTERPRISE</h1>
        <p>Laporan Keseluruhan Hasil Assessment Karyawan</p>
        <p>Dicetak pada: {{ now()->format('d M Y H:i') }}</p>
    </div>

    <table class="summary-grid">
        <tr>
            <td width="33%">
                <div class="summary-card">
                    <h3>Total Peserta</h3>
                    <p>{{ $totalFinished }}</p>
                </div>
            </td>
            <td width="33%">
                <div class="summary-card">
                    <h3>Rata-rata Skor</h3>
                    <p>{{ $avgScore }}%</p>
                </div>
            </td>
            <td width="33%">
                <div class="summary-card">
                    <h3>Tingkat Kelulusan</h3>
                    <p>{{ $totalFinished > 0 ? round(($stats['passed'] / $totalFinished) * 100) : 0 }}%</p>
                </div>
            </td>
        </tr>
    </table>

    <div class="section-title">Visualisasi Distribusi Nilai</div>
    <div class="chart-container">
        <div class="bar-row">
            <div class="bar-label">Lulus vs Tidak Lulus</div>
            <div class="bar-wrapper">
                @php($passPct = $totalFinished > 0 ? ($stats['passed'] / $totalFinished) * 100 : 0)
                <div class="bar-fill" style="width: {{ $passPct }}%; background: #10b981;"></div>
                <div class="bar-value">{{ $stats['passed'] }} Lulus</div>
            </div>
            <div class="bar-wrapper" style="margin-top: 5px;">
                @php($failPct = $totalFinished > 0 ? ($stats['failed'] / $totalFinished) * 100 : 0)
                <div class="bar-fill" style="width: {{ $failPct }}%; background: #ef4444;"></div>
                <div class="bar-value">{{ $stats['failed'] }} Gagal</div>
            </div>
        </div>

        <div class="bar-row" style="margin-top: 20px;">
            <div class="bar-label">Rentang Skor Peserta</div>
            <div class="bar-wrapper">
                @php($lowPct = $totalFinished > 0 ? ($stats['ranges']['0-50'] / $totalFinished) * 100 : 0)
                <div class="bar-fill" style="width: {{ $lowPct }}%; background: #f59e0b;"></div>
                <div class="bar-value">0-50: {{ $stats['ranges']['0-50'] }} org</div>
            </div>
            <div class="bar-wrapper" style="margin-top: 5px;">
                @php($midPct = $totalFinished > 0 ? ($stats['ranges']['51-75'] / $totalFinished) * 100 : 0)
                <div class="bar-fill" style="width: {{ $midPct }}%; background: #3b82f6;"></div>
                <div class="bar-value">51-75: {{ $stats['ranges']['51-75'] }} org</div>
            </div>
            <div class="bar-wrapper" style="margin-top: 5px;">
                @php($highPct = $totalFinished > 0 ? ($stats['ranges']['76-100'] / $totalFinished) * 100 : 0)
                <div class="bar-fill" style="width: {{ $highPct }}%; background: #8b5cf6;"></div>
                <div class="bar-value">76-100: {{ $stats['ranges']['76-100'] }} org</div>
            </div>
        </div>
    </div>

    <div class="section-title">Data Detail Peserta</div>
    <table>
        <thead>
            <tr>
                <th>Nama Peserta</th>
                <th>NIK</th>
                <th>Kuis</th>
                <th>Skor</th>
                <th>Durasi</th>
                <th>Status</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($participants as $p)
            <tr>
                <td><strong>{{ $p->name }}</strong></td>
                <td>{{ $p->nim }}</td>
                <td>{{ $p->quiz->title ?? 'N/A' }}</td>
                <td style="font-weight: bold;">{{ $p->score }}</td>
                <td>{{ $p->duration ?? '-' }}</td>
                <td>
                    @if($p->score >= ($p->quiz->passing_score ?? 70))
                        <span class="status-tag status-pass">LULUS</span>
                    @else
                        <span class="status-tag status-fail">GAGAL</span>
                    @endif
                </td>
                <td>{{ $p->finished_at?->format('d/m/y H:i') ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Laporan ini dihasilkan secara otomatis oleh sistem PahamAja. Halaman 1 dari 1.
    </div>
</body>
</html>
