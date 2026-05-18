<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Hasil kuis {{ $quiz->title }} – PahamAja">
    <title>Hasil Kuis – PahamAja</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --purple: #7C3AED;
            --bg: #EEECf5;
            --white: #FFFFFF;
            --border: #E8E6F0;
            --text: #1E1B4B;
            --muted: #6B7280;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #F0EFFF 0%, #EAEAFF 100%);
            color: var(--text);
            min-height: 100vh;
            display: flex; flex-direction: column;
        }

        /* HEADER */
        .result-header {
            background: #fff;
            border-bottom: 1px solid var(--border);
            padding: 0 28px;
            height: 64px;
            display: flex; align-items: center; gap: 12px;
            position: sticky; top: 0; z-index: 50;
            box-shadow: 0 1px 12px rgba(0,0,0,0.03);
        }
        .logo-icon {
            width: 36px; height: 36px; border-radius: 10px;
            background: linear-gradient(135deg, #7C3AED, #4F46E5);
            display: flex; align-items: center; justify-content: center;
            font-size: 16px; font-weight: 900; font-style: italic; color: #fff;
            box-shadow: 0 4px 10px rgba(124,58,237,0.25);
        }
        .logo-name { font-size: 16px; font-weight: 800; color: var(--text); }

        /* MAIN */
        .page-wrap {
            flex: 1; padding: 40px 24px;
            max-width: 820px; margin: 0 auto; width: 100%;
        }

        /* SCORE HERO */
        .score-hero {
            text-align: center; padding: 40px 24px 36px;
            background: #fff; border: 1px solid var(--border);
            border-radius: 28px; margin-bottom: 20px;
            box-shadow: 0 12px 36px rgba(0,0,0,0.03);
        }
        .score-ring-wrap {
            position: relative; margin: 0 auto 28px; width: 160px; height: 160px;
        }
        .score-ring { transform: rotate(-90deg); }
        .score-text {
            position: absolute; inset: 0; display: flex; flex-direction: column;
            align-items: center; justify-content: center;
        }
        .score-number { font-size: 44px; font-weight: 900; line-height: 1; }
        .score-label  { font-size: 13px; font-weight: 800; color: var(--muted); margin-top: 4px; }

        .result-badge {
            display: inline-flex; align-items: center; gap: 8px;
            border-radius: 12px; padding: 10px 20px; font-size: 15px; font-weight: 800;
            margin-bottom: 24px;
        }
        .badge-lulus { background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.2); color: #059669; }
        .badge-gagal { background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.2); color: #DC2626; }

        .result-name { font-size: 24px; font-weight: 900; color: var(--text); margin-bottom: 6px; }
        .result-quiz { font-size: 14px; color: var(--muted); font-weight: 700; text-transform: uppercase; letter-spacing: .05em;}

        /* STATS ROW */
        .stats-row { display: grid; grid-template-columns: repeat(3,1fr); gap: 16px; margin-bottom: 24px; }
        .stat-c {
            background: #fff; border: 1px solid var(--border);
            border-radius: 20px; padding: 24px 20px; text-align: center;
            box-shadow: 0 8px 24px rgba(0,0,0,0.02);
        }
        .stat-c-val { font-size: 32px; font-weight: 900; color: var(--text); line-height:1.2; }
        .stat-c-lbl { font-size: 11px; color: var(--muted); font-weight: 800; text-transform: uppercase; letter-spacing: .12em; margin-top: 4px; }

        /* REVIEW */
        .review-section { background: #fff; border: 1px solid var(--border); border-radius: 24px; overflow: hidden; box-shadow: 0 12px 36px rgba(0,0,0,0.02); }
        .review-header  { padding: 20px 24px; border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 14px; background:#FAFAFC;}
        .q-item { padding: 24px; border-bottom: 1px solid #F3F2F9; }
        .q-item:last-child { border-bottom: none; }
        .q-num-badge {
            display: inline-flex; align-items: center; justify-content: center;
            width: 28px; height: 28px; border-radius: 8px; font-size: 12px; font-weight: 900; flex-shrink: 0;
        }
        .q-correct-badge { background: rgba(16,185,129,0.12); color: #059669; box-shadow:0 0 0 2px rgba(16,185,129,0.1) }
        .q-wrong-badge   { background: rgba(239,68,68,0.1); color: #DC2626; box-shadow:0 0 0 2px rgba(239,68,68,0.1)}
        .opt-row {
            display: flex; align-items: center; gap: 12px; padding: 12px 16px;
            border-radius: 12px; font-size: 14px; font-weight: 600; margin-top: 10px;
        }
        .opt-correct { background: rgba(16,185,129,0.08); border: 1px solid rgba(16,185,129,0.2); color: #059669; }
        .opt-wrong   { background: rgba(239,68,68,0.06); border: 1px solid rgba(239,68,68,0.15); color: #DC2626; }

        /* ACTION */
        .action-row { display: flex; gap: 12px; justify-content: center; margin-top: 32px; flex-wrap: wrap; }
        .btn-action {
            display: flex; align-items: center; gap: 10px; padding: 16px 32px; border-radius: 16px;
            font-size: 15px; font-weight: 800; cursor: pointer; border: none;
            font-family: inherit; text-decoration: none; transition: all .2s;
        }
        .btn-primary-a {
            background: linear-gradient(135deg, #7C3AED, #4F46E5); color: #fff;
            box-shadow: 0 8px 24px rgba(124,58,237,0.3);
        }
        .btn-primary-a:hover { box-shadow: 0 12px 32px rgba(124,58,237,0.4); transform: translateY(-3px); }
        .btn-ghost-a { background: #fff; border: 1px solid var(--border); color: var(--text); box-shadow:0 4px 12px rgba(0,0,0,0.02); }
        .btn-ghost-a:hover { border-color: #7C3AED; color: #7C3AED; }

        @keyframes countUp { from{opacity:0;transform:scale(.85);} to{opacity:1;transform:scale(1);} }
        .count-up { animation:countUp .6s cubic-bezier(.16,1,.3,1) .3s backwards; }

        @media (max-width:640px) {
            .stats-row { grid-template-columns: 1fr 1fr; }
            .score-number { font-size: 36px; }
        }

        /* 3D Enhancements */
        .card-3d { transform-style: preserve-3d; perspective: 1000px; }
        .img-3d { border-radius: 20px; box-shadow: 0 10px 30px rgba(30,27,75,0.1); transform-style: preserve-3d; transition: transform 0.3s; }
        .img-3d:hover { transform: translateZ(20px); }
    </style>
</head>
<body>

<!-- Header -->
<header class="result-header">
    <div class="logo-icon">P</div>
    <div class="logo-name">Paham<span style="color:#7C3AED">Aja</span></div>
    <span style="margin-left:auto; font-size:11px; color:#9CA3AF; font-weight:800; text-transform:uppercase; letter-spacing:.12em;">Hasil Assessment</span>
</header>

@php
    $score   = $participant->score ?? 0;
    $passing = $quiz->passing_score ?? 70;
    $passed  = $score >= $passing;
    $total   = $quiz->questions_count ?? $quiz->questions->count();
    
    // Safely approximate correct based on score:
    $correct = round(($score / 100) * $total);
    $wrong   = $total - $correct;
    
    $percent = min(100, max(0, $score));
    $strokeCirc   = 2 * M_PI * 65;
    $strokeOffset = $strokeCirc - ($percent / 100 * $strokeCirc);
    $ringColor    = $passed ? '#10B981' : '#EF4444';
    $ringTrack    = $passed ? 'rgba(16,185,129,0.12)' : 'rgba(239,68,68,0.1)';

    $duration = $participant->duration;
@endphp

<div class="page-wrap">
    <!-- Score Hero -->
    <div class="score-hero card-3d" data-tilt data-tilt-max="5">
        <!-- Participant Avatar -->
        <div style="margin-bottom: 24px;">
            <div class="img-3d" style="width:80px; height:80px; margin:0 auto; overflow:hidden; border:3px solid #fff; background:linear-gradient(135deg,#7C3AED,#4F46E5); display:flex; align-items:center; justify-content:center; color:#fff; font-size:32px; font-weight:900;">
                @if($participant->employee)
                    <img src="{{ avatar_url($participant->employee->avatar, $participant->employee->name) }}" style="width:100%; height:100%; object-fit:cover;">
                @else
                    <i class="fa-solid fa-user"></i>
                @endif
            </div>
        </div>
        <!-- Ring -->
        <div class="score-ring-wrap">
            <svg class="score-ring" width="160" height="160" viewBox="0 0 160 160">
                <circle cx="80" cy="80" r="65" fill="none" stroke="{{ $ringTrack }}" stroke-width="12"/>
                <circle cx="80" cy="80" r="65" fill="none" stroke="{{ $ringColor }}" stroke-width="12"
                        stroke-linecap="round" stroke-dasharray="{{ $strokeCirc }}"
                        stroke-dashoffset="{{ $strokeOffset }}" style="transition:stroke-dashoffset 1.5s ease;"/>
            </svg>
            <div class="score-text">
                <div class="score-number count-up" style="color:{{ $ringColor }};">{{ $score }}</div>
                <div class="score-label">SCORE</div>
            </div>
        </div>

        @if($participant->status === 'pending_review')
        <div class="result-badge" style="background:rgba(245,158,11,0.1); border:1px solid rgba(245,158,11,0.2); color:#D97706;">
            <i class="fa-solid fa-clock"></i> Menunggu Penilaian Manual
        </div>
        <p style="font-size:14px; color:var(--muted); line-height:1.6; margin-bottom:24px; max-width:400px; margin-left:auto; margin-right:auto;">
            Jawaban esai Anda sedang dalam proses peninjauan oleh Admin. Skor akhir akan muncul setelah proses selesai.
        </p>
        @else
        <div class="result-badge {{ $passed ? 'badge-lulus' : 'badge-gagal' }}">
            <i class="fa-solid {{ $passed ? 'fa-trophy' : 'fa-rotate-right' }}"></i>
            {{ $passed ? 'Selamat! Anda Lulus 🎉' : 'Belum Lulus – Terus Semangat 💪' }}
        </div>
        @endif

        <div class="result-name">{{ $participant->name }}</div>
        <div class="result-quiz">{{ $quiz->title }}</div>
    </div>

    <!-- Stats -->
    <div class="stats-row">
        <div class="stat-c">
            <div class="stat-c-val" style="color:#10B981;">{{ $correct }}</div>
            <div class="stat-c-lbl">Benar</div>
        </div>
        <div class="stat-c">
            <div class="stat-c-val" style="color:#EF4444;">{{ $wrong }}</div>
            <div class="stat-c-lbl">Salah</div>
        </div>
        <div class="stat-c">
            <div class="stat-c-val" style="color:#7C3AED;">{{ $total }}</div>
            <div class="stat-c-lbl">Total Soal</div>
        </div>
        <div class="stat-c" style="grid-column: 1 / -1; display:flex; justify-content:space-between; align-items:center; padding:16px 24px;">
            <div style="text-align:left;">
                <div style="font-size:11px; font-weight:800; color:var(--muted); text-transform:uppercase; letter-spacing:.05em;">Waktu Pengerjaan</div>
                <div style="font-size:14px; font-weight:700; color:var(--text);">{{ \Carbon\Carbon::parse($participant->finished_at ?? $participant->started_at)->format('d M Y, H:i') }}</div>
            </div>
            @if($duration)
            <div style="text-align:right;">
                <div style="font-size:11px; font-weight:800; color:var(--muted); text-transform:uppercase; letter-spacing:.05em;">Durasi</div>
                <div style="font-size:14px; font-weight:700; color:#D97706;">{{ $duration }}</div>
            </div>
            @endif
        </div>
    </div>

    <!-- Answer Review (Only if Review Data exists, e.g., when passed) -->
    @if(isset($reviewData) && $reviewData && count($reviewData) > 0)
    <div class="review-section" style="margin-top:32px;">
        <div class="review-header">
            <div style="width:42px; height:42px; border-radius:12px; background:linear-gradient(135deg,#7C3AED,#4F46E5); display:flex; align-items:center; justify-content:center; color:#fff; font-size:16px; flex-shrink:0;">
                <i class="fa-solid fa-list-check"></i>
            </div>
            <div>
                <div style="font-size:16px; font-weight:900; color:#1E1B4B;">Review Jawaban</div>
                <div style="font-size:12px; font-weight:700; color:#6B7280; margin-top:2px;">Lihat rekap jawaban Anda</div>
            </div>
        </div>

        @foreach($reviewData as $idx => $r)
             <div style="display:flex; align-items:flex-start; gap:14px; margin-bottom:14px;">
                <span class="q-num-badge {{ $r['is_correct'] ? 'q-correct-badge' : 'q-wrong-badge' }}">
                    <i class="fa-solid {{ $r['is_correct'] ? 'fa-check' : 'fa-xmark' }}"></i>
                </span>
                <div style="flex:1;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
                        <div style="font-size:11px; font-weight:800; color:#7C3AED; text-transform:uppercase; letter-spacing:.1em;">Soal {{ $idx + 1 }}</div>
                        <span style="font-size:9px; font-weight:800; background:rgba(124,58,237,0.1); color:#7C3AED; padding:2px 6px; border-radius:4px;">{{ strtoupper($r['type'] ?? 'mcq') }}</span>
                    </div>
                    <div style="font-size:15px; font-weight:700; color:#1E1B4B; line-height:1.5;">{!! $r['question'] !!}</div>
                </div>
            </div>

            @if(($r['type'] ?? 'mcq') === 'mcq')
                <div class="opt-row {{ $r['is_correct'] ? 'opt-correct' : 'opt-wrong' }}">
                    <span style="font-size:11px; font-weight:800; opacity:.7;">Jawaban Anda</span>
                    <span>{{ $r['selected'] }}</span>
                    <i class="fa-solid {{ $r['is_correct'] ? 'fa-check-circle' : 'fa-times-circle' }}" style="margin-left:auto;"></i>
                </div>

                @if(!$r['is_correct'])
                <div class="opt-row opt-correct" style="margin-top:8px;">
                    <span style="font-size:11px; font-weight:800; opacity:.7;">Jawaban Benar</span>
                    <span>{{ $r['correct'] }}</span>
                    <i class="fa-solid fa-check-circle" style="margin-left:auto;"></i>
                </div>
                @endif
            @else
                <div style="margin-top:10px; padding:16px; background:#F9FAFB; border:1px solid var(--border); border-radius:14px;">
                    <div style="font-size:11px; font-weight:800; color:var(--muted); text-transform:uppercase; margin-bottom:8px;">Jawaban Anda</div>
                    <div style="font-size:14px; font-weight:600; color:var(--text); line-height:1.6;">{{ $r['selected'] }}</div>
                </div>
                
                <div style="margin-top:10px; padding:16px; background:rgba(16,185,129,0.03); border:1px solid rgba(16,185,129,0.1); border-radius:14px;">
                    <div style="font-size:11px; font-weight:800; color:#059669; text-transform:uppercase; margin-bottom:8px;">Hasil Penilaian @if($quiz->essay_grading_method === 'ai')(AI Gemini)@endif</div>
                    <div style="display:flex; align-items:center; gap:8px; margin-bottom:10px;">
                        <div style="font-size:24px; font-weight:900; color:#059669;">{{ $r['score'] ?? 0 }}</div>
                        <div style="font-size:12px; font-weight:700; color:var(--muted);">/ 5 Poin</div>
                    </div>
                    <div style="font-size:13px; font-weight:600; color:#4B5563; line-height:1.6; font-style:italic;">
                        "{{ $r['explanation'] ?: 'Tidak ada umpan balik.' }}"
                    </div>
                </div>
            @endif

            @if($r['explanation'] && ($r['type'] ?? 'mcq') === 'mcq')
            <div style="margin-top:12px; padding:12px 16px; background:rgba(124,58,237,0.04); border-left:4px solid #7C3AED; border-radius:0 12px 12px 0; font-size:13px; color:#4E4C6A; font-weight:600; line-height:1.6;">
                <i class="fa-solid fa-lightbulb" style="margin-right:8px; color:#7C3AED;"></i>{{ $r['explanation'] }}
            </div>
            @endif
        @endforeach
    </div>
    @endif

    <div class="action-row" style="margin-bottom:30px;">
        <a href="{{ route('quiz.join', $quiz->slug) }}" class="btn-action btn-primary-a" id="finishBtn" style="opacity:0; transform:translateY(10px); transition:all 0.6s ease;">
            <i class="fa-solid fa-rotate-right"></i> Selesai / Coba Lagi
        </a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const scoreVal = {{ $score }};
    const isPassed = {{ $passed ? 'true' : 'false' }};
    const scoreEl  = document.querySelector('.score-number');
    
    // 1. Counter Animation
    let current = 0;
    const duration = 1500;
    const startTime = performance.now();

    function animate(time) {
        const elapsed = time - startTime;
        const progress = Math.min(elapsed / duration, 1);
        const easeOut = 1 - Math.pow(1 - progress, 3);
        
        const currentScore = Math.floor(easeOut * scoreVal);
        scoreEl.textContent = currentScore;

        if (progress < 1) {
            requestAnimationFrame(animate);
        } else {
            // 2. Confetti if passed
            if (isPassed) {
                const duration = 3 * 1000;
                const end = Date.now() + duration;

                (function frame() {
                    confetti({ particleCount: 3, angle: 60, spread: 55, origin: { x: 0 }, colors: ['#7C3AED', '#4F46E5'] });
                    confetti({ particleCount: 3, angle: 120, spread: 55, origin: { x: 1 }, colors: ['#10B981', '#4F46E5'] });
                    if (Date.now() < end) { requestAnimationFrame(frame); }
                }());
            }
            // Show finish btn
            const btn = document.getElementById('finishBtn');
            btn.style.opacity = '1';
            btn.style.transform = 'translateY(0)';
        }
    }
    requestAnimationFrame(animate);
});
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/vanilla-tilt/1.8.1/vanilla-tilt.min.js"></script>
<script>
    VanillaTilt.init(document.querySelectorAll("[data-tilt]"), { max: 15, speed: 400, glare: true, "max-glare": 0.1 });
</script>
</body>
</html>
