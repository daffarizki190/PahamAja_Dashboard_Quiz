@extends('layouts.app')

@section('title', 'Dashboard – PahamAja')
@section('meta_description', 'Kelola dan pantau semua kuis assessment')
@section('search_placeholder', 'Cari nama kuis...')
@section('show_search', true)

@section('topbar_actions')
    <a href="{{ route('admin.quizzes.create') }}" class="btn btn-primary" style="padding:8px 16px; font-size:13px;">
        <i class="fa-solid fa-plus"></i> Tambah
    </a>
@endsection

@section('head_extra')
<style>
    /* ── 3D Emoji Stat Cards ── */
    .big-stat {
        background: #fff; border: 1px solid #E8E6F0; border-radius: 18px;
        padding: 24px 28px 20px;
        display: flex; align-items: center; justify-content: space-between;
        box-shadow: 0 1px 4px rgba(0,0,0,0.04); transition: all 0.2s;
    }
    .big-stat:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,0.09); }
    .big-stat-label { font-size: 13px; font-weight: 600; color: #6B7280; margin-bottom: 8px; }
    .big-stat-value { font-size: 42px; font-weight: 900; color: #1E1B4B; line-height: 1; }
    .big-stat-emoji { font-size: 56px; flex-shrink: 0; filter: drop-shadow(0 4px 10px rgba(0,0,0,0.15)); }

    /* ── QUIZ CARD ── */
    .quiz-card {
        background: #fff; border: 1px solid #E8E6F0; border-radius: 16px;
        padding: 18px 20px; display: flex; align-items: flex-start; gap: 16px;
        transition: all 0.2s; cursor: default;
        box-shadow: 0 1px 4px rgba(0,0,0,0.04);
    }
    .quiz-card:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,0.09); }
    .quiz-card-icon {
        width: 52px; height: 52px; border-radius: 12px;
        background: #EDE9FE; display: flex; align-items: center; justify-content: center;
        font-size: 30px; flex-shrink: 0;
        filter: drop-shadow(0 3px 8px rgba(0,0,0,0.12));
    }
    .quiz-card-body { flex: 1; min-width: 0; }
    .quiz-card-title { font-size: 14px; font-weight: 800; color: #1E1B4B; margin-bottom: 6px; line-height: 1.3; }
    .quiz-card-meta  { font-size: 12px; color: #6B7280; font-weight: 500; display: flex; align-items: center; gap: 12px; margin-bottom: 10px; }
    .quiz-card-meta i { font-size: 10px; }
    .quiz-card-footer { display: flex; align-items: center; justify-content: space-between; }
    .quiz-participants  { font-size: 12px; color: #6B7280; font-weight: 600; display: flex; align-items: center; gap: 5px; }

    /* Responsive Grid */
    .quiz-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 22px;
    }

    @media (max-width: 640px) {
        .quiz-grid { grid-template-columns: 1fr; }
    }

    /* Status badges */
    .status-aktif   { background: #7C3AED; color: #fff; border-radius: 20px; padding: 4px 14px; font-size: 11px; font-weight: 800; }
    .status-selesai { background: #10B981; color: #fff; border-radius: 20px; padding: 4px 14px; font-size: 11px; font-weight: 800; }
    .status-draft   { background: #F59E0B; color: #fff; border-radius: 20px; padding: 4px 14px; font-size: 11px; font-weight: 800; }

    /* Action bar icons on hover */
    .quiz-card-actions {
        display: flex; gap: 4px; opacity: 0; transition: opacity 0.2s;
    }
    .quiz-card:hover .quiz-card-actions { opacity: 1; }
</style>
@endsection

@section('content')
@php
    $totalKuis      = $quizzes->count();
    $totalPeserta   = $quizzes->sum(fn($q) => $q->participants->count());
    $totalPertanyaan= $quizzes->sum(fn($q) => $q->questions->count());

    /* Emoji icon pool for quiz cards */
    $iconPool = ['📝','🧠','🎯','🔥','🧬'];
@endphp

<!-- Stat Row -->
<div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap:22px; margin-bottom:22px;" class="fade-up">
    <div class="big-stat">
        <div>
            <div class="big-stat-label">Total Kuis</div>
            <div class="big-stat-value">{{ $totalKuis }}</div>
        </div>
        <div class="big-stat-emoji">📋</div>
    </div>
    <div class="big-stat delay-1">
        <div>
            <div class="big-stat-label">Total Peserta</div>
            <div class="big-stat-value">{{ $stats['employees'] }}</div>
        </div>
        <div class="big-stat-emoji">👥</div>
    </div>
    <div class="big-stat delay-2">
        <div>
            <div class="big-stat-label">Total Pertanyaan</div>
            <div class="big-stat-value">{{ $totalPertanyaan }}</div>
        </div>
        <div class="big-stat-emoji">❓</div>
    </div>
</div>

<!-- Global Leaderboard Widget -->
@if(isset($topEmployees) && $topEmployees->count() > 0)
<div class="card fade-up delay-2" style="background: var(--bg-card); border: 1px solid var(--border); border-radius: 18px; margin-bottom: 22px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.03);">
    <div style="padding: 16px 24px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; background: linear-gradient(90deg, rgba(124,58,237,0.03), transparent);">
        <div style="display: flex; align-items: center; gap: 12px;">
            <div style="width: 36px; height: 36px; border-radius: 10px; background: linear-gradient(135deg, #F59E0B, #EA580C); display: flex; align-items: center; justify-content: center; color: #fff; font-size: 16px;">
                <i class="fa-solid fa-trophy"></i>
            </div>
            <div>
                <h3 style="margin: 0; font-size: 15px; font-weight: 800; color: var(--text-primary);">Leaderboard Global</h3>
                <p style="margin: 0; font-size: 12px; color: var(--text-muted);">Top 5 Karyawan dengan rata-rata skor terbaik</p>
            </div>
        </div>
        <a href="{{ route('admin.employees.index') }}" class="btn btn-ghost" style="padding: 6px 12px; font-size: 12px;">Lihat Semua</a>
    </div>
    <div style="padding: 0;">
        <table style="width: 100%; border-collapse: collapse;">
            <tbody>
                @foreach($topEmployees as $idx => $emp)
                <tr style="border-bottom: 1px solid var(--border); transition: background 0.2s;" onmouseover="this.style.background='rgba(124,58,237,0.02)'" onmouseout="this.style.background='transparent'">
                    <td style="padding: 14px 24px; width: 50px;">
                        @if($idx === 0) <span style="font-size: 24px;">🥇</span>
                        @elseif($idx === 1) <span style="font-size: 24px;">🥈</span>
                        @elseif($idx === 2) <span style="font-size: 24px;">🥉</span>
                        @else <span style="font-size: 14px; font-weight: 800; color: var(--text-dim);">#{{ $idx + 1 }}</span>
                        @endif
                    </td>
                    <td style="padding: 14px 24px;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="width: 38px; height: 38px; border-radius: 10px; background: linear-gradient(135deg, var(--purple), var(--indigo)); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 14px; overflow: hidden;">
                                @if($emp->avatar)
                                    <img src="{{ avatar_url($emp->avatar) }}" style="width: 100%; height: 100%; object-fit: cover;">
                                @else
                                    {{ strtoupper(substr($emp->name, 0, 1)) }}
                                @endif
                            </div>
                            <div>
                                <div style="font-weight: 700; color: var(--text-primary); font-size: 14px;">{{ $emp->name }}</div>
                                <div style="font-size: 11px; color: var(--text-muted);">{{ $emp->department }} &bull; {{ $emp->quizzes_taken }} Kuis</div>
                            </div>
                        </div>
                    </td>
                    <td style="padding: 14px 24px; text-align: right;">
                        <div style="font-size: 20px; font-weight: 900; color: var(--purple);">{{ $emp->avg_score }}</div>
                        <div style="font-size: 10px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">Rata-rata</div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<!-- Quiz Grid -->
<div class="quiz-grid fade-up delay-1" id="quizGrid">
    @php $idx = 0; @endphp
    @forelse($quizzes as $quiz)
    @php
        $total    = $quiz->participants->unique('employee_id')->count();
        $passingScore = $quiz->passing_score ?? 70;
        $passed   = $quiz->participants->whereNotNull('score')->groupBy('employee_id')->map(fn($a) => $a->max('score'))->filter(fn($s) => $s >= $passingScore)->count();
        $icon     = $iconPool[$idx++ % count($iconPool)];

        /* Simple status logic: any participant who hasn't finished = aktif, else selesai */
        $hasActive = $quiz->participants->whereNull('finished_at')->count() > 0;
        $statusClass = $hasActive ? 'status-aktif' : 'status-selesai';
        $statusLabel = $hasActive ? 'Aktif' : 'Selesai';
    @endphp
    <div class="quiz-card" data-name="{{ strtolower($quiz->title) }}">
        <div class="quiz-card-icon">{{ $icon }}</div>
        <div class="quiz-card-body">
            <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:8px;">
                <div class="quiz-card-title">{{ $quiz->title }}</div>
                <div style="display:flex; gap:5px; flex-shrink:0;">
                    <a href="{{ route('admin.quizzes.show', $quiz->slug) }}"
                       style="width:26px;height:26px;background:#F3F2FB;border-radius:6px;display:flex;align-items:center;justify-content:center;color:#7C3AED;font-size:11px;text-decoration:none;transition:background .18s;"
                       title="Analitik" onmouseover="this.style.background='#EDE9FE'" onmouseout="this.style.background='#F3F2FB'">
                        <i class="fa-solid fa-chart-line"></i>
                    </a>
                    <a href="{{ route('admin.quizzes.edit', $quiz->slug) }}"
                       style="width:26px;height:26px;background:#F3F2FB;border-radius:6px;display:flex;align-items:center;justify-content:center;color:#6B7280;font-size:11px;text-decoration:none;transition:background .18s;"
                       title="Edit" onmouseover="this.style.background='#F9F8FD'" onmouseout="this.style.background='#F3F2FB'">
                        <i class="fa-solid fa-pen"></i>
                    </a>
                </div>
            </div>
            <div class="quiz-card-meta">
                <span><i class="fa-regular fa-calendar" style="margin-right:2px;"></i> {{ $quiz->created_at->format('d M Y') }}</span>
                <span><i class="fa-regular fa-clock" style="margin-right:2px;"></i> {{ $quiz->time_limit }}m</span>
                <span>Passing: {{ $quiz->passing_score }}%</span>
            </div>
            <div class="quiz-card-footer">
                <div class="quiz-participants">
                    <i class="fa-solid fa-users" style="font-size:11px;"></i>
                    {{ $total }}
                </div>
                <span class="{{ $statusClass }}">{{ $statusLabel }}</span>
            </div>
        </div>
    </div>
    @empty
    <div style="grid-column: 1 / -1; display:flex; flex-direction:column; align-items:center; justify-content:center; padding: 100px 20px; background: var(--bg-card); border: 2px dashed var(--border); border-radius: 24px; transition: all 0.3s; cursor: pointer;" onmouseover="this.style.borderColor='var(--purple)'; this.style.transform='scale(1.01)'" onmouseout="this.style.borderColor='var(--border)'; this.style.transform='scale(1)'" onclick="window.location.href='{{ route('admin.quizzes.create') }}'">
        <div style="width: 120px; height: 120px; border-radius: 50%; background: linear-gradient(135deg, rgba(124,58,237,0.1), rgba(79,70,229,0.1)); display: flex; align-items: center; justify-content: center; margin-bottom: 24px; position: relative;">
            <div style="position: absolute; width: 100%; height: 100%; border-radius: 50%; background: var(--purple); filter: blur(40px); opacity: 0.2; animation: pulse 2s infinite;"></div>
            <i class="fa-solid fa-clipboard-list" style="font-size: 52px; color: var(--purple); z-index: 1;"></i>
        </div>
        <h3 style="font-size: 20px; font-weight: 800; color: var(--text-primary); margin: 0 0 8px;">Katalog Kuis Masih Kosong</h3>
        <p style="font-size: 14px; color: var(--text-muted); font-weight: 500; text-align: center; max-width: 320px; margin: 0 0 28px;">
            Buat kuis pertama Anda sekarang. Anda bisa menggunakan AI untuk membuat soal secara otomatis atau membuatnya secara manual.
        </p>
        <button class="btn btn-primary" style="padding: 12px 28px; border-radius: 12px; font-size: 14px;" onclick="event.stopPropagation(); window.location.href='{{ route('admin.quizzes.create') }}'">
            <i class="fa-solid fa-wand-magic-sparkles"></i> Mulai Buat Kuis
        </button>
    </div>
    @endforelse
</div>
@endsection

@section('scripts')
<script>
// Search Integration
window.addEventListener('pahamaja-search', function (e) {
    const q = e.detail.toLowerCase().trim();
    document.querySelectorAll('#quizGrid .quiz-card').forEach(card => {
        const name = card.dataset.name || '';
        card.style.display = (!q || name.includes(q)) ? '' : 'none';
    });
});
</script>
@endsection
