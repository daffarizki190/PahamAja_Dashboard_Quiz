@extends('layouts.app')

@section('title', $quiz->title . ' – Analitik')
@section('meta_description', 'Analitik kuis ' . $quiz->title)
@section('page_title', 'Analitik Kuis')
@section('page_subtitle', $quiz->title)

@section('topbar_left')
    <a href="{{ route('admin.quizzes.show', $quiz->slug) }}" class="btn btn-ghost" style="padding:9px 12px; font-size:12px; background:rgba(124,58,237,0.08); border:1px solid rgba(124,58,237,0.15); color:var(--purple); margin-right:8px;">
        <i class="fa-solid fa-arrow-left"></i> Kembali
    </a>
@endsection

@section('topbar_actions')
    <div style="display:flex; align-items:center; gap:6px; background:rgba(16,185,129,0.1); border:1px solid rgba(16,185,129,0.2); border-radius:20px; padding:5px 12px; font-size:12px; font-weight:700; color:#059669;">
        <span style="width:7px; height:7px; border-radius:50%; background:#10B981; animation:pulse 1.5s ease-in-out infinite; display:inline-block;"></span>
        Aktif
    </div>
    <a href="{{ route('admin.quiz.export', $quiz->slug) }}" class="btn btn-ghost" style="padding:7px 13px; font-size:12px;" title="Export Excel">
        <i class="fa-solid fa-file-excel" style="color:#059669;"></i>
    </a>
    <a href="{{ route('admin.quiz.export-pdf', $quiz->slug) }}" class="btn btn-ghost" style="padding:7px 13px; font-size:12px;" title="Export PDF">
        <i class="fa-solid fa-file-pdf" style="color:#DC2626;"></i>
    </a>
    <button type="button" id="btnAiInsight"
            onclick="window.pahamajaOpenAiInsight && window.pahamajaOpenAiInsight(event)"
            class="btn btn-primary" style="padding:7px 14px; font-size:12px;">
        <i class="fa-solid fa-wand-magic-sparkles"></i> AI Insight
    </button>
@endsection

