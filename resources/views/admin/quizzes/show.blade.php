@extends('layouts.app')

@section('title', $quiz->title . ' – Manage Quiz')
@section('meta_description', 'Kelola soal dan sesi kuis ' . $quiz->title)
@section('page_title', \Illuminate\Support\Str::limit($quiz->title, 36))
@section('page_subtitle', 'Manajemen Soal & Sesi Kuis')

@section('topbar_left')
    <a href="{{ route('admin.quizzes.index') }}" class="btn btn-ghost" style="padding:9px 12px; font-size:12px; background:rgba(124,58,237,0.08); border:1px solid rgba(124,58,237,0.15); color:var(--purple); margin-right:8px;">
        <i class="fa-solid fa-arrow-left"></i> Kembali
    </a>
@endsection

@section('topbar_actions')
    <a href="{{ route('admin.quizzes.edit', $quiz->slug) }}" class="btn btn-primary" style="padding:9px 16px; font-size:12px;">
        <i class="fa-solid fa-pen"></i> Edit
    </a>
    <a href="{{ route('admin.quiz.dashboard', $quiz->slug) }}" class="btn btn-ghost" style="padding:9px 16px; font-size:12px;">
        <i class="fa-solid fa-chart-line"></i> Analytics
    </a>
@endsection

@section('head_extra')
<style>
    .tab-nav { display:flex; gap:4px; background:#F3F2FB; border:1px solid #E5E3F0; border-radius:12px; padding:4px; margin-bottom:24px; }
    .tab-btn {
        padding:9px 20px; border-radius:9px; font-size:13px; font-weight:700;
        cursor:pointer; transition:all .2s; border:none; font-family:inherit; color:var(--text-muted);
        background:transparent;
    }
    .tab-btn.active { background:var(--white); color:var(--purple); box-shadow:0 2px 8px rgba(0,0,0,0.05); border:1px solid #E5E3F0; }
    .tab-btn:not(.active):hover { color:var(--text-primary); background:rgba(124,58,237,0.05); }

    /* Responsive Grid */
    .show-main-grid { display:grid; grid-template-columns:1fr 300px; gap:24px; align-items:start; }
    .show-stats-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin-bottom:24px; }
    .table-responsive { width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; }

    @media (max-width: 1100px) {
        .show-main-grid { grid-template-columns: 1fr; }
        .show-sidebar { order: 2; }
        .show-main-panel { order: 1; }
    }
    @media (max-width: 768px) {
        .show-stats-grid { grid-template-columns: 1fr; }
        .tab-nav { overflow-x: auto; padding-bottom: 8px; }
        .tab-btn { white-space: nowrap; }
    }

    .q-block { padding:24px; border-bottom:1px solid #F3F2F9; }
    .q-block:last-child { border-bottom:none; }
    .option-tag {
        display:inline-flex; align-items:center; gap:8px;
        padding:8px 14px; border-radius:10px; font-size:13px; font-weight:600; margin:5px 4px 0 0;
    }
    .opt-correct { background:rgba(16,185,129,0.1); border:1px solid rgba(16,185,129,0.2); color:#059669; }
    .opt-wrong   { background:#F9F8FD; border:1px solid #E5E3F0; color:var(--text-muted); }

    .session-card { background:#F9F8FD; border:1px solid #E5E3F0; border-radius:16px; padding:20px 24px; transition:all .2s; }
    .session-card:hover { border-color:var(--purple); transform:translateY(-2px); box-shadow:0 4px 12px rgba(0,0,0,0.05); }

    .info-row-r { display:flex; justify-content:space-between; align-items:center; padding:10px 0; border-bottom:1px solid #F3F2F9; font-size:13px; }
    .info-row-r:last-child { border-bottom:none; }
</style>
@endsection

@section('content')
<div class="show-main-grid fade-up">

    <!-- Main Panel -->
    <div class="show-main-panel">
        <!-- Tabs -->
        <div class="tab-nav">
            <button class="tab-btn active" id="tab-analytics" onclick="switchTab('analytics')">
                <i class="fa-solid fa-chart-pie" style="margin-right:6px;"></i> Analitik
            </button>
            <button class="tab-btn" id="tab-questions" onclick="switchTab('questions')">
                <i class="fa-solid fa-list-ul" style="margin-right:6px;"></i> Soal ({{ $quiz->questions->count() }})
            </button>
            <button class="tab-btn" id="tab-sessions" onclick="switchTab('sessions')">
                <i class="fa-solid fa-calendar-days" style="margin-right:6px;"></i> Sesi
            </button>
        </div>

        <!-- ANALYTICS TAB -->
        <div id="content-analytics">
            <!-- Summary Row -->
            <div class="show-stats-grid">
                <div class="card" style="padding:20px; display:flex; align-items:center; gap:16px;">
                    <div id="gaugeChartShow" style="width:80px; height:80px;"></div>
                    <div>
                        <div style="font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:.05em;">Rata-rata Skor</div>
                        <div style="font-size:24px; font-weight:900; color:var(--text-primary);">{{ number_format($avgScore, 1) }}</div>
                    </div>
                </div>
                <div class="card" style="padding:20px; display:flex; align-items:center; gap:16px;">
                    <div style="width:48px; height:48px; border-radius:12px; background:rgba(124,58,237,0.1); color:var(--purple); display:flex; align-items:center; justify-content:center; font-size:20px;">
                        <i class="fa-solid fa-medal"></i>
                    </div>
                    <div>
                        <div style="font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:.05em;">Tingkat Lulus</div>
                        @php
                            $completed = $quiz->participants->whereNotNull('score')->count();
                            $passed = $quiz->participants->filter(fn($p) => !is_null($p->score) && $p->score >= $quiz->passing_score)->count();
                            $passRate = $completed > 0 ? round(($passed / $completed) * 100) : 0;
                        @endphp
                        <div style="font-size:24px; font-weight:900; color:var(--purple);">{{ $passRate }}%</div>
                    </div>
                </div>
                <div class="card" style="padding:20px; display:flex; align-items:center; gap:16px;">
                    <div style="width:48px; height:48px; border-radius:12px; background:rgba(79,70,229,0.1); color:var(--indigo); display:flex; align-items:center; justify-content:center; font-size:20px;">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <div>
                        <div style="font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:.05em;">Total Peserta</div>
                        <div style="font-size:24px; font-weight:900; color:var(--indigo);">{{ $quiz->participants->count() }}</div>
                    </div>
                </div>
            </div>

            <!-- Participant Analytics (Leaderboard) -->
            <div class="card" style="margin-bottom:24px;">
                <div style="padding:18px 24px; border-bottom:1px solid #E5E3F0; display:flex; justify-content:space-between; align-items:center;">
                    <h3 style="font-size:14px; font-weight:800; color:var(--text-primary); margin:0;">Peringkat Peserta</h3>
                    <a href="{{ route('admin.quiz.dashboard', $quiz->slug) }}" style="font-size:12px; font-weight:700; color:var(--purple); text-decoration:none;">Lihat Semua</a>
                </div>
                <div class="table-responsive">
                    <table class="data-table" style="border:none;">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama Peserta</th>
                                <th>Nilai</th>
                                <th>Status</th>
                                <th style="text-align:right;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sortedParticipants->take(5) as $idx => $p)
                            <tr>
                                <td style="width:40px; font-weight:800; color:var(--text-muted);">{{ $idx + 1 }}</td>
                                <td>
                                    <div style="display:flex; align-items:center; gap:10px;">
                                        @php
                                            $pEmp = $p->employee;
                                            $pInit = strtoupper(substr($p->name, 0, 1));
                                        @endphp
                                        <div style="width:32px; height:32px; border-radius:8px; background:linear-gradient(135deg,#7C3AED,#4F46E5); display:flex; align-items:center; justify-content:center; color:#fff; font-size:12px; font-weight:800; flex-shrink:0; overflow:hidden;">
                                            @if($pEmp && $pEmp->avatar)
                                                <img src="/storage/{{ $pEmp->avatar }}" style="width:100%; height:100%; object-fit:cover;">
                                            @else
                                                {{ $pInit }}
                                            @endif
                                        </div>
                                        <div>
                                            <div style="font-size:13px; font-weight:700; color:var(--text-primary);">{{ $p->name }}</div>
                                            <div style="font-size:11px; color:var(--text-muted);">{{ $p->nim }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($p->status === 'pending_review')
                                        <span class="badge badge-yellow" onclick="window.location.href='{{ route('admin.participant.review', ['quiz' => $quiz->slug, 'participant' => $p->id]) }}'" style="cursor:pointer;">Review Esai</span>
                                    @elseif(!is_null($p->score))
                                        <span style="font-size:15px; font-weight:900; color:{{ $p->score >= $quiz->passing_score ? '#059669' : '#DC2626' }};">{{ $p->score }}</span>
                                    @else
                                        <span class="badge badge-purple">Mengerjakan</span>
                                    @endif
                                </td>
                                <td>
                                    @if(!is_null($p->score))
                                        <span class="badge {{ $p->score >= $quiz->passing_score ? 'badge-green' : 'badge-red' }}">{{ $p->score >= $quiz->passing_score ? 'Lulus' : 'Gagal' }}</span>
                                    @else
                                        <span style="color:var(--text-muted); font-size:11px;">—</span>
                                    @endif
                                </td>
                                <td style="text-align:right;">
                                    @if($p->status === 'pending_review')
                                        <a href="{{ route('admin.participant.review', ['quiz' => $quiz->slug, 'participant' => $p->id]) }}" 
                                           class="btn btn-primary" style="padding:4px 12px; font-size:11px;">
                                            <i class="fa-solid fa-pen-nib"></i> Nilai
                                        </a>
                                    @else
                                        <a href="{{ route('admin.participant.answers', ['quiz' => $quiz->slug, 'participant' => $p->id]) }}" 
                                           class="btn btn-ghost" style="padding:4px 8px; font-size:11px;" title="Lihat Jawaban">
                                            <i class="fa-solid fa-eye" style="color:var(--purple);"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" style="text-align:center; padding:30px; color:var(--text-muted);">Belum ada peserta</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Question Analytics -->
            <div class="card">
                <div style="padding:18px 24px; border-bottom:1px solid #E5E3F0;">
                    <h3 style="font-size:14px; font-weight:800; color:var(--text-primary); margin:0;">Analisis Soal</h3>
                </div>
                <div class="table-responsive">
                    <table class="data-table" style="border:none;">
                        <thead>
                            <tr>
                                <th>Soal</th>
                                <th>Tingkat Benar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($questionAnalytics->take(5) as $idx => $row)
                            <tr>
                                <td>
                                    <div style="font-size:13px; color:var(--text-primary); line-height:1.4; max-width:400px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                        <span style="color:var(--purple); font-weight:800; margin-right:4px;">#{{ $idx+1 }}</span> {{ $row['text'] }}
                                    </div>
                                </td>
                                <td>
                                    @if(!is_null($row['correct_rate']))
                                        <div style="display:flex; align-items:center; gap:10px;">
                                            <div class="progress-bar-track" style="width:80px; height:6px;">
                                                <div class="progress-bar-fill" style="width:{{ $row['correct_rate'] }}%; background:{{ $row['correct_rate'] >= 70 ? '#059669' : ($row['correct_rate'] >= 40 ? '#D97706' : '#DC2626') }};"></div>
                                            </div>
                                            <span style="font-size:12px; font-weight:800;">{{ $row['correct_rate'] }}%</span>
                                        </div>
                                    @else
                                        <span style="font-size:11px; color:var(--text-muted);">N/A</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div style="padding:12px; text-align:center; border-top:1px solid #F3F2F9;">
                    <a href="{{ route('admin.quiz.dashboard', $quiz->slug) }}" style="font-size:11px; font-weight:700; color:var(--purple); text-decoration:none;">LIHAT ANALISA LENGKAP <i class="fa-solid fa-arrow-right" style="margin-left:4px;"></i></a>
                </div>
            </div>
        </div>

        <!-- QUESTIONS TAB -->
        <div id="content-questions" style="display:none;">
            @if(session('success'))
            <div style="background:rgba(16,185,129,0.1); border:1px solid rgba(16,185,129,0.25); color:#34D399; padding:12px 20px; border-radius:12px; margin-bottom:18px; font-size:13px; font-weight:700;">
                <i class="fa-solid fa-circle-check" style="margin-right:6px;"></i>{{ session('success') }}
            </div>
            @endif

            <div class="card" style="border-radius:20px; overflow:hidden;">
                <div style="padding:20px 24px; border-bottom:1px solid rgba(255,255,255,0.07); display:flex; align-items:center; gap:12px;">
                    <div style="width:38px; height:38px; border-radius:11px; background:linear-gradient(135deg,var(--purple),var(--indigo)); display:flex; align-items:center; justify-content:center; color:#fff; font-size:14px;">
                        <i class="fa-solid fa-clipboard-question"></i>
                    </div>
                    <div>
                        <div style="font-size:14px; font-weight:800; color:var(--text-primary);">Daftar Pertanyaan</div>
                        <div style="font-size:11px; color:var(--text-muted);">{{ $quiz->questions->count() }} soal tersedia</div>
                    </div>
                </div>

                @forelse($quiz->questions as $idx => $question)
                <div class="q-block">
                    <div style="display:flex; align-items:flex-start; gap:14px; margin-bottom:14px;">
                        <span style="width:32px; height:32px; border-radius:10px; background:rgba(124,58,237,0.15); color:#A78BFA; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:800; flex-shrink:0;">
                            {{ $idx + 1 }}
                        </span>
                        <div style="flex:1;">
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
                                <div style="font-size:15px; font-weight:700; color:var(--text-primary); line-height:1.5;">
                                    {{ $question->question ?? $question->text }}
                                </div>
                                <span class="badge {{ $question->type === 'essay' ? 'badge-yellow' : 'badge-purple' }}" style="font-size:9px; padding:3px 8px;">{{ strtoupper($question->type ?? 'mcq') }}</span>
                            </div>
                        </div>
                    </div>
                    @if(($question->type ?? 'mcq') === 'mcq')
                    <div style="padding-left:46px; display:flex; flex-wrap:wrap; gap:6px;">
                        @php
                            $optMap = ['A'=>'option_a','B'=>'option_b','C'=>'option_c','D'=>'option_d'];
                            $correct = strtoupper($question->correct_answer ?? '');
                        @endphp
                        @foreach($optMap as $letter => $field)
                            @if(!empty($question->$field))
                            <span class="option-tag {{ $letter === $correct ? 'opt-correct' : 'opt-wrong' }}">
                                @if($letter === $correct)<i class="fa-solid fa-check" style="font-size:10px;"></i>@endif
                                <strong>{{ $letter }}.</strong> {{ $question->$field }}
                            </span>
                            @endif
                        @endforeach
                    </div>
                    @else
                    <div style="padding-left:46px;">
                        <div style="background:rgba(245,158,11,0.03); border:1px solid rgba(245,158,11,0.1); border-radius:12px; padding:16px;">
                            <div style="font-size:11px; font-weight:800; color:#D97706; text-transform:uppercase; margin-bottom:8px;">Kunci Jawaban Ideal</div>
                            <div style="font-size:13px; color:var(--text-primary); line-height:1.6; font-weight:600;">{{ $question->ideal_answer }}</div>
                        </div>
                    </div>
                    @endif
                    @if(!empty($question->explanation))
                    <div style="margin-top:12px; padding:10px 14px; padding-left:46px; font-size:12px; color:#A78BFA; line-height:1.6; background:rgba(124,58,237,0.06); border-radius:10px; margin-left:46px;">
                        <i class="fa-solid fa-lightbulb" style="margin-right:6px;"></i>{{ $question->explanation }}
                    </div>
                    @endif
                </div>
                @empty
                <div style="padding:48px 24px; text-align:center;">
                    <div style="font-size:40px; color:#4E4C6A; margin-bottom:12px;"><i class="fa-solid fa-inbox"></i></div>
                    <div style="color:#8B8AAE; font-size:13px;">Belum ada soal untuk kuis ini</div>
                    <div style="margin-top:16px; display:flex; gap:10px; justify-content:center; flex-wrap:wrap;">
                        <a href="{{ route('admin.quizzes.edit', $quiz->slug) }}" class="btn btn-primary" style="font-size:13px; padding:9px 18px;">
                            <i class="fa-solid fa-pen"></i> Edit & Tambah Soal
                        </a>
                        <a href="{{ route('admin.quizzes.ai-create') }}" class="btn btn-ghost" style="font-size:13px; padding:9px 18px;">
                            <i class="fa-solid fa-robot"></i> Generate AI
                        </a>
                    </div>
                </div>
                @endforelse
            </div>
        </div>

        <!-- SESSIONS TAB -->
        <div id="content-sessions" style="display:none;">
            <div class="card" style="border-radius:20px; overflow:hidden;">
                <div style="padding:20px 24px; border-bottom:1px solid rgba(255,255,255,0.07); display:flex; align-items:center; justify-content:space-between;">
                    <div style="display:flex; align-items:center; gap:12px;">
                        <div style="width:38px; height:38px; border-radius:11px; background:linear-gradient(135deg,var(--indigo),var(--purple)); display:flex; align-items:center; justify-content:center; color:#fff; font-size:14px;">
                            <i class="fa-solid fa-calendar-check"></i>
                        </div>
                        <div>
                            <div style="font-size:14px; font-weight:800; color:var(--text-primary);">Sesi Pengerjaan</div>
                            <div style="font-size:11px; color:var(--text-muted);">{{ $sessions->count() }} sesi terdaftar</div>
                        </div>
                    </div>
                    <button onclick="openSessionModal()" class="btn btn-primary" style="padding:9px 16px; font-size:12px;">
                        <i class="fa-solid fa-plus"></i> Tambah Sesi
                    </button>
                </div>

                <div style="padding:20px; display:flex; flex-direction:column; gap:12px;">
                    @forelse($sessions as $session)
                    <div class="session-card">
                        <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:12px;">
                            <div>
                                <div style="font-size:15px; font-weight:800; color:var(--text-primary); margin-bottom:6px;">{{ $session->name }}</div>
                                <div style="font-size:12px; color:var(--text-muted); font-weight:600;">
                                    <i class="fa-solid fa-calendar" style="margin-right:4px; color:var(--purple);"></i>
                                    {{ $session->start_time->format('d M Y, H:i') }} – {{ $session->end_time->format('H:i') }}
                                </div>
                                <div style="font-size:12px; color:var(--text-muted); font-weight:600; margin-top:4px;">
                                    <i class="fa-solid fa-users" style="margin-right:4px; color:var(--indigo);"></i>
                                    {{ $session->participants->count() }} peserta ditugaskan
                                </div>
                            </div>
                            <div style="display:flex; gap:6px; flex-shrink:0;">
                                <button onclick="openAssignModal({{ $session->id }}, '{{ addslashes($session->name) }}')"
                                        class="btn btn-ghost" style="padding:7px 12px; font-size:11px;">
                                    <i class="fa-solid fa-users-gear"></i> Kelola
                                </button>
                                <form action="{{ route('admin.sessions.destroy', $session->id) }}" method="POST"
                                      onsubmit="event.preventDefault(); PahamAja.confirm('Hapus Sesi', 'Apakah Anda yakin ingin menghapus sesi ini?', 'danger', () => this.submit())">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" style="padding:7px 10px; font-size:12px;">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div style="padding:40px; text-align:center;">
                        <div style="font-size:36px; color:#4E4C6A; margin-bottom:12px;"><i class="fa-solid fa-calendar-xmark"></i></div>
                        <div style="color:#8B8AAE; font-size:13px; margin-bottom:16px;">Belum ada sesi. Kuis dapat diakses bebas oleh semua karyawan.</div>
                        <button onclick="openSessionModal()" class="btn btn-primary" style="font-size:13px; padding:9px 20px;">
                            <i class="fa-solid fa-plus"></i> Buat Sesi Pertama
                        </button>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Right Sidebar -->
    <div style="display:flex; flex-direction:column; gap:16px;">
        <!-- Quiz Info -->
        <div style="background:linear-gradient(135deg,rgba(124,58,237,0.1),rgba(79,70,229,0.06)); border:1px solid rgba(124,58,237,0.2); border-radius:20px; padding:24px;">
            <div style="font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:.15em; color:var(--purple); margin-bottom:16px;">
                Info Kuis
            </div>
            <div class="info-row-r">
                <span style="color:var(--text-muted);">Dibuat pada</span>
                <span style="font-weight:800; color:var(--text-primary);">{{ $quiz->created_at->format('d M Y') }}</span>
            </div>
            <div class="info-row-r">
                <span style="color:var(--text-muted);">Durasi</span>
                <span style="font-weight:800; color:var(--text-primary);">{{ $quiz->time_limit }} menit</span>
            </div>
            <div class="info-row-r">
                <span style="color:var(--text-muted);">Nilai Lulus</span>
                <span style="font-weight:800; color:var(--text-primary);">{{ $quiz->passing_score }}%</span>
            </div>
            <div class="info-row-r">
                <span style="color:var(--text-muted);">Total Soal</span>
                <span style="font-weight:800; color:var(--text-primary);">{{ $quiz->questions->count() }}</span>
            </div>
            <div class="info-row-r">
                <span style="color:var(--text-muted);">Peserta</span>
                <span style="font-weight:800; color:var(--text-primary);">{{ $quiz->participants_count ?? $quiz->participants->count() }}</span>
            </div>
        </div>

        <!-- Share Link & Barcode -->
        <div class="card" style="border-radius:18px; padding:20px;">
            <div style="font-size:13px; font-weight:800; color:var(--text-primary); margin-bottom:12px; display:flex; align-items:center; justify-content:space-between;">
                <span><i class="fa-solid fa-qrcode" style="color:var(--purple); margin-right:6px;"></i> Akses Kuis</span>
                <span style="font-size:10px; color:var(--text-muted); font-weight:600; text-transform:uppercase;">Scan Barcode</span>
            </div>
            
            <!-- Barcode Display -->
            <div style="background:#fff; border:1px solid #F3F2FB; border-radius:12px; padding:16px; display:flex; justify-content:center; margin-bottom:16px; box-shadow:inset 0 2px 4px rgba(0,0,0,0.02);">
                {!! QrCode::size(140)->color(30,27,75)->margin(1)->generate(route('quiz.join', $quiz->slug)) !!}
            </div>

            <div style="background:#F9F8FD; border:1px solid #E5E3F0; border-radius:10px; padding:10px 14px; display:flex; align-items:center; gap:8px;">
                <span id="quizLinkText" style="font-size:11px; color:var(--text-muted); flex:1; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                    {{ route('quiz.join', $quiz->slug) }}
                </span>
                <button onclick="copyLink()" class="btn btn-ghost" style="padding:5px 10px; font-size:11px; flex-shrink:0; background:#fff;">
                    <i class="fa-solid fa-copy"></i>
                </button>
            </div>
        </div>

        <!-- Actions -->
        <div class="card" style="border-radius:18px; padding:20px; display:flex; flex-direction:column; gap:10px;">
            <div style="font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:.15em; color:var(--text-muted); margin-bottom:6px;">Aksi</div>
            <a href="{{ route('admin.quizzes.edit', $quiz->slug) }}" class="btn btn-ghost" style="justify-content:flex-start; width:100%;">
                <i class="fa-solid fa-pen" style="width:14px;"></i> Edit Kuis
            </a>
            <a href="{{ route('admin.quiz.export', $quiz->slug) }}" class="btn btn-ghost" style="justify-content:flex-start; width:100%;">
                <i class="fa-solid fa-file-excel" style="width:14px; color:#34D399;"></i> Export Excel
            </a>
            <a href="{{ route('admin.quiz.export-pdf', $quiz->slug) }}" class="btn btn-ghost" style="justify-content:flex-start; width:100%;">
                <i class="fa-solid fa-file-pdf" style="width:14px; color:#F87171;"></i> Export PDF
            </a>
            <div style="height:1px; background:rgba(255,255,255,0.07);"></div>
            <form action="{{ route('admin.quizzes.destroy', $quiz->id) }}" method="POST"
                  onsubmit="event.preventDefault(); PahamAja.confirm('Hapus Kuis', 'Hapus kuis ini secara permanen? Data peserta dan sesi akan hilang.', 'danger', () => this.submit())">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" style="justify-content:flex-start; width:100%;">
                    <i class="fa-solid fa-trash" style="width:14px;"></i> Hapus Kuis
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Add Session -->
<div id="sessionModal" class="modal-overlay" onclick="if(event.target===this) closeSessionModal()">
    <div class="modal-box" style="max-width:480px;">
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:24px;">
            <div>
                <h3 style="font-size:18px; font-weight:900; color:#fff;">Tambah Sesi Baru</h3>
                <p style="font-size:12px; color:#8B8AAE; margin-top:4px;">Tentukan nama dan jadwal sesi</p>
            </div>
            <button onclick="closeSessionModal()" class="btn btn-ghost" style="padding:8px 10px;"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="{{ route('admin.quizzes.sessions.store', $quiz->slug) }}" method="POST" style="display:flex; flex-direction:column; gap:16px;">
            @csrf
            <div>
                <label class="form-label">Nama Sesi</label>
                <input type="text" name="name" required class="form-input" placeholder="Misal: Sesi Pagi Batch A">
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
                <div>
                    <label class="form-label">Waktu Mulai</label>
                    <input type="datetime-local" name="start_time" required class="form-input">
                </div>
                <div>
                    <label class="form-label">Waktu Selesai</label>
                    <input type="datetime-local" name="end_time" required class="form-input">
                </div>
            </div>
            <div style="display:flex; gap:10px; margin-top:4px;">
                <button type="button" onclick="closeSessionModal()" class="btn btn-ghost" style="flex:1; justify-content:center;">Batal</button>
                <button type="submit" class="btn btn-primary" style="flex:1; justify-content:center;">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Sesi
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Assign Participants -->
<div id="assignModal" class="modal-overlay" onclick="if(event.target===this) closeAssignModal()">
    <div class="modal-box" style="max-width:520px; max-height:80vh; display:flex; flex-direction:column;">
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; flex-shrink:0;">
            <div>
                <h3 style="font-size:18px; font-weight:900; color:#fff;" id="assignModalTitle">Kelola Peserta Sesi</h3>
                <p style="font-size:12px; color:#8B8AAE; margin-top:4px;">Pilih karyawan untuk sesi ini</p>
            </div>
            <button onclick="closeAssignModal()" class="btn btn-ghost" style="padding:8px 10px;"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div style="flex-shrink:0; margin-bottom:14px;">
            <div style="position:relative;">
                <i class="fa-solid fa-search" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#4E4C6A; font-size:12px;"></i>
                <input type="text" id="empSearchModal" placeholder="Cari nama atau NIM..."
                       class="form-input" style="padding-left:36px;"
                       oninput="filterEmp(this.value)">
            </div>
        </div>
        <div style="overflow-y:auto; flex:1; display:flex; flex-direction:column; gap:8px; padding-right:4px;" id="empListModal">
            @foreach($employees as $emp)
            <label style="display:flex; align-items:center; gap:12px; background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.07); border-radius:12px; padding:12px 16px; cursor:pointer; transition:all .2s;"
                   data-search="{{ strtolower($emp->name . ' ' . $emp->nim) }}" class="emp-row"
                   onmouseover="this.style.borderColor='rgba(124,58,237,0.25)'" onmouseout="this.style.borderColor='rgba(255,255,255,0.07)'">
                <input type="checkbox" name="employee_ids[]" value="{{ $emp->id }}" style="width:16px; height:16px; accent-color:#7C3AED; flex-shrink:0;">
                <div style="width:36px; height:36px; border-radius:10px; background:linear-gradient(135deg,#7C3AED,#4F46E5); display:flex; align-items:center; justify-content:center; color:#fff; font-size:12px; font-weight:800; flex-shrink:0;">
                    {{ strtoupper(substr($emp->name, 0, 1)) }}
                </div>
                <div>
                    <div style="font-size:13px; font-weight:700; color:#F1F0FF;">{{ $emp->name }}</div>
                    <div style="font-size:11px; color:#8B8AAE;">{{ $emp->nim }} · {{ $emp->position }}</div>
                </div>
            </label>
            @endforeach
        </div>
        <div style="display:flex; gap:10px; margin-top:16px; flex-shrink:0;">
            <button onclick="closeAssignModal()" class="btn btn-ghost" style="flex:1; justify-content:center;">Batal</button>
            <button onclick="submitAssign()" class="btn btn-primary" style="flex:1; justify-content:center;" id="btnSaveAssign">
                <i class="fa-solid fa-users-gear"></i> Tugaskan
            </button>
        </div>
    </div>
</div>
<form id="assignForm" method="POST" style="display:none;"></form>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="{{ asset('js/prevent-double-submit.js') }}"></script>
<script>
function switchTab(tab) {
    ['analytics','questions','sessions'].forEach(t => {
        const content = document.getElementById('content-' + t);
        if (content) content.style.display = t === tab ? '' : 'none';
        
        const btn = document.getElementById('tab-' + t);
        if (btn) btn.className = 'tab-btn' + (t === tab ? ' active' : '');
    });
}

// Analytics Charts
document.addEventListener('DOMContentLoaded', () => {
    const avg = {{ number_format($avgScore, 1) }};
    const el  = document.querySelector('#gaugeChartShow');
    if (el && typeof ApexCharts !== 'undefined') {
        new ApexCharts(el, {
            series: [avg],
            chart: { type:'radialBar', height:100, sparkline:{enabled:true}, fontFamily:'Plus Jakarta Sans, sans-serif' },
            plotOptions: { radialBar: {
                hollow: { size:'50%' },
                track: { background:'#F3F2FB' },
                dataLabels: { name:{show:false}, value:{show:true, fontSize:'13px', fontWeight:900, color: '#1E1B4B', offsetY:5, formatter:v => v} }
            }},
            colors: ['#7C3AED']
        }).render();
    }
});

function openSessionModal()  { document.getElementById('sessionModal').classList.add('open'); document.body.style.overflow='hidden'; }
function closeSessionModal() { document.getElementById('sessionModal').classList.remove('open'); document.body.style.overflow=''; }

let currentSessionId = null;
function openAssignModal(sessionId, name) {
    currentSessionId = sessionId;
    document.getElementById('assignModalTitle').textContent = 'Kelola Peserta: ' + name;
    document.getElementById('assignModal').classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeAssignModal()  { document.getElementById('assignModal').classList.remove('open'); document.body.style.overflow=''; }

function filterEmp(q) {
    q = q.toLowerCase();
    document.querySelectorAll('.emp-row').forEach(row => {
        row.style.display = (!q || row.dataset.search.includes(q)) ? '' : 'none';
    });
}

function submitAssign() {
    const form = document.getElementById('assignForm');
    const checked = document.querySelectorAll('#empListModal input[type=checkbox]:checked');
    form.innerHTML = `<input type="hidden" name="_token" value="{{ csrf_token() }}">`;
    checked.forEach(cb => {
        const i = document.createElement('input');
        i.type = 'hidden'; i.name = 'employee_ids[]'; i.value = cb.value;
        form.appendChild(i);
    });
    form.action = `/admin/sessions/${currentSessionId}/assign`;
    form.method = 'POST';
    form.submit();
}

function copyLink() {
    const text = document.getElementById('quizLinkText').textContent.trim();
    navigator.clipboard.writeText(text).then(() => {
        const btn = event.currentTarget;
        btn.innerHTML = '<i class="fa-solid fa-check"></i>';
        setTimeout(() => btn.innerHTML = '<i class="fa-solid fa-copy"></i>', 1500);
    });
}

// Mobile grid collapse
const grid = document.querySelector('[style*="grid-template-columns:1fr 300px"]');
if (grid && window.innerWidth < 900) grid.style.gridTemplateColumns = '1fr';
</script>
@endsection
