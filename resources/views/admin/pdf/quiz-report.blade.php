<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Kuis - {{ $quiz->title }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #1e293b; line-height: 1.5; font-size: 11px; margin: 0; padding: 0; }
        
        .header-bar { height: 8px; background: linear-gradient(90deg, #4f46e5 0%, #7c3aed 100%); }
        .header { padding: 30px 40px; background: #fff; border-bottom: 1px solid #e2e8f0; }
        .header h1 { margin: 0; font-size: 22px; color: #0f172a; letter-spacing: -0.025em; }
        .header p { margin: 5px 0 0; color: #64748b; font-size: 10px; text-transform: uppercase; letter-spacing: 0.05em; }
        
        .container { padding: 30px 40px; }
        
        .summary-grid { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .summary-card { background: #f8fafc; padding: 15px; border-radius: 12px; border: 1px solid #e2e8f0; text-align: center; }
        .summary-card h3 { margin: 0; color: #64748b; font-size: 9px; text-transform: uppercase; letter-spacing: 0.1em; }
        .summary-card p { margin: 8px 0 0; font-size: 20px; font-weight: bold; color: #0f172a; }

        .section-title { font-size: 14px; font-weight: bold; margin-bottom: 20px; color: #0f172a; border-left: 4px solid #4f46e5; padding-left: 12px; }
        
        /* CSS Chart Styles */
        .chart-section { background: #fff; padding: 25px; border-radius: 16px; border: 1px solid #e2e8f0; margin-bottom: 40px; }
        .chart-row { margin-bottom: 20px; }
        .chart-label { display: block; font-size: 10px; font-weight: bold; color: #475569; margin-bottom: 6px; }
        .chart-track { height: 12px; background: #f1f5f9; border-radius: 100px; overflow: hidden; position: relative; }
        .chart-fill { height: 100%; border-radius: 100px; }
        .chart-legend { margin-top: 15px; display: table; width: 100%; }
        .legend-item { display: table-cell; font-size: 9px; color: #64748b; }
        .dot { display: inline-block; width: 8px; height: 8px; border-radius: 50%; margin-right: 5px; }

        table { width: 100%; border-collapse: collapse; }
        th { background: #f8fafc; padding: 12px 15px; text-align: left; font-size: 9px; font-weight: bold; text-transform: uppercase; color: #64748b; border-bottom: 2px solid #e2e8f0; }
        td { padding: 12px 15px; border-bottom: 1px solid #f1f5f9; font-size: 10px; }
        tr:nth-child(even) td { background: #fcfcfc; }

        .tag { padding: 4px 10px; border-radius: 6px; font-size: 9px; font-weight: bold; }
        .tag-pass { background: #dcfce7; color: #166534; }
        .tag-fail { background: #fee2e2; color: #991b1b; }

        .footer { position: fixed; bottom: 0; width: 100%; padding: 20px 40px; text-align: center; font-size: 9px; color: #94a3b8; border-top: 1px solid #f1f5f9; background: #fff; }
    </style>
</head>
<body>
    <div class="header-bar"></div>
    <div class="header">
        <p>PAHAMAJA ENTERPRISE ASSESSMENT</p>
        <h1>{{ $quiz->title }}</h1>
        <div style="margin-top: 10px; font-size: 9px; color: #94a3b8;">
            Passing Score: <strong>{{ $quiz->passing_score }}%</strong> &nbsp;|&nbsp; 
            Durasi: <strong>{{ $quiz->time_limit }} Menit</strong> &nbsp;|&nbsp; 
            Dicetak: {{ now()->format('d M Y, H:i') }}
        </div>
    </div>

    <div class="container">
        <table class="summary-grid">
            <tr>
                <td width="25%" style="padding-right: 15px;">
                    <div class="summary-card">
                        <h3>Peserta</h3>
                        <p>{{ $participants->count() }}</p>
                    </div>
                </td>
                <td width="25%" style="padding-right: 15px;">
                    <div class="summary-card">
                        <h3>Rata-rata</h3>
                        <p>{{ $avgScore }}%</p>
                    </div>
                </td>
                <td width="25%" style="padding-right: 15px;">
                    <div class="summary-card">
                        <h3>Lulus</h3>
                        <p style="color: #10b981;">{{ $passed }}</p>
                    </div>
                </td>
                <td width="25%">
                    <div class="summary-card">
                        <h3>Pass Rate</h3>
                        <p style="color: #4f46e5;">{{ $passRate }}%</p>
                    </div>
                </td>
            </tr>
        </table>

        <div class="section-title">Visualisasi Hasil & Sebaran</div>
        <div class="chart-section">
            <div class="chart-row">
                <span class="chart-label">Status Kelulusan Peserta</span>
                <div class="chart-track">
                    @php($passPct = $finished->count() > 0 ? ($passed / $finished->count()) * 100 : 0)
                    <div class="chart-fill" style="width: {{ $passPct }}%; background: #10b981;"></div>
                </div>
                <div class="chart-legend">
                    <div class="legend-item"><span class="dot" style="background: #10b981;"></span> Lulus ({{ $passed }})</div>
                    <div class="legend-item"><span class="dot" style="background: #ef4444;"></span> Gagal ({{ $failed }})</div>
                </div>
            </div>

            <div class="chart-row">
                <span class="chart-label">Distribusi Nilai Berdasarkan Grade</span>
                <div class="chart-track" style="background: #f1f5f9; display: block; border-radius: 4px;">
                    @php($lowPct = $finished->count() > 0 ? ($low / $finished->count()) * 100 : 0)
                    @php($midPct = $finished->count() > 0 ? ($mid / $finished->count()) * 100 : 0)
                    @php($highPct = $finished->count() > 0 ? ($high / $finished->count()) * 100 : 0)
                    <div style="width: {{ $lowPct }}%; height: 100%; background: #f43f5e; float: left;"></div>
                    <div style="width: {{ $midPct }}%; height: 100%; background: #3b82f6; float: left;"></div>
                    <div style="width: {{ $highPct }}%; height: 100%; background: #8b5cf6; float: left;"></div>
                </div>
                <div class="chart-legend">
                    <div class="legend-item"><span class="dot" style="background: #f43f5e;"></span> Low (0-50): {{ $low }}</div>
                    <div class="legend-item"><span class="dot" style="background: #3b82f6;"></span> Mid (51-75): {{ $mid }}</div>
                    <div class="legend-item"><span class="dot" style="background: #8b5cf6;"></span> High (76-100): {{ $high }}</div>
                </div>
            </div>
        </div>

        <div class="section-title">Daftar Detail Peserta</div>
        <table>
            <thead>
                <tr>
                    <th width="30">Pos</th>
                    <th>Nama Lengkap</th>
                    <th>NIK</th>
                    <th>Skor</th>
                    <th width="80">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($participants as $index => $p)
                <tr>
                    <td style="color: #94a3b8; font-weight: bold;">{{ $index + 1 }}</td>
                    <td><strong style="color: #0f172a;">{{ $p->name }}</strong></td>
                    <td style="color: #64748b;">{{ $p->nim }}</td>
                    <td style="font-weight: bold; font-size: 11px; color: #4f46e5;">{{ $p->score ?? '-' }}%</td>
                    <td>
                        @if(!is_null($p->score))
                            @if($p->score >= $quiz->passing_score)
                                <span class="tag tag-pass">PASSED</span>
                            @else
                                <span class="tag tag-fail">FAILED</span>
                            @endif
                        @else
                            <span style="color: #f59e0b; font-style: italic;">In Progress</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        Laporan ini dihasilkan secara otomatis oleh <strong>PahamAja Enterprise Dashboard</strong>. &nbsp;&bull;&nbsp; Dokumen ini adalah laporan resmi hasil kuis.
    </div>
</body>
</html>