@section('head_extra')
<style>
    /* Stat cards top row */
    .analytic-stat {
        background: #fff;
        border: 1px solid #E5E3F0;
        border-radius: 16px;
        padding: 22px 24px;
        display: flex; align-items: center; gap: 20px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.04);
        transition: all 0.2s;
    }
    .analytic-stat:hover { box-shadow: 0 6px 20px rgba(0,0,0,0.08); transform: translateY(-2px); }
    .analytic-stat-text h3 { font-size: 13px; font-weight: 600; color: #6B7280; margin-bottom: 4px; }
    .analytic-stat-text .val { font-size: 32px; font-weight: 900; color: #1E1B4B; line-height: 1; }

    /* Donut gauge */
    #gaugeChart { width: 80px; height: 80px; flex-shrink: 0; }

    /* Score chart */
    .chart-card { background: #fff; border: 1px solid #E5E3F0; border-radius: 16px; padding: 24px; box-shadow: 0 1px 4px rgba(0,0,0,0.04); }

    /* QR section */
    .qr-section { background: #fff; border: 1px solid #E5E3F0; border-radius: 16px; padding: 24px; box-shadow: 0 1px 4px rgba(0,0,0,0.04); }
    .qr-wrap { background: #fff; border: 1px solid #E5E3F0; border-radius: 12px; padding: 16px; display: inline-block; margin-bottom: 14px; }
    .qr-wrap svg { display: block; }
    .link-copy { display:flex; background:#F9F8FD; border:1px solid #E5E3F0; border-radius:8px; overflow:hidden; }
    .link-copy input { flex:1; background:transparent; border:none; color:#6B7280; font-size:12px; padding:9px 12px; outline:none; font-family:inherit; }
    .link-copy button { background:linear-gradient(135deg,#7C3AED,#4F46E5); color:#fff; border:none; padding:9px 14px; font-size:11px; font-weight:800; cursor:pointer; transition:all .2s; }
    .link-copy button:hover { opacity:.9; }

    /* Bottom action cards */
    .action-card { background: #fff; border: 1px solid #E5E3F0; border-radius: 16px; padding: 22px 24px; box-shadow: 0 1px 4px rgba(0,0,0,0.04); }
    .action-card h4 { font-size: 14px; font-weight: 800; color: #1E1B4B; margin-bottom: 14px; }

    /* Export buttons */
    .export-btn {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 8px 16px; border-radius: 8px; font-size: 12px; font-weight: 700;
        cursor: pointer; text-decoration: none; transition: all 0.18s;
        border: 1px solid #E5E3F0; background: #fff; color: #1E1B4B;
    }
    .export-btn:hover { background: #F3F2FB; border-color: #C4BFDF; }

    /* Edit/Delete buttons */
    .quiz-action-btn {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 8px 16px; border-radius: 8px; font-size: 12px; font-weight: 700;
        cursor: pointer; text-decoration: none; transition: all 0.18s;
        border: 1px solid #E5E3F0; background: #fff; color: #1E1B4B;
    }
    .quiz-action-btn:hover { background: #F3F2FB; border-color: #C4BFDF; }
    .quiz-action-btn.danger { color: #DC2626; border-color: rgba(220,38,38,0.2); }
    .quiz-action-btn.danger:hover { background: rgba(239,68,68,0.06); }

    /* Leaderboard table overrides */
    .data-table thead th { background: #F9F8FD; }

    /* AI Modal */
    #aiInsightModal .modal-box { background: #fff; }
    #aiInsightModal h3 { color: #1E1B4B; }

    /* Responsive Grids */
    .analytic-grid-3 { display:grid; grid-template-columns:repeat(3,1fr); gap:18px; margin-bottom:22px; }
    .analytic-sub-grid { display:grid; grid-template-columns:1fr 1fr; gap:18px; margin-bottom:22px; }
    .analytic-main-grid { display:grid; grid-template-columns:1fr 340px; gap:22px; }
    .table-responsive { width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; }

    @media (max-width: 1100px) {
        .analytic-main-grid { grid-template-columns: 1fr; }
        .analytic-sub-grid { grid-template-columns: 1fr; }
    }
    @media (max-width: 768px) {
        .analytic-grid-3 { grid-template-columns: 1fr; }
        .analytic-stat { padding: 18px; gap: 14px; }
        .val { font-size: 28px !important; }
    }

    /* Podium Styles */
    .podium-wrap {
        display: flex; align-items: flex-end; justify-content: center; gap: 20px;
        margin-bottom: 40px; padding: 20px; perspective: 1000px;
    }
    .podium-item {
        display: flex; flex-direction: column; align-items: center; gap: 12px;
        position: relative; transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .podium-step {
        width: 100px; border-radius: 12px 12px 20px 20px;
        display: flex; flex-direction: column; align-items: center; justify-content: flex-end;
        padding-bottom: 12px; color: #fff; font-weight: 900; font-size: 24px;
        box-shadow: 0 10px 30px rgba(30,27,75,0.1);
    }
    .podium-1 { order: 2; }
    .podium-1 .podium-step { height: 120px; background: linear-gradient(180deg, #F59E0B, #B45309); width: 120px; }
    .podium-1 .p-avatar { width: 90px; height: 90px; border: 4px solid #F59E0B; }
    .podium-1 .p-crown { font-size: 24px; color: #F59E0B; margin-bottom: -15px; z-index: 10; }
    
    .podium-2 { order: 1; }
    .podium-2 .podium-step { height: 90px; background: linear-gradient(180deg, #94A3B8, #475569); }
    .podium-2 .p-avatar { width: 75px; height: 75px; border: 3px solid #94A3B8; }
    
    .podium-3 { order: 3; }
    .podium-3 .podium-step { height: 70px; background: linear-gradient(180deg, #D97706, #78350F); }
    .podium-3 .p-avatar { width: 65px; height: 65px; border: 3px solid #D97706; }

    .p-avatar {
        border-radius: 20px; background: #fff; overflow: hidden;
        display: flex; align-items: center; justify-content: center;
        font-size: 24px; font-weight: 900; color: var(--purple);
    }
    .p-name { 
        font-size: 13px; font-weight: 800; color: #1E1B4B; margin-top: 5px; 
        text-align: center; max-width: 130px; line-height: 1.2;
        display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
        min-height: 32px;
    }
    .p-score { font-size: 11px; font-weight: 800; background: rgba(124,58,237,0.1); color: var(--purple); padding: 2px 8px; border-radius: 6px; }
</style>
@endsection

@section('content')
<!-- Stats Row – 3 cards -->
<div class="analytic-grid-3">

    <!-- Rata-rata Skor dengan gauge -->
    <div class="analytic-stat card-3d fade-up delay-1" data-tilt data-tilt-max="10" data-tilt-speed="400" data-tilt-glare="true" data-tilt-max-glare="0.1">
        <div id="gaugeChart"></div>
        <div class="analytic-stat-text">
            <h3>Rata-rata Skor</h3>
            <div class="val" id="statAvgScore">{{ number_format($avgScore, 1) }}</div>
        </div>
        <!-- trophy icon -->
        <img src="https://fonts.gstatic.com/s/e/notoemoji/latest/1f3c6/emoji.svg" alt="🏆" class="float-3d depth-shadow" style="width:52px; height:52px; margin-left:auto; flex-shrink:0;" onerror="this.outerHTML='<span style=\'font-size:40px;margin-left:auto;\'>🏆</span>'">
    </div>

    <!-- Tingkat Kelulusan -->
    <div class="analytic-stat card-3d fade-up delay-2" data-tilt data-tilt-max="10" data-tilt-speed="400" data-tilt-glare="true" data-tilt-max-glare="0.1">
        <div class="analytic-stat-text" style="flex:1">
            <h3>Tingkat Kelulusan</h3>
            <div class="val" style="font-size:38px; color:#7C3AED;">
                @php
                    $passingScore = $quiz->passing_score ?? 70;
                    $completed = $participants->whereNotNull('score')->unique('employee_id')->count();
                    $passed = $participants->whereNotNull('score')->groupBy('employee_id')->map(fn($a) => $a->max('score'))->filter(fn($s) => $s >= $passingScore)->count();
                    $passRate = $completed > 0 ? round(($passed / $completed) * 100) : 0;
                @endphp
                {{ $passRate }}%
            </div>
        </div>
        <!-- medal icon -->
        <img src="https://fonts.gstatic.com/s/e/notoemoji/latest/1f3c5/emoji.svg" alt="🏅" class="float-3d depth-shadow" style="width:52px; height:52px; flex-shrink:0; animation-delay: 0.5s;" onerror="this.outerHTML='<span style=\'font-size:40px;\'>🏅</span>'">
    </div>

    <!-- Total Peserta -->
    <div class="analytic-stat card-3d fade-up delay-3" data-tilt data-tilt-max="10" data-tilt-speed="400" data-tilt-glare="true" data-tilt-max-glare="0.1">
        <div class="analytic-stat-text" style="flex:1">
            <h3>Total Peserta</h3>
            <div class="val">{{ $participants->count() }}</div>
        </div>
        <!-- people icon -->
        <img src="https://fonts.gstatic.com/s/e/notoemoji/latest/1f465/emoji.svg" alt="👥" class="float-3d depth-shadow" style="width:52px; height:52px; flex-shrink:0; animation-delay: 1s;" onerror="this.outerHTML='<span style=\'font-size:40px;\'>👥</span>'">
    </div>
</div>

<div class="analytic-sub-grid">
    <!-- Sebaran Nilai Bar Chart -->
    <div class="chart-card fade-up delay-2">
        <div style="font-size:15px; font-weight:800; color:#1E1B4B; margin-bottom:20px;">Sebaran Nilai</div>
        <div id="scoreBarChart" style="width:100%; min-height:220px;"></div>
    </div>

    <!-- QR Code -->
    <div class="qr-section" style="text-align:center;">
        <div class="qr-wrap" id="qrWrap">
            {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(140)->color(30,27,75)->margin(1)->generate(route('quiz.join', $quiz->slug)) !!}
        </div>
        <div style="display:flex; gap:8px; justify-content:center; margin-bottom:12px;">
            <button onclick="copyQr()" class="btn btn-ghost" style="padding:6px 10px; font-size:11px;">
                <i class="fa-solid fa-copy"></i>
            </button>
            <button onclick="downloadQr()" class="btn btn-ghost" style="padding:6px 10px; font-size:11px;">
                <i class="fa-solid fa-download"></i>
            </button>
        </div>
        <div class="link-copy">
            <input type="text" id="quizLink" value="{{ url('/quiz/'.$quiz->slug) }}" readonly>
            <button onclick="copyLink()">Copy</button>
        </div>
    </div>
                    <th>Jawaban Terbanyak</th>
                </tr>
            </thead>
            <tbody>
                @forelse($questionAnalytics as $idx => $row)
                <tr>
                    <td>
                        <div style="font-weight:700; color:#7C3AED; font-size:12px; margin-bottom:3px;">#{{ $idx + 1 }}</div>
                        <div style="font-size:13px; color:#1E1B4B; line-height:1.4; max-width:500px;">{{ $row['text'] }}</div>
                    </td>
                    <td>
                        <span class="badge badge-purple">{{ $row['answered'] }}/{{ $row['participants'] }}</span>
                    </td>
                    <td>
                        @if(!is_null($row['correct_rate']))
                        @php $rate = $row['correct_rate']; $rateColor = $rate >= 70 ? '#059669' : ($rate >= 40 ? '#D97706' : '#DC2626'); @endphp
                        <div style="display:flex; align-items:center; gap:10px; min-width:140px;">
                            <div class="progress-bar-track" style="flex:1;">
                                <div class="progress-bar-fill" data-width="{{ $rate }}" style="background:{{ $rateColor }}; width:0%;"></div>
                            </div>
                            <span style="font-size:14px; font-weight:800; color:{{ $rateColor }}; min-width:36px; text-align:right;">{{ $rate }}%</span>
                        </div>
                        @else
                        <span style="color:#9CA3AF; font-size:12px;">Belum ada data</span>
                        @endif
                    </td>
                    <td style="color:#374151; font-size:13px;">{{ $row['top_option'] ?: '—' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align:center; padding:40px; color:#6B7280; font-size:13px;">Belum ada data soal</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Leaderboard -->
<div class="card fade-up delay-4" style="overflow:hidden;">
    <div style="padding:18px 22px; border-bottom:1px solid #E5E3F0; display:flex; align-items:center; gap:12px;">
        <div style="width:36px; height:36px; border-radius:10px; background:linear-gradient(135deg,#D97706,#EA580C); display:flex; align-items:center; justify-content:center; color:#fff; font-size:14px;">
            <i class="fa-solid fa-trophy"></i>
        </div>
        <div>
            <div style="font-size:14px; font-weight:800; color:#1E1B4B;">Peringkat Peserta</div>
            <div style="font-size:11px; color:#6B7280;">Skor real-time</div>
        </div>
        <div style="flex:1;"></div>
        <button type="button" onclick="document.getElementById('unparticipatedModal').classList.add('open'); document.body.style.overflow='hidden';" class="btn btn-ghost" style="padding:6px 12px; font-size:12px; color:#DC2626; border:1px solid rgba(220,38,38,0.2); background:rgba(220,38,38,0.05); border-radius:8px; font-weight:700;">
            <i class="fa-solid fa-user-clock"></i> Belum Mengerjakan ({{ $unparticipatedEmployees->count() }})
        </button>
    </div>
    
    @php
        $top3 = $participants->whereNotNull('score')->sortByDesc('score')->take(3);
    @endphp

    @if($top3->count() > 0)
    <div class="podium-wrap fade-up delay-4">
        @foreach($top3 as $idx => $topP)
            @php 
                $rank = $loop->index + 1; 
                $emp = $topP->employee;
                $pInitials = strtoupper(substr($topP->name, 0, 1));
            @endphp
            <div class="podium-item podium-{{ $rank }}" data-tilt data-tilt-max="15">
                @if($rank == 1) <div class="p-crown float-3d"><i class="fa-solid fa-crown"></i></div> @endif
                <div class="p-avatar img-3d">
                    @if($emp && $emp->avatar)
                        <img src="/storage/{{ $emp->avatar }}" style="width:100%; height:100%; object-fit:cover;">
                    @else
                        {{ $pInitials }}
                    @endif
                </div>
                <div class="p-name">{{ $topP->name }}</div>
                <div class="p-score">{{ $topP->score }} pts</div>
                <div class="podium-step">
                    {{ $rank }}
                </div>
            </div>
        @endforeach
    </div>
    @endif

    <div style="overflow-x:auto;">

        <table class="data-table" id="liveLeaderboard">
            <thead>
                <tr>
                    <th>Peringkat</th>
                    <th>Identitas</th>
                    <th>Sesi</th>
                    <th>Nilai</th>
                    <th>Status</th>
                    <th style="text-align:right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($participants as $index => $participant)
                @php
                    $hasScore = !is_null($participant->score);
                    $passed   = $hasScore && $participant->score >= $quiz->passing_score;
                    $medals   = ['🥇','🥈','🥉'];
                @endphp
                <tr>
                    <td>
                        @if($hasScore && $index < 3)
                            <span style="font-size:20px;">{{ $medals[$index] }}</span>
                        @else
                            <span style="color:#9CA3AF; font-size:13px; font-weight:700;">#{{ $index + 1 }}</span>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex; align-items:center; gap:12px;">
                            <div style="width:36px; height:36px; border-radius:10px; background:linear-gradient(135deg,#7C3AED,#4F46E5); display:flex; align-items:center; justify-content:center; color:#fff; font-size:13px; font-weight:800; flex-shrink:0; overflow:hidden;">
                                @if($participant->employee && $participant->employee->avatar)
                                    <img src="{{ avatar_url($participant->employee->avatar) }}" style="width: 100%; height: 100%; object-fit: cover;">
                                @else
                                    {{ strtoupper(substr($participant->name, 0, 1)) }}
                                @endif
                            </div>
                            <div>
                                <div style="font-size:14px; font-weight:700; color:#1E1B4B;">{{ $participant->name }}</div>
                                <div style="font-size:11px; color:#6B7280;">{{ $participant->nim }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span style="font-size:12px; color:#6B7280; font-weight:600;">{{ $participant->quizSession ? $participant->quizSession->name : '—' }}</span>
                    </td>
                    <td>
                        @if($hasScore)
                        @php $scoreColor = $passed ? '#059669' : '#DC2626'; @endphp
                        <span style="font-size:18px; font-weight:900; color:{{ $scoreColor }};">{{ $participant->score }}</span>
                        @else
                        <span class="badge badge-yellow" style="animation:pulse 1.5s ease-in-out infinite;">
                            <i class="fa-solid fa-pen" style="font-size:9px;"></i> Mengerjakan
                        </span>
                        @endif
                    </td>
                    <td>
                        @if($hasScore)
                        <span class="badge {{ $passed ? 'badge-green' : 'badge-red' }}">
                            {{ $passed ? 'Lulus' : 'Tidak Lulus' }}
                        </span>
                        @else
                        <span style="color:#9CA3AF; font-size:11px; font-weight:600;">Menunggu...</span>
                        @endif
                    </td>
                    <td style="text-align:right;">
                        <div style="display:flex; align-items:center; justify-content:flex-end; gap:6px;">
                            <a href="{{ route('admin.participant.answers', ['quiz' => $quiz->slug, 'participant' => $participant->id]) }}"
                               class="btn btn-ghost" style="padding:6px 10px; font-size:12px;" title="Lihat Jawaban">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <form action="{{ route('admin.participant.destroy', ['quiz' => $quiz->slug, 'participant' => $participant->id]) }}"
                                  method="POST" onsubmit="event.preventDefault(); PahamAja.confirm('Hapus Peserta', 'Apakah Anda yakin ingin menghapus peserta ini? Seluruh riwayat jawaban dan nilai peserta ini akan dihapus permanen.', 'danger', () => this.submit())">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" style="padding:6px 10px; font-size:12px;" title="Hapus">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center; padding:50px; color:#6B7280;">
                        <div style="font-size:36px; margin-bottom:10px;">👥</div>
                        Belum ada peserta yang bergabung
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- AI Insight Modal -->
<div id="aiInsightModal" class="modal-overlay" aria-hidden="true">
    <div class="modal-box" style="max-width:700px; max-height:85vh; display:flex; flex-direction:column;">
        <div style="display:flex; align-items:flex-start; gap:14px; margin-bottom:20px; flex-shrink:0;">
            <div style="width:44px; height:44px; border-radius:12px; background:rgba(124,58,237,0.1); color:#7C3AED; display:flex; align-items:center; justify-content:center; font-size:18px; flex-shrink:0;">
                <i class="fa-solid fa-wand-magic-sparkles"></i>
            </div>
            <div style="flex:1;">
                <h3 style="font-size:18px; font-weight:900; margin-bottom:4px;">✨ AI Insight</h3>
                <p style="font-size:12px;">Analisis mendalam dari Gemini AI</p>
            </div>
            <button type="button" id="btnCloseAiInsightModal" class="btn btn-ghost" style="padding:7px 10px; flex-shrink:0;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div id="aiInsightModalLoading" style="display:flex; align-items:center; gap:12px; color:#6B7280; font-size:13px; padding:8px 0;">
            <span class="dots-wave purple"><span></span><span></span><span></span><span></span></span>
            Menganalisis data kuis...
        </div>

        <div id="aiInsightTabs" style="display:none; margin-bottom:16px; flex-shrink:0;">
            <div style="display:flex; background:#F3F2FB; border:1px solid #E5E3F0; border-radius:10px; padding:3px; gap:3px; width:fit-content;">
                <button data-ai-tab="analysis" class="ai-tab" style="padding:7px 14px; border-radius:8px; font-size:12px; font-weight:700; cursor:pointer; border:none; font-family:inherit; transition:all .2s; color:#6B7280; background:transparent;">Analisis</button>
                <button data-ai-tab="reco"     class="ai-tab" style="padding:7px 14px; border-radius:8px; font-size:12px; font-weight:700; cursor:pointer; border:none; font-family:inherit; transition:all .2s; color:#6B7280; background:transparent;">Rekomendasi</button>
                <button data-ai-tab="questions" class="ai-tab" style="padding:7px 14px; border-radius:8px; font-size:12px; font-weight:700; cursor:pointer; border:none; font-family:inherit; transition:all .2s; color:#6B7280; background:transparent;">Soal</button>
                <button data-ai-tab="raw"      class="ai-tab" style="padding:7px 14px; border-radius:8px; font-size:12px; font-weight:700; cursor:pointer; border:none; font-family:inherit; transition:all .2s; color:#6B7280; background:transparent;">Raw</button>
            </div>
        </div>

        <div id="aiInsightViews" style="display:none; overflow-y:auto; flex:1; display:flex; flex-direction:column; gap:12px;">
            <div id="aiInsightViewAnalysis"></div>
            <div id="aiInsightViewReco"      style="display:none;"></div>
            <div id="aiInsightViewQuestions" style="display:none;"></div>
            <div id="aiInsightViewRaw"       style="display:none;"></div>
        </div>

        <p id="aiInsightModalError" style="display:none; color:#DC2626; font-size:13px; font-weight:700; margin-top:12px;"></p>
    </div>
</div>

<!-- Unparticipated Modal -->
<div id="unparticipatedModal" class="modal-overlay" aria-hidden="true">
    <div class="modal-box" style="max-width:600px; max-height:80vh; display:flex; flex-direction:column;">
        <div style="display:flex; align-items:flex-start; gap:14px; margin-bottom:20px; flex-shrink:0;">
            <div style="width:44px; height:44px; border-radius:12px; background:rgba(220,38,38,0.1); color:#DC2626; display:flex; align-items:center; justify-content:center; font-size:18px; flex-shrink:0;">
                <i class="fa-solid fa-user-clock"></i>
            </div>
            <div style="flex:1;">
                <h3 style="font-size:18px; font-weight:900; margin-bottom:4px; color:#1E1B4B;">Belum Mengerjakan</h3>
                <p style="font-size:12px; color:#6B7280;">Daftar karyawan aktif yang belum memulai kuis ini ({{ $unparticipatedEmployees->count() }} orang)</p>
            </div>
            <button type="button" onclick="document.getElementById('unparticipatedModal').classList.remove('open'); document.body.style.overflow='';" class="btn btn-ghost" style="padding:7px 10px; flex-shrink:0;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div style="overflow-y:auto; flex:1; padding-right:4px;">
            @if($unparticipatedEmployees->count() > 0)
                <div style="display:grid; grid-template-columns:1fr; gap:12px;">
                    @foreach($unparticipatedEmployees as $employee)
                    <div style="display:flex; align-items:center; gap:12px; padding:12px; border:1px solid #E5E3F0; border-radius:12px; background:#F9F8FD;">
                        <div style="width: 32px; height: 32px; border-radius: 8px; background: linear-gradient(135deg, var(--purple), var(--indigo)); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(124,58,237,0.3);">
                            @if($employee->avatar)
                                <img src="{{ avatar_url($employee->avatar) }}" style="width: 100%; height: 100%; object-fit: cover;">
                            @else
                                {{ substr($employee->name, 0, 1) }}
                            @endif
                        </div>
                        <div style="flex:1;">
                            <div style="font-size:14px; font-weight:700; color:#1E1B4B;">{{ $employee->name }}</div>
                            <div style="font-size:11px; color:#6B7280;">{{ $employee->nim }} &bull; {{ $employee->department ?? 'Tidak ada departemen' }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div style="text-align:center; padding:40px 20px; color:#6B7280;">
                    <div style="font-size:36px; margin-bottom:10px;">🎉</div>
                    <div style="font-size:14px; font-weight:700;">Semua karyawan aktif telah mengerjakan kuis ini!</div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/prevent-double-submit.js') }}"></script>
<script>
// ── PROGRESS BARS ──
document.addEventListener('DOMContentLoaded', () => {
    setTimeout(() => {
        document.querySelectorAll('.progress-bar-fill[data-width]').forEach(el => {
            const v = parseFloat(el.dataset.width ?? 0);
            el.style.width = Math.min(100, Math.max(0, v)) + '%';
        });
    }, 300);
});

// ── GAUGE CHART (donut) ──
(function initGauge() {
    const avg = {{ number_format($avgScore, 1) }};
    const el  = document.querySelector('#gaugeChart');
    if (!el || typeof ApexCharts === 'undefined') return;
    new ApexCharts(el, {
        series: [avg],
        chart: { type:'radialBar', height:90, width:90, sparkline:{enabled:true}, fontFamily:'Plus Jakarta Sans, sans-serif', background:'transparent' },
        plotOptions: { radialBar: {
            hollow: { size:'55%' },
            track: { background:'#E5E3F0' },
            dataLabels: { name:{show:false}, value:{show:true, fontSize:'13px', fontWeight:900, color:'#1E1B4B', offsetY:5, formatter:v => v} }
        }},
        colors: ['#7C3AED'],
        theme: { mode:'light' }
    }).render();
})();

// ── BAR CHART (Sebaran Nilai) ──
function initBarChart() {
    const el = document.querySelector('#scoreBarChart');
    if (!el || typeof ApexCharts === 'undefined') return;
    try {
        const d = {!! json_encode($chartData) !!};
        if (!d?.scores) return;
        const scores = d.scores.map(s => parseInt(s) || 0);
        const labels = ['0-20','21-40','41-60','61-80','81-100'];
        new ApexCharts(el, {
            series: [{ name:'Peserta', data: scores }],
            chart: { type:'bar', height:220, fontFamily:'Plus Jakarta Sans, sans-serif', background:'transparent',
                toolbar:{show:false}, animations:{enabled:true} },
            plotOptions: { bar: { borderRadius:6, columnWidth:'45%', distributed:false } },
            colors: ['#7C3AED'],
            dataLabels: { enabled:true, style:{fontSize:'12px', fontWeight:700, colors:['#fff']} },
            xaxis: { categories: labels, labels:{ style:{colors:'#6B7280', fontSize:'12px', fontWeight:600} }, axisBorder:{show:false}, axisTicks:{show:false} },
            yaxis: { labels:{ style:{colors:'#6B7280', fontSize:'11px'} } },
            grid: { borderColor:'#F0EFF7', strokeDashArray:4 },
            theme: { mode:'light' },
            tooltip: { y:{ formatter:v => v + ' peserta' } }
        }).render();
    } catch(e) {}
}
if (document.readyState === 'loading') window.addEventListener('DOMContentLoaded', initBarChart);
else initBarChart();

// ── QR UTILS ──
const getQrSvg = () => document.querySelector('#qrWrap svg');
const svgToPngBlob = async (svgEl, size=512) => {
    const data = new XMLSerializer().serializeToString(svgEl);
    const blob = new Blob([data], {type:'image/svg+xml;charset=utf-8'});
    const url  = URL.createObjectURL(blob);
    const img  = new Image(); img.src = url;
    await img.decode();
    const c = document.createElement('canvas'); c.width = c.height = size;
    const ctx = c.getContext('2d');
    ctx.fillStyle = '#fff'; ctx.fillRect(0,0,size,size);
    ctx.drawImage(img, 0, 0, size, size);
    URL.revokeObjectURL(url);
    return await new Promise(res => c.toBlob(res, 'image/png'));
};
async function downloadQr() {
    const svg = getQrSvg(); if (!svg) return;
    const blob = await svgToPngBlob(svg);
    const a = document.createElement('a'); a.href = URL.createObjectURL(blob);
    a.download = 'qr-{{ $quiz->slug }}.png'; a.click(); URL.revokeObjectURL(a.href);
}
async function copyQr() {
    const svg = getQrSvg(); if (!svg) return;
    const blob = await svgToPngBlob(svg);
    if (navigator.clipboard?.write) {
        await navigator.clipboard.write([new ClipboardItem({'image/png':blob})]);
    } else { await downloadQr(); }
}
async function copyLink() {
    const val = document.getElementById('quizLink').value;
    await navigator.clipboard.writeText(val);
    const btn = event.currentTarget;
    btn.textContent = '✓'; setTimeout(() => btn.textContent = 'Copy', 1800);
}

// ── AI INSIGHT ──
window.pahamajaOpenAiInsight = async function(e) {
    if (e?.preventDefault) e.preventDefault();
    const modal   = document.getElementById('aiInsightModal');
    const loading = document.getElementById('aiInsightModalLoading');
    const tabs    = document.getElementById('aiInsightTabs');
    const views   = document.getElementById('aiInsightViews');
    const vA = document.getElementById('aiInsightViewAnalysis');
    const vR = document.getElementById('aiInsightViewReco');
    const vQ = document.getElementById('aiInsightViewQuestions');
    const vRaw = document.getElementById('aiInsightViewRaw');
    const err = document.getElementById('aiInsightModalError');

    modal.classList.add('open'); modal.setAttribute('aria-hidden','false');
    document.body.style.overflow = 'hidden';
    loading.style.display = 'flex';
    tabs.style.display = 'none'; views.style.display = 'none'; err.style.display = 'none';
    vA.innerHTML = vR.innerHTML = vQ.innerHTML = vRaw.innerHTML = '';

    try {
        const res = await fetch('{{ route('admin.quiz.ai-insights', $quiz->slug) }}', {
            method:'POST',
            headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json','X-Requested-With':'XMLHttpRequest'}
        });
        if (!res.ok) throw new Error('Gagal. HTTP ' + res.status);
        const data = await res.json();
        const text = data.insight || data.text || '';
        loading.style.display = 'none';
        tabs.style.display = ''; views.style.display = '';
        const renderSection = (t) => `<div style="background:#F9F8FD; border:1px solid #E5E3F0; border-radius:12px; padding:16px; font-size:13px; color:#374151; line-height:1.7; white-space:pre-wrap;">${t}</div>`;
        const sections = text.split(/(?=Ringkasan:|Diagnosis|Rekomendasi|Area Perhatian|Rencana)/g).filter(Boolean);
        vA.innerHTML = sections.map(renderSection).join('') || renderSection(text);
        vRaw.innerHTML = `<pre style="font-size:11px; color:#6B7280; overflow-x:auto; white-space:pre-wrap;">${text.replace(/</g,'&lt;')}</pre>`;
        document.querySelectorAll('.ai-tab').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.ai-tab').forEach(b => { b.style.background='transparent'; b.style.color='#6B7280'; });
                btn.style.background = '#7C3AED'; btn.style.color = '#fff'; btn.style.borderRadius = '8px';
                ['analysis','reco','questions','raw'].forEach(t => {
                    document.getElementById('aiInsightView'+t.charAt(0).toUpperCase()+t.slice(1)).style.display = btn.dataset.aiTab===t ? '' : 'none';
                });
            });
        });
        document.querySelector('.ai-tab[data-ai-tab=analysis]')?.click();
    } catch(e) {
        loading.style.display = 'none';
        err.style.display = ''; err.textContent = e.message;
    }
};
document.getElementById('btnCloseAiInsightModal')?.addEventListener('click', () => {
    document.getElementById('aiInsightModal').classList.remove('open');
    document.body.style.overflow = '';
});
document.getElementById('aiInsightModal')?.addEventListener('click', e => {
    if (e.target === e.currentTarget) { e.currentTarget.classList.remove('open'); document.body.style.overflow=''; }
});
document.getElementById('unparticipatedModal')?.addEventListener('click', e => {
    if (e.target === e.currentTarget) { e.currentTarget.classList.remove('open'); document.body.style.overflow=''; }
});
</script>
@endsection
