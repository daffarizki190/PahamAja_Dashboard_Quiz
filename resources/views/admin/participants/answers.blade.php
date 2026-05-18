@extends('layouts.app')

@section('title', 'Detail Hasil – ' . $participant->name)

@section('head_extra')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
    :root {
        --purple: #7C3AED;
        --bg: #F8FAFC;
        --white: #FFFFFF;
        --border: #E2E8F0;
        --text: #1E1B4B;
        --muted: #64748B;
    }

    .result-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 20px;
    }

    /* SCORE HERO SECTION */
    .score-hero {
        background: #fff;
        border-radius: 32px;
        padding: 48px 32px;
        text-align: center;
        border: 1px solid var(--border);
        box-shadow: 0 20px 50px rgba(0,0,0,0.04);
        margin-bottom: 24px;
        position: relative;
        overflow: hidden;
    }
    
    .score-hero::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; height: 6px;
        background: linear-gradient(90deg, #7C3AED, #4F46E5);
    }

    .score-ring-wrap {
        position: relative;
        width: 180px; height: 180px;
        margin: 0 auto 24px;
    }
    
    .score-ring-svg { transform: rotate(-90deg); }
    
    .score-display {
        position: absolute;
        inset: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    
    .score-val { font-size: 52px; font-weight: 900; color: var(--text); line-height: 1; }
    .score-lbl { font-size: 13px; font-weight: 800; color: var(--muted); margin-top: 4px; letter-spacing: 0.1em; }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 24px;
        border-radius: 99px;
        font-size: 14px;
        font-weight: 800;
        margin-bottom: 24px;
    }
    .status-pass { background: rgba(16,185,129,0.1); color: #059669; border: 1px solid rgba(16,185,129,0.2); }
    .status-fail { background: rgba(239,68,68,0.1); color: #DC2626; border: 1px solid rgba(239,68,68,0.2); }

    /* STATS GRID */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 32px;
    }
    
    .stat-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 20px;
        padding: 20px;
        text-align: center;
        transition: transform 0.2s;
    }
    .stat-card:hover { transform: translateY(-4px); }
    .stat-val { font-size: 24px; font-weight: 900; color: var(--text); }
    .stat-lbl { font-size: 11px; font-weight: 800; color: var(--muted); text-transform: uppercase; margin-top: 4px; }

    /* REVIEW SECTION */
    .review-card {
        background: #fff;
        border-radius: 24px;
        border: 1px solid var(--border);
        overflow: hidden;
        margin-bottom: 24px;
    }
    
    .review-header {
        padding: 20px 24px;
        border-bottom: 1px solid var(--border);
        background: #F8FAFC;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .q-item {
        padding: 24px;
        border-bottom: 1px solid #F1F5F9;
    }
    .q-item:last-child { border-bottom: none; }
    
    .q-badge {
        width: 32px; height: 32px; border-radius: 10px;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 14px; font-weight: 800; margin-bottom: 12px;
    }
    .q-correct { background: rgba(16,185,129,0.1); color: #059669; }
    .q-wrong   { background: rgba(239,68,68,0.1); color: #DC2626; }

    .ans-box {
        padding: 16px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 600;
        margin-top: 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .ans-selected { background: #F8FAFC; border: 1px solid var(--border); }
    .ans-correct  { background: rgba(16,185,129,0.05); border: 1px solid rgba(16,185,129,0.1); color: #059669; }
    
    /* AUDIT LOG */
    .audit-log {
        background: #1E1B4B;
        border-radius: 24px;
        padding: 24px;
        color: #fff;
    }
</style>
@endsection

@section('content')
<div class="result-container">
    <div style="margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between;">
        <a href="{{ route('admin.quizzes.show', $quiz->slug) }}" class="btn btn-ghost" style="color: var(--muted);">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard
        </a>
        <div style="font-size: 13px; font-weight: 800; color: var(--muted); text-transform: uppercase;">
            Detail Pengerjaan Peserta
        </div>
    </div>

    @php
        $score = $participant->score ?? 0;
        $passing = $quiz->passing_score ?? 70;
        $isPassed = $score >= $passing;
        $strokeCirc = 2 * M_PI * 75;
        $strokeOffset = $strokeCirc - (($score/100) * $strokeCirc);
        $totalQuestions = $quiz->questions->count();
        $correctCount = $rows->where('is_correct', true)->count();
    @endphp

    <!-- SCORE HERO -->
    <div class="score-hero">
        <div class="score-ring-wrap">
            <svg class="score-ring-svg" width="180" height="180" viewBox="0 0 180 180">
                <circle cx="90" cy="90" r="75" fill="none" stroke="#F1F5F9" stroke-width="12"/>
                <circle cx="90" cy="90" r="75" fill="none" stroke="{{ $isPassed ? '#10B981' : '#EF4444' }}" stroke-width="12"
                        stroke-linecap="round" stroke-dasharray="{{ $strokeCirc }}"
                        stroke-dashoffset="{{ $strokeOffset }}" style="transition: stroke-dashoffset 1s ease;"/>
            </svg>
            <div class="score-display">
                <div class="score-val">{{ $score }}</div>
                <div class="score-lbl">SCORE</div>
            </div>
        </div>

        <div class="status-badge {{ $isPassed ? 'status-pass' : 'status-fail' }}">
            <i class="fa-solid {{ $isPassed ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
            {{ $isPassed ? 'Dinyatakan Lulus' : 'Belum Lulus' }}
        </div>

        <h2 style="font-size: 28px; font-weight: 900; color: var(--text); margin-bottom: 8px;">{{ $participant->name }}</h2>
        <p style="font-size: 15px; color: var(--muted); font-weight: 600;">NIK: {{ $participant->nim }} · {{ $quiz->title }}</p>
    </div>

    <!-- STATS GRID -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-val" style="color: #10B981;">{{ $correctCount }}</div>
            <div class="stat-lbl">Benar</div>
        </div>
        <div class="stat-card">
            <div class="stat-val" style="color: #EF4444;">{{ $totalQuestions - $correctCount }}</div>
            <div class="stat-lbl">Salah</div>
        </div>
        <div class="stat-card">
            <div class="stat-val">{{ $totalQuestions }}</div>
            <div class="stat-lbl">Total Soal</div>
        </div>
        <div class="stat-card">
            <div class="stat-val" style="color: #F59E0B;">{{ $participant->duration ?? '--:--' }}</div>
            <div class="stat-lbl">Durasi</div>
        </div>
    </div>

    <!-- REVIEW SECTION -->
    <div class="review-card">
        <div class="review-header">
            <i class="fa-solid fa-list-check" style="color: var(--purple);"></i>
            <h3 style="font-size: 16px; font-weight: 900; margin: 0;">Review Jawaban</h3>
        </div>

        @foreach($rows as $idx => $r)
        <div class="q-item">
            <div class="q-badge {{ $r['is_correct'] ? 'q-correct' : 'q-wrong' }}">
                {{ $idx + 1 }}
            </div>
            <div style="font-size: 16px; font-weight: 700; color: var(--text); line-height: 1.5; margin-bottom: 16px;">
                {!! $r['question'] !!}
            </div>

            <div class="ans-box ans-selected">
                <div>
                    <span style="font-size: 11px; font-weight: 800; color: var(--muted); text-transform: uppercase; display: block; margin-bottom: 4px;">Jawaban Peserta</span>
                    {{ $r['selected'] ?: 'Tidak Dijawab' }}
                </div>
                <i class="fa-solid {{ $r['is_correct'] ? 'fa-check-circle' : 'fa-times-circle' }}" style="color: {{ $r['is_correct'] ? '#10B981' : '#EF4444' }}; font-size: 18px;"></i>
            </div>

            @if(!$r['is_correct'])
            <div class="ans-box ans-correct" style="margin-top: 8px;">
                <div>
                    <span style="font-size: 11px; font-weight: 800; color: #059669; text-transform: uppercase; display: block; margin-bottom: 4px;">Jawaban Benar</span>
                    {{ $r['correct'] }}
                </div>
                <i class="fa-solid fa-shield-check" style="font-size: 18px;"></i>
            </div>
            @endif

            @if($r['explanation'])
            <div style="margin-top: 12px; padding: 12px 16px; background: rgba(124,58,237,0.04); border-left: 4px solid var(--purple); border-radius: 4px; font-size: 13px; color: #4B5563; font-style: italic;">
                "{{ $r['explanation'] }}"
            </div>
            @endif
        </div>
        @endforeach
    </div>

    <!-- AUDIT TRAIL -->
    <div class="audit-log">
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px;">
            <i class="fa-solid fa-shield-halved" style="font-size: 20px; color: #A78BFA;"></i>
            <h3 style="font-size: 16px; font-weight: 900; margin: 0;">Integrity Audit Trail (TDD)</h3>
        </div>
        
        <div style="display: flex; flex-direction: column; gap: 10px;">
            @foreach($logs as $log)
            <div style="background: rgba(255,255,255,0.05); padding: 12px 16px; border-radius: 12px; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <span style="font-size: 12px; font-weight: 800; color: #A78BFA; text-transform: uppercase;">{{ str_replace('_', ' ', $log->event_type) }}</span>
                    <p style="font-size: 11px; color: #94A3B8; margin: 4px 0 0 0;">{{ $log->created_at->format('H:i:s') }} · IP: {{ $log->ip_address }}</p>
                </div>
                @if($log->event_type === 'TAB_BLUR')
                <span style="background: #EF4444; color: #fff; padding: 4px 10px; border-radius: 6px; font-size: 10px; font-weight: 900;">PELANGGARAN</span>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
