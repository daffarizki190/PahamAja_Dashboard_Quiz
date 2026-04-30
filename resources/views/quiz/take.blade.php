<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $quiz->title }} – Ikuti kuis sekarang">
    <title>{{ $quiz->title }} – PahamAja</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        :root {
            --purple: #7C3AED;
            --purple-light: #A78BFA;
            --bg: #EEEDF5;
            --white: #FFFFFF;
            --border: #E5E3F0;
            --text: #1E1B4B;
            --muted: #6B7280;
            --dev-hint: rgba(124, 58, 237, 0.4);
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
        }

        /* ── HEADER ── */
        .quiz-header {
            background: var(--white);
            border-bottom: 1px solid var(--border);
            padding: 0 32px;
            height: 60px;
            display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 50;
            box-shadow: 0 1px 4px rgba(0,0,0,0.06);
        }
        .quiz-logo { display: flex; align-items: center; gap: 10px; }
        .quiz-logo-icon {
            width: 34px; height: 34px; border-radius: 9px;
            background: linear-gradient(135deg, #7C3AED, #4F46E5);
            display: flex; align-items: center; justify-content: center;
            font-size: 15px; font-weight: 900; font-style: italic; color: #fff;
        }
        .quiz-logo-name { font-size: 15px; font-weight: 800; color: var(--text); }

        .quiz-title-center {
            position: absolute; left: 50%; transform: translateX(-50%);
            font-size: 15px; font-weight: 800; color: var(--text);
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 400px;
        }

        .timer-pill {
            display: flex; align-items: center; gap: 8px;
            background: #F3F4F6; color: #374151;
            border-radius: 12px; padding: 10px 18px;
            font-size: 18px; font-weight: 800; font-variant-numeric: tabular-nums;
            border: 2px solid var(--border);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .timer-pill i { font-size: 14px; margin-right: 2px; opacity: 0.7; }
        .timer-pill.warning { background: #FFFBEB; color: #B45309; border-color: #FCD34D; }
        .timer-pill.danger { background: #FEF2F2; color: #B91C1C; border-color: #FCA5A5; animation: timerPulse 1.5s ease-in-out infinite; }
        @keyframes timerPulse { 0%, 100% { opacity: 1; transform: scale(1); } 50% { opacity: 0.8; transform: scale(1.02); } }

        /* ── PROGRESS ── */
        .progress-bar-wrap {
            background: var(--white);
            border-bottom: 1px solid var(--border);
            padding: 10px 32px;
        }
        .progress-label { font-size: 12px; font-weight: 600; color: var(--muted); margin-bottom: 6px; }
        .progress-track { height: 6px; background: #E5E3F0; border-radius: 99px; overflow: hidden; }
        .progress-fill  { height: 100%; border-radius: 99px; background: linear-gradient(90deg,#7C3AED,#4F46E5); transition: width 0.4s cubic-bezier(0.4, 0, 0.2, 1); box-shadow: 0 0 10px rgba(124,58,237,0.3); }

        /* ── MAIN LAYOUT ── */
        .quiz-body { padding: 32px; max-width: 800px; margin: 0 auto; }

        /* ── ILLUSTRATION ── */
        .question-illustration {
            display: flex; justify-content: center;
            margin-bottom: -20px; position: relative; z-index: 1;
        }
        .question-illustration img { width: 100px; height: 100px; }

        /* ── QUESTION CARD ── */
        .q-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 36px 40px 32px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        }
        .q-text {
            font-size: 19px; font-weight: 800; color: var(--text);
            line-height: 1.55; text-align: center; margin-bottom: 32px;
        }

        /* ── OPTIONS 2x2 GRID ── */
        .options-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }
        .option-item {
            display: flex; align-items: flex-start; gap: 12px;
            padding: 14px 18px;
            border: 1.5px solid var(--border); border-radius: 12px;
            cursor: pointer; transition: all 0.18s;
            background: var(--white);
        }
        .option-item:hover { border-color: rgba(124,58,237,0.3); background: rgba(124,58,237,0.04); }
        .option-item.selected {
            border-color: #7C3AED;
            background: linear-gradient(135deg, #7C3AED, #4F46E5);
        }
        .option-item input[type=radio] { display: none; }
        .option-bubble {
            width: 30px; height: 30px; border-radius: 8px; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
            font-size: 12px; font-weight: 800;
            background: #F3F2FB; color: var(--muted); border: 1.5px solid var(--border);
            transition: all 0.18s;
        }
        .option-item.selected .option-bubble {
            background: rgba(255,255,255,0.25); color: #fff; border-color: transparent;
        }
        .option-label { font-size: 14px; font-weight: 600; color: var(--text); line-height: 1.4; padding-top: 4px; flex: 1; }
        .option-item.selected .option-label { color: #fff; }
        
        /* ── DEV HINT (End of Text Dot) ── */
        .option-item.dev-cheat-active[data-correct="1"] .option-label::after {
            content: ''; 
            display: inline-block;
            width: 2px; 
            height: 2px;
            background: rgba(30, 27, 75, 0.2); 
            border-radius: 50%;
            margin-left: 4px;
            vertical-align: middle;
            pointer-events: none;
        }

        /* ── ESSAY STYLES ── */
        .essay-textarea {
            width: 100%; border-radius: 16px; border: 2px solid var(--border);
            padding: 20px; font-family: inherit; font-size: 15px; font-weight: 600;
            color: var(--text); background: #F9FAFB; transition: all 0.2s;
            resize: vertical; min-height: 160px; outline: none;
        }
        .essay-textarea:focus { border-color: var(--purple); background: #fff; box-shadow: 0 0 0 4px rgba(124,58,237,0.1); }
        .essay-status { font-size: 11px; font-weight: 700; color: var(--purple); margin-top: 8px; display: none; }

        /* ── NAVIGATION ── */
        .nav-buttons { display: flex; justify-content: center; gap: 14px; margin-top: 28px; }
        .btn-nav {
            display: flex; align-items: center; gap: 8px; padding: 12px 28px;
            border-radius: 12px; font-size: 14px; font-weight: 700; cursor: pointer;
            transition: all 0.18s; font-family: inherit;
        }
        .btn-prev {
            background: var(--white); color: var(--text);
            border: 1.5px solid var(--border);
        }
        .btn-prev:hover { border-color: #7C3AED; color: #7C3AED; }
        .btn-next {
            background: linear-gradient(135deg, #7C3AED, #4F46E5);
            color: #fff; border: none;
            box-shadow: 0 4px 14px rgba(124,58,237,0.35);
        }
        .btn-next:hover { box-shadow: 0 6px 20px rgba(124,58,237,0.5); transform: translateY(-1px); }
        .btn-submit {
            background: linear-gradient(135deg, #7C3AED, #4F46E5);
            color: #fff; border: none;
            box-shadow: 0 4px 14px rgba(124,58,237,0.35);
        }
        .btn-submit:hover { box-shadow: 0 6px 20px rgba(124,58,237,0.5); transform: translateY(-1px); }

        /* ── SUBMIT MODAL ── */
        .modal-bg { display:none; position:fixed; inset:0; z-index:9999; background:rgba(30,27,75,0.5); backdrop-filter:blur(6px); align-items:center; justify-content:center; }
        .modal-bg.open { display:flex; }
        .modal-box { background:#fff; border:1px solid #E5E3F0; border-radius:20px; padding:32px; max-width:420px; width:90%; animation:fadeUp .3s ease; box-shadow:0 20px 60px rgba(30,27,75,0.15); }
        @keyframes fadeUp { from{opacity:0;transform:translateY(14px);} to{opacity:1;transform:translateY(0);} }

        @media (max-width: 640px) {
            .quiz-body { padding: 20px 16px; }
            .q-card { padding: 24px 20px; }
            .q-text { font-size: 16px; }
            .options-grid { grid-template-columns: 1fr; }
            .quiz-title-center { position: static; transform: none; display: flex; align-items: center; }
            .test-title { display: none; }
            .quiz-logo-name { display: none; }
            .test-user { font-size: 11px !important; margin-top: 0 !important; }
            .nav-buttons { flex-direction: column; gap: 12px; }
            .btn-nav { width: 100%; justify-content: center; padding: 14px 20px; font-size: 15px; }
            .modal-box { padding: 24px; width: 95%; }
        }
        
        /* Modal Custom */
        .pa-modal-overlay { position: fixed; inset: 0; background: rgba(30,27,75,0.6); backdrop-filter: blur(8px); display: none; align-items: center; justify-content: center; z-index: 999999; padding: 20px; }
        .pa-modal-overlay.open { display: flex; }
        .pa-modal {
            background: #fff; border-radius: 24px; padding: 32px; width: 100%; max-width: 440px;
            box-shadow: 0 30px 70px rgba(0,0,0,0.2); animation: modalIn 0.35s cubic-bezier(0.34, 1.56, 0.64, 1); text-align: center;
        }
        @keyframes modalIn { from { transform: scale(0.9) translateY(20px); opacity: 0; } to { transform: scale(1) translateY(0); opacity: 1; } }
        .pa-modal-icon { width: 64px; height: 64px; border-radius: 20px; display: flex; align-items: center; justify-content: center; font-size: 28px; margin: 0 auto 20px; background: rgba(245,158,11,0.12); color: #F59E0B; }
        .pa-modal-title { font-size: 20px; font-weight: 900; color: #1E1B4B; margin-bottom: 12px; }
        .pa-modal-text { font-size: 14px; color: #6B7280; line-height: 1.6; margin-bottom: 28px; }
        /* ── APPLE DOTS WAVE ── */
        @keyframes dotsWave {
            0%, 60%, 100% { transform: translateY(0) scale(1);   opacity: 0.35; }
            30%            { transform: translateY(-9px) scale(1.15); opacity: 1; }
        }
        .dots-wave { display: inline-flex; align-items: center; gap: 5px; vertical-align: middle; }
        .dots-wave span {
            display: inline-block; width: 7px; height: 7px; border-radius: 50%;
            background: currentColor; animation: dotsWave 1.2s ease-in-out infinite;
        }
        .dots-wave span:nth-child(1) { animation-delay: 0s; }
        .dots-wave span:nth-child(2) { animation-delay: 0.15s; }
        .dots-wave span:nth-child(3) { animation-delay: 0.3s; }
        .dots-wave span:nth-child(4) { animation-delay: 0.45s; }
        .dots-wave.white { color: #fff; }


    </style>
</head>
<body>

<!-- Header -->
<header class="quiz-header">
    <div class="quiz-logo">
        <div class="quiz-logo-icon">P</div>
        <div class="quiz-logo-name">Paham<span style="color:#7C3AED">Aja</span></div>
    </div>

    <div class="quiz-title-center">
        <div class="test-title" style="font-weight:800; color:#1E1B4B;">{{ \Illuminate\Support\Str::limit($quiz->title, 50) }}</div>
        <div class="test-user" style="font-size:12px; font-weight:700; color:#7C3AED; display:flex; align-items:center; justify-content:center; gap:6px; margin-top:4px;">
            <i class="fa-solid fa-user-circle"></i> {{ \Illuminate\Support\Str::limit($participant->name, 20) }}
        </div>
    </div>

    <div id="timerEl" class="timer-pill">
        <i class="fa-solid fa-clock"></i> <span id="timerDisplay">--:--</span>
    </div>
</header>

<!-- Progress Bar -->
<div class="progress-bar-wrap">
    <div class="progress-label" id="progressLabel">Soal 1 dari {{ count($quiz->questions) }}</div>
    <div class="progress-track">
        <div class="progress-fill" id="progressFill" style="width: {{ count($quiz->questions) > 0 ? round(1/count($quiz->questions)*100) : 0 }}%"></div>
    </div>
</div>

<!-- Main Quiz -->
<div class="quiz-body">
    <!-- Illustration -->
    <div class="question-illustration">
        <img src="https://fonts.gstatic.com/s/e/notoemoji/latest/1f914/emoji.svg" alt="thinking"
             onerror="this.style.fontSize='80px'; this.outerHTML='<div style=\'font-size:80px;text-align:center;\'>🤔</div>'">
    </div>

    <!-- Question Card -->
    <div class="q-card">
        <div class="q-text" id="questionText">Memuat soal...</div>

        <form id="quizForm">
            <div class="options-grid" id="optionsGrid">
                <!-- Rendered by JS -->
            </div>
        </form>

        <!-- Navigation -->
        <div class="nav-buttons">
            <button class="btn-nav btn-prev" id="btnPrev" onclick="prevQuestion()">
                Sebelumnya
            </button>
            <button class="btn-nav btn-next" id="btnNext" onclick="nextQuestion()">
                Selanjutnya <i class="fa-solid fa-chevron-right" style="font-size:12px;"></i>
            </button>
            <button class="btn-nav btn-submit" id="btnSubmit" onclick="confirmSubmit()" style="display:none;">
                <i class="fa-solid fa-paper-plane"></i> Kumpulkan
            </button>
        </div>
    </div>
</div>

<!-- Confirm Submit Modal -->
<div class="modal-bg" id="confirmModal">
    <div class="modal-box">
        <div style="text-align:center; margin-bottom:24px;">
            <div style="width:56px; height:56px; border-radius:16px; background:rgba(124,58,237,0.1); color:#7C3AED; display:flex; align-items:center; justify-content:center; font-size:24px; margin:0 auto 14px;">
                <i class="fa-solid fa-paper-plane"></i>
            </div>
            <h2 style="font-size:19px; font-weight:900; color:#1E1B4B; margin-bottom:8px;">Kumpulkan Jawaban?</h2>
            <p style="font-size:13px; color:#6B7280; line-height:1.6;" id="confirmText">
                Pastikan Anda sudah memeriksa semua jawaban.
            </p>


        </div>
        <div style="display:flex; gap:10px;">
            <button onclick="closeConfirm()" class="btn-nav btn-prev" style="flex:1; justify-content:center;">
                Cek Lagi
            </button>
            <button onclick="submitQuiz()" class="btn-nav btn-submit" style="flex:1; justify-content:center;" id="submitFinalBtn">
                <i class="fa-solid fa-check"></i> Ya, Kumpulkan
            </button>
        </div>
    </div>
</div>

<!-- Hidden submit form -->
<form id="submitForm" action="{{ route('quiz.storeAnswer', ['quiz' => $quiz->slug, 'participant' => $participant->id]) }}" method="POST" style="display:none;">
    @csrf
    <div id="hiddenAnswers"></div>

</form>

<div class="pa-modal-overlay" id="paModalOverlay">
    <div class="pa-modal">
        <div class="pa-modal-icon" id="paModalIcon"><i class="fa-solid fa-triangle-exclamation"></i></div>
        <div class="pa-modal-title" id="paModalTitle">Peringatan</div>
        <div class="pa-modal-text" id="paModalText">Konten pesan...</div>
        <button class="btn-nav btn-next" style="width:100%; justify-content:center; padding:14px;" onclick="closePahamAjaModal()">Mengerti</button>
    </div>
</div>

<script>
const QUESTIONS = @json($quiz->questions);
const AUTOSAVE_URL = "{{ route('quiz.autosave', ['quiz' => $quiz->slug, 'participant' => $participant->id]) }}";
const TIME_LIMIT   = {{ $remainingSeconds }};
const CSRF_TOKEN   = '{{ csrf_token() }}';
const P_ID         = '{{ $participant->id }}';
const LOG_URL      = "{{ route('quiz.logEvent', ['quiz' => $quiz->slug, 'participant' => $participant->id]) }}";
const LS_KEY_IDX   = `pahamaja_idx_${P_ID}`;
const LS_KEY_STRIKE= `pahamaja_strike_${P_ID}`;
const IS_DEV       = {{ $isDev ? 'true' : 'false' }};
let devModeActive  = false;

async function logEvent(type, payload = {}) {
    try {
        await fetch(LOG_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
            body: JSON.stringify({ event_type: type, payload })
        });
    } catch (e) {}
}

let currentIdx = parseInt(localStorage.getItem(LS_KEY_IDX)) || 0;
const answers  = {};
let timeLeft   = TIME_LIMIT;
let timerInterval;

// ── RENDER QUESTION ──
function renderQuestion(idx) {
    const q = QUESTIONS[idx];
    if (!q) return;

    document.getElementById('questionText').innerHTML = q.text || q.question || '';
    document.getElementById('progressLabel').textContent = `Soal ${idx + 1} dari ${QUESTIONS.length}`;
    document.getElementById('progressFill').style.width = Math.round(((idx + 1) / QUESTIONS.length) * 100) + '%';

    const saved = answers[q.id] || "";
    const grid = document.getElementById('optionsGrid');
    grid.innerHTML = '';

    if (q.type === 'essay') {
        const wrapper = document.createElement('div');
        wrapper.style.gridColumn = '1 / -1';
        wrapper.innerHTML = `
            <textarea class="essay-textarea" placeholder="Tuliskan jawaban Anda di sini..." oninput="saveEssay(${q.id}, this.value)">${saved}</textarea>
            <div id="essay-status-${q.id}" class="essay-status">
                <i class="fa-solid fa-cloud-arrow-up"></i> Menyimpan...
            </div>
        `;
        grid.appendChild(wrapper);
    } else if (q.options && q.options.length > 0) {
        const labels = ['A', 'B', 'C', 'D', 'E', 'F'];
        q.options.forEach((opt, i) => {
            const letter = labels[i] || '';
            const val = String(opt.id);
            const isSel = String(saved) === val;
            
            const labelEl = document.createElement('label');
            labelEl.className = 'option-item' + (isSel ? ' selected' : '') + (devModeActive && opt.is_correct ? ' dev-cheat-active' : '');
            labelEl.setAttribute('data-correct', opt.is_correct ? '1' : '0');
            labelEl.innerHTML = `
                <input type="radio" name="answer_${q.id}" value="${val}" ${isSel ? 'checked' : ''}>
                <div class="option-bubble">${isSel ? '<i class="fa-solid fa-check" style="font-size:11px;"></i>' : letter}</div>
                <div class="option-label">${opt.text}</div>
            `;
            labelEl.addEventListener('click', () => selectAnswer(q.id, val, idx));
            grid.appendChild(labelEl);
        });
    }

    document.getElementById('btnPrev').style.display   = idx === 0 ? 'none' : 'flex';
    document.getElementById('btnNext').style.display   = idx < QUESTIONS.length - 1 ? 'flex' : 'none';
    document.getElementById('btnSubmit').style.display = idx === QUESTIONS.length - 1 ? 'flex' : 'none';
    
    // TDD Tracking
    logEvent('QUESTION_VIEW', { question_id: q.id, index: idx });
}

function selectAnswer(qId, val, idx) {
    answers[qId] = val;
    renderQuestion(idx);
    autosave(qId, val);
}

let essayTimers = {};
function saveEssay(qId, val) {
    answers[qId] = val;
    const status = document.getElementById(`essay-status-${qId}`);
    if (status) status.style.display = 'block';
    
    if (essayTimers[qId]) clearTimeout(essayTimers[qId]);
    essayTimers[qId] = setTimeout(() => {
        autosave(qId, val).then(() => {
            if (status) status.style.display = 'none';
        });
    }, 1000);
}

function nextQuestion() { if (currentIdx < QUESTIONS.length - 1) { currentIdx++; renderQuestion(currentIdx); localStorage.setItem(LS_KEY_IDX, currentIdx); } }
function prevQuestion() { if (currentIdx > 0) { currentIdx--; renderQuestion(currentIdx); localStorage.setItem(LS_KEY_IDX, currentIdx); } }

// ── AUTOSAVE ──
async function autosave(questionId, answer) {
    try {
        await fetch(AUTOSAVE_URL, {
            method:'POST',
            headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF_TOKEN},
            body: JSON.stringify({ question_id: questionId, answer })
        });
    } catch(e) {}
}

// ── TIMER ──
function startTimer() {
    const el   = document.getElementById('timerDisplay');
    const pill = document.getElementById('timerEl');
    
    const updateDisplay = () => {
        if (timeLeft < 0) timeLeft = 0;
        const m = String(Math.floor(timeLeft / 60)).padStart(2, '0');
        const s = String(timeLeft % 60).padStart(2, '0');
        el.textContent = `${m}:${s}`;

        if (timeLeft <= 60) {
            pill.className = 'timer-pill danger';
        } else if (timeLeft <= 300) {
            pill.className = 'timer-pill warning';
        } else {
            pill.className = 'timer-pill';
        }
    };

    updateDisplay();

    timerInterval = setInterval(() => {
        timeLeft--;
        updateDisplay();
        if (timeLeft <= 0) {
            clearInterval(timerInterval);
            submitQuiz();
        }
    }, 1000);
}

// ── SUBMIT ──
function confirmSubmit() {
    const answered   = Object.values(answers).filter(v => v !== null && v !== '').length;
    const unanswered = QUESTIONS.length - answered;
    document.getElementById('confirmText').innerHTML = unanswered > 0
        ? `⚠️ Masih ada <strong style="color:#D97706">${unanswered} soal</strong> yang belum dijawab.`
        : `Semua <strong style="color:#059669">${answered} soal</strong> sudah dijawab.`;
    document.getElementById('confirmModal').classList.add('open');
}
function closeConfirm() { document.getElementById('confirmModal').classList.remove('open'); }



function submitQuiz() {
    logEvent('QUIZ_SUBMIT_START');

    clearInterval(timerInterval);
    // Clear persistence on completion
    localStorage.removeItem(LS_KEY_IDX);
    localStorage.removeItem(LS_KEY_STRIKE);

    document.getElementById('submitFinalBtn').disabled = true;
    document.getElementById('submitFinalBtn').innerHTML = '<span class="dots-wave white"><span></span><span></span><span></span><span></span></span> Mengumpulkan...';
    
    const container = document.getElementById('hiddenAnswers');
    if (!container) {
        console.error('Final form container not found!');
        return;
    }
    
    container.innerHTML = '';
    QUESTIONS.forEach(q => {
        const val = answers[q.id] || '';
        const inp = document.createElement('input');
        inp.type = 'hidden'; 
        inp.name = `answers[${q.id}]`; 
        inp.value = val;
        container.appendChild(inp);
    });
    

    
    document.getElementById('submitForm').submit();
}

document.addEventListener('DOMContentLoaded', () => {
    // TDD Session Start
    logEvent('SESSION_START', {
        screen: `${window.screen.width}x${window.screen.height}`,
        ua: navigator.userAgent
    });
    
    @if(isset($selected))
        const saved = @json($selected ?? []);
        Object.assign(answers, saved);
    @endif
    renderQuestion(currentIdx);
    startTimer();

    // ── ANTI-CHEAT / INTEGRITY (Client-side) ──
    
    // 1. Tab-Switch / Visibility Detection
    let switchCount = parseInt(localStorage.getItem(LS_KEY_STRIKE)) || 0;
    
    document.addEventListener('visibilitychange', () => {
        const type = document.hidden ? 'TAB_BLUR' : 'TAB_FOCUS';
        logEvent(type, { timestamp: new Date().toISOString() });

        if (document.hidden) {
            switchCount++;
            localStorage.setItem(LS_KEY_STRIKE, switchCount);
            console.warn(`[Integrity] Tab switch detected. Count: ${switchCount}`);
        } else {
            if (switchCount >= 3) {
                logEvent('DISQUALIFIED', { switch_count: switchCount });
                // Clear persistence on disqualification
                localStorage.removeItem(LS_KEY_IDX);
                localStorage.removeItem(LS_KEY_STRIKE);
                showPahamAjaModal(`🚫 DISKUALIFIKASI`, `Anda telah berpindah tab sebanyak 3 kali. Sesuai aturan, pengerjaan kuis ini dibatalkan dan Anda harus mengulang dari awal.`, 'danger', 'Ulangi dari Awal', `{{ route('quiz.disqualify', $quiz->slug) }}`);
            } else {
                showPahamAjaModal(`⚠️ PERINGATAN INTEGRITAS`, `Sistem mendeteksi Anda meninggalkan halaman kuis. Tetaplah di sini untuk menjaga validitas jawaban Anda!\n\nPerpindahan terdeteksi (${switchCount}).`, 'warning');
            }
        }
    });

    window.showPahamAjaModal = function(title, text, type = 'warning', btnText = 'Mengerti', btnUrl = null) {
        const icon = document.getElementById('paModalIcon');
        const overlay = document.getElementById('paModalOverlay');
        const btn = overlay.querySelector('button');
        
        icon.innerHTML = type === 'warning' ? '<i class="fa-solid fa-triangle-exclamation"></i>' : '<i class="fa-solid fa-shield-halved"></i>';
        if (type === 'danger') icon.innerHTML = '<i class="fa-solid fa-ban"></i>';
        
        document.getElementById('paModalTitle').textContent = title;
        document.getElementById('paModalText').textContent = text;
        
        btn.textContent = btnText;
        if (btnUrl) {
            btn.onclick = () => { window.location.href = btnUrl; };
        } else {
            btn.onclick = closePahamAjaModal;
        }

        overlay.classList.add('open');
    }
    window.closePahamAjaModal = function() {
        document.getElementById('paModalOverlay').classList.remove('open');
    }

    // 2. Disable Right-Click
    document.addEventListener('contextmenu', e => {
        e.preventDefault();
        return false;
    });

    // 3. Disable Selection & Copy
    document.addEventListener('selectstart', e => e.preventDefault());
    document.addEventListener('copy', e => e.preventDefault());
    document.addEventListener('cut', e => e.preventDefault());

    // 4. Disable Keyboard Shortcuts (Ctrl+C, Ctrl+V, Ctrl+U, F12)
    document.addEventListener('keydown', e => {
        // F12
        if (e.keyCode === 123) { e.preventDefault(); return false; }
        // Ctrl+Shift+I, Ctrl+Shift+J, Ctrl+Shift+C, Ctrl+U
        if (e.ctrlKey && (e.shiftKey && (e.keyCode === 73 || e.keyCode === 74 || e.keyCode === 67) || e.keyCode === 85)) {
            e.preventDefault(); return false;
        }
        // Ctrl+C, Ctrl+V
        if (e.ctrlKey && (e.keyCode === 67 || e.keyCode === 86)) {
            e.preventDefault(); return false;
        }
    });

    // ── DEV CHEAT TRIGGER (Long Press 'Next' Button) ──
    if (IS_DEV) {
        let pressTimer;
        const triggerBtn = document.getElementById('btnNext');
        
        if (triggerBtn) {
            const startPress = (e) => {
                if (e.type === 'click') return; 
                pressTimer = setTimeout(() => {
                    devModeActive = !devModeActive;
                    renderQuestion(currentIdx);
                    
                    // Subtle feedback
                    triggerBtn.style.opacity = '0.7';
                    setTimeout(() => triggerBtn.style.opacity = '', 200);
                    
                    if (window.navigator.vibrate) window.navigator.vibrate(50);
                    console.log(`[DevMode] ${devModeActive ? 'ON' : 'OFF'}`);
                }, 1500); // 1.5 Seconds long press
            };

            const cancelPress = () => {
                clearTimeout(pressTimer);
            };

            triggerBtn.addEventListener('mousedown', startPress);
            triggerBtn.addEventListener('touchstart', startPress, {passive: true});
            triggerBtn.addEventListener('mouseup', cancelPress);
            triggerBtn.addEventListener('mouseleave', cancelPress);
            triggerBtn.addEventListener('touchend', cancelPress);
        }
    }
});
</script>
</body>
</html>
