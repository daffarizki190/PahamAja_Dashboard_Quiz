<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $quiz->title }} – Ikuti kuis sekarang">
    <title>{{ $quiz->title }} – PahamAja Live</title>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>

    <style>
        :root {
            --bg-base: rgb(70, 26, 66);
            --card-bg: rgba(9, 9, 9, 0.55);
            --card-bg-solid: rgba(9, 9, 9, 0.8);
            --text-white: #fff;
            --text-muted: rgba(255,255,255,0.6);
            --opt1-start: rgb(184, 186, 2);
            --opt1-end: rgb(94, 99, 0);
            --opt1-shadow: rgb(94, 99, 0);
            --opt2-start: rgb(190, 119, 252);
            --opt2-end: rgb(103, 27, 184);
            --opt2-shadow: rgb(103, 27, 184);
            --opt3-start: rgb(255, 132, 37);
            --opt3-end: rgb(220, 66, 1);
            --opt3-shadow: rgb(220, 66, 1);
            --opt4-start: rgb(33, 213, 198);
            --opt4-end: rgb(0, 111, 94);
            --opt4-shadow: rgb(0, 111, 94);
            --success: #10B981;
            --danger: #EF4444;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Quicksand', 'Helvetica', 'Arial', sans-serif;
            background-color: var(--bg-base);
            background-image: url('https://cf.quizizz.com/themes/v2/classic/joinClassicWBg.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: var(--text-white);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ── HEADER ── */
        .quiz-header {
            background: var(--card-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255,255,255,0.08);
            padding: 0 24px;
            height: 64px;
            display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 50;
        }

        .quiz-logo { display: flex; align-items: center; gap: 10px; }
        .quiz-logo-icon {
            width: 36px; height: 36px; border-radius: 10px;
            background: linear-gradient(135deg, #A78BFA, #7C3AED);
            display: flex; align-items: center; justify-content: center;
            font-size: 15px; font-weight: 700; font-style: italic; color: #fff;
            box-shadow: 0 4px 10px rgba(124, 58, 237, 0.4);
        }
        .quiz-logo-name { font-size: 15px; font-weight: 700; color: #fff; }

        .gamification-stats { display: flex; gap: 12px; align-items: center; }

        .stat-badge {
            background: rgba(255,255,255,0.1);
            border-radius: 12px;
            padding: 6px 14px;
            display: flex; align-items: center; gap: 6px;
            font-weight: 700; font-size: 15px;
            border: 1px solid rgba(255,255,255,0.12);
            transition: all 0.3s;
            color: #fff;
        }
        .stat-badge.score { color: #FBBF24; }
        .stat-badge.streak { color: #F97316; }
        .stat-badge i { font-size: 16px; }

        .powerup-btn {
            background: linear-gradient(135deg, #EF4444, #B91C1C);
            color: #fff;
            border: none; border-radius: 10px; padding: 7px 14px;
            font-weight: 700; font-size: 13px; font-family: inherit;
            cursor: pointer; display: flex; align-items: center; gap: 5px;
            box-shadow: 0 4px 0 #991B1B;
            transition: all 0.15s;
        }
        .attack-btn {
            background: rgba(255, 0, 0, 0.2); border: 1px solid rgba(255, 0, 0, 0.5); color: #ff4d4d;
            padding: 8px 16px; border-radius: 8px; font-weight: bold; font-family: inherit;
            cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 8px;
        }
        .attack-btn:not(:disabled):hover { background: rgba(255, 0, 0, 0.4); transform: scale(1.05); }
        .attack-btn:disabled { opacity: 0.5; cursor: not-allowed; }

        /* Target Modal */
        .target-modal {
            position: fixed; top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0, 0, 0, 0.8); z-index: 2000;
            display: none; justify-content: center; align-items: center;
        }
        .target-modal.active { display: flex; }
        .target-modal-content {
            background: rgba(30, 30, 40, 0.95); border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 24px; border-radius: 16px; width: 90%; max-width: 400px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }
        .target-modal-title { font-size: 1.25rem; font-weight: bold; margin-bottom: 16px; color: #fff; }
        .target-list {
            display: flex; flex-direction: column; gap: 8px; max-height: 300px; overflow-y: auto;
            margin-bottom: 16px; padding-right: 4px;
        }
        .target-item {
            background: rgba(255, 255, 255, 0.05); padding: 12px; border-radius: 8px;
            cursor: pointer; transition: all 0.2s; border: 1px solid transparent;
            display: flex; justify-content: space-between; align-items: center;
        }
        .target-item:hover { background: rgba(255, 255, 255, 0.1); }
        .target-item.selected { border-color: #ff4d4d; background: rgba(255, 77, 77, 0.1); }
        .modal-actions { display: flex; justify-content: flex-end; gap: 12px; }
        .modal-btn {
            padding: 8px 16px; border-radius: 8px; font-weight: bold; cursor: pointer; transition: all 0.2s;
            border: none;
        }
        .modal-btn.cancel { background: rgba(255,255,255,0.1); color: #fff; }
        .modal-btn.cancel:hover { background: rgba(255,255,255,0.2); }
        .modal-btn.confirm { background: #ff4d4d; color: #fff; }
        .modal-btn.confirm:hover { background: #ff3333; }
        .modal-btn:disabled { opacity: 0.5; cursor: not-allowed; }

        .powerup-btn:disabled { background: rgba(255,255,255,0.08); color: rgba(255,255,255,0.3); box-shadow: none; cursor: not-allowed; }
        .powerup-btn:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 6px 0 #991B1B; }
        .powerup-btn:active:not(:disabled) { transform: translateY(2px); box-shadow: 0 2px 0 #991B1B; }

        .timer-pill {
            display: flex; align-items: center; gap: 8px;
            background: rgba(255,255,255,0.1);
            color: #fff;
            border-radius: 12px; padding: 8px 16px;
            font-size: 18px; font-weight: 700; font-variant-numeric: tabular-nums;
            border: 1px solid rgba(255,255,255,0.12);
            transition: all 0.3s;
        }
        .timer-pill.warning { background: rgba(251,191,36,0.2); color: #FCD34D; border-color: rgba(251,191,36,0.3); }
        .timer-pill.danger { background: rgba(239,68,68,0.25); color: #FCA5A5; border-color: rgba(239,68,68,0.4); animation: timerPulse 1.5s ease-in-out infinite; }
        @keyframes timerPulse { 0%, 100% { opacity: 1; transform: scale(1); } 50% { opacity: 0.7; transform: scale(1.05); } }

        /* ── PROGRESS ── */
        .progress-bar-wrap {
            background: rgba(0,0,0,0.25);
            padding: 10px 24px;
        }
        .progress-label { font-size: 12px; font-weight: 700; color: rgba(255,255,255,0.7); margin-bottom: 6px; display: flex; justify-content: space-between; }
        .progress-track { height: 8px; background: rgba(255,255,255,0.1); border-radius: 99px; overflow: hidden; }
        .progress-fill {
            height: 100%; border-radius: 99px;
            background: linear-gradient(90deg, #34D399, #10B981);
            transition: width 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
            position: relative;
        }
        .progress-fill::after {
            content: ''; position: absolute; top: 0; left: 0; bottom: 0; right: 0;
            background: linear-gradient(90deg, rgba(255,255,255,0) 0%, rgba(255,255,255,0.4) 50%, rgba(255,255,255,0) 100%);
            animation: shimmer 1.5s infinite;
        }
        @keyframes shimmer { 0% { transform: translateX(-100%); } 100% { transform: translateX(100%); } }

        /* ── MAIN LAYOUT ── */
        .quiz-body {
            padding: 32px 24px;
            max-width: 900px;
            margin: 0 auto;
            display: flex; flex-direction: column;
            min-height: calc(100vh - 64px - 50px);
        }

        /* ── QUESTION CARD ── */
        .q-card-wrapper {
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            flex: 1; display: flex; flex-direction: column;
        }

        .slide-out-left { animation: slideOutLeft 0.4s forwards cubic-bezier(0.4, 0, 0.2, 1); }
        .slide-in-right { animation: slideInRight 0.4s forwards cubic-bezier(0.34, 1.56, 0.64, 1); }
        .shake-animation { animation: shake 0.5s cubic-bezier(.36,.07,.19,.97) both; }

        @keyframes slideOutLeft {
            0% { transform: translateX(0) scale(1); opacity: 1; }
            100% { transform: translateX(-80%) scale(0.95); opacity: 0; }
        }
        @keyframes slideInRight {
            0% { transform: translateX(80%) scale(0.95); opacity: 0; }
            100% { transform: translateX(0) scale(1); opacity: 1; }
        }
        @keyframes shake {
            10%, 90% { transform: translate3d(-2px, 0, 0); }
            20%, 80% { transform: translate3d(4px, 0, 0); }
            30%, 50%, 70% { transform: translate3d(-8px, 0, 0); }
            40%, 60% { transform: translate3d(8px, 0, 0); }
        }

        /* Attack Effects */
        .effect-freeze { 
            animation: freezeAnim 0.5s forwards;
            pointer-events: none; 
        }
        @keyframes freezeAnim {
            0% { filter: none; box-shadow: none; }
            100% { filter: grayscale(100%) blur(2px) sepia(50%) hue-rotate(180deg) brightness(80%); box-shadow: inset 0 0 100px rgba(0, 200, 255, 0.5); }
        }
        
        .effect-blind { animation: blindAnim 0.5s forwards; }
        @keyframes blindAnim {
            0% { filter: none; }
            100% { filter: contrast(200%) brightness(20%) blur(6px); }
        }

        .effect-glitch { animation: screenGlitch 0.1s infinite; }
        @keyframes screenGlitch {
            0% { transform: translate(0); filter: none; }
            20% { transform: translate(-8px, 8px); filter: hue-rotate(90deg) invert(10%); }
            40% { transform: translate(-8px, -8px); filter: hue-rotate(180deg) invert(20%); }
            60% { transform: translate(8px, 8px); filter: hue-rotate(270deg) invert(10%); }
            80% { transform: translate(8px, -8px); filter: hue-rotate(360deg) invert(20%); }
            100% { transform: translate(0); filter: none; }
        }

        .q-card {
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 16px;
            padding: 32px;
            flex: 1; display: flex; flex-direction: column;
        }

        .q-number {
            font-size: 13px; font-weight: 600; color: rgba(255,255,255,0.4);
            text-transform: uppercase; letter-spacing: 1px;
            margin-bottom: 16px; text-align: center;
        }

        .q-text {
            font-size: 20px; font-weight: 700; color: #fff;
            line-height: 1.6; text-align: center;
            margin-bottom: 32px;
            flex-shrink: 0;
        }

        /* ── OPTIONS GRID (2x2 Wayground-style) ── */
        .options-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            flex: 1;
        }

        .option-item {
            display: flex; align-items: center; justify-content: center;
            gap: 12px;
            padding: 20px 24px;
            border-radius: 8px;
            cursor: pointer;
            transition: transform 0.1s ease, box-shadow 0.1s ease;
            position: relative;
            overflow: hidden;
            min-height: 80px;
            border: none;
            user-select: none;
        }
        .option-item:active:not(.answered) {
            transform: translateY(4px);
        }

        /* ── 4 Option Color Themes (Wayground) ── */
        .option-item.opt-0 {
            background: linear-gradient(180deg, var(--opt1-start), var(--opt1-end));
            box-shadow: 0 6px 0 var(--opt1-shadow);
        }
        .option-item.opt-0:active:not(.answered) { box-shadow: 0 2px 0 var(--opt1-shadow); }

        .option-item.opt-1 {
            background: linear-gradient(180deg, var(--opt2-start), var(--opt2-end));
            box-shadow: 0 6px 0 var(--opt2-shadow);
        }
        .option-item.opt-1:active:not(.answered) { box-shadow: 0 2px 0 var(--opt2-shadow); }

        .option-item.opt-2 {
            background: linear-gradient(180deg, var(--opt3-start), var(--opt3-end));
            box-shadow: 0 6px 0 var(--opt3-shadow);
        }
        .option-item.opt-2:active:not(.answered) { box-shadow: 0 2px 0 var(--opt3-shadow); }

        .option-item.opt-3 {
            background: linear-gradient(180deg, var(--opt4-start), var(--opt4-end));
            box-shadow: 0 6px 0 var(--opt4-shadow);
        }
        .option-item.opt-3:active:not(.answered) { box-shadow: 0 2px 0 var(--opt4-shadow); }

        /* Fallback for 5th+ options */
        .option-item.opt-4, .option-item.opt-5 {
            background: linear-gradient(180deg, #6366F1, #4338CA);
            box-shadow: 0 6px 0 #3730A3;
        }

        /* State: Correct */
        .option-item.correct {
            background: linear-gradient(180deg, #34D399, #059669) !important;
            box-shadow: 0 6px 0 #047857 !important;
            animation: popCorrect 0.4s ease;
        }
        /* State: Incorrect */
        .option-item.incorrect {
            background: linear-gradient(180deg, #F87171, #DC2626) !important;
            box-shadow: 0 6px 0 #991B1B !important;
            opacity: 0.9;
        }
        .option-item.fade-out {
            opacity: 0.25; pointer-events: none;
            transform: scale(0.95);
        }
        .option-item.answered { cursor: default; }

        @keyframes popCorrect {
            0% { transform: scale(1); }
            40% { transform: scale(1.06); }
            100% { transform: scale(1); }
        }

        .option-bubble {
            width: 32px; height: 32px; border-radius: 50%;
            flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
            font-size: 15px; font-weight: 700;
            background: rgba(255,255,255,0.25); color: #fff;
        }

        .option-label {
            font-size: 16px; font-weight: 700; line-height: 1.4;
            flex: 1; color: #fff;
            text-shadow: 0 1px 3px rgba(0,0,0,0.2);
        }

        /* Floating Points Animation */
        .floating-points {
            position: fixed;
            font-size: 32px; font-weight: 700; color: #34D399;
            text-shadow: 0 2px 12px rgba(52, 211, 153, 0.5);
            pointer-events: none;
            animation: floatUp 1.2s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
            z-index: 100;
            font-family: 'Quicksand', sans-serif;
        }
        @keyframes floatUp {
            0% { opacity: 0; transform: translateY(20px) scale(0.5); }
            20% { opacity: 1; transform: translateY(-10px) scale(1.3); }
            100% { opacity: 0; transform: translateY(-80px) scale(1); }
        }

        /* ── ESSAY ── */
        .essay-textarea {
            width: 100%; border-radius: 12px;
            border: 2px solid rgba(255,255,255,0.15);
            padding: 20px; font-family: inherit; font-size: 16px; font-weight: 600;
            color: #fff; background: rgba(255,255,255,0.06);
            transition: all 0.2s;
            resize: vertical; min-height: 180px; outline: none;
        }
        .essay-textarea:focus {
            border-color: rgba(167, 139, 250, 0.5);
            background: rgba(255,255,255,0.1);
            box-shadow: 0 0 0 4px rgba(167, 139, 250, 0.15);
        }
        .essay-textarea::placeholder { color: rgba(255,255,255,0.35); }

        /* ── NAVIGATION ── */
        .nav-buttons {
            display: flex; justify-content: space-between; align-items: center;
            margin-top: 20px; gap: 12px;
        }
        .btn-nav {
            display: flex; align-items: center; gap: 8px; padding: 12px 28px;
            border-radius: 10px; font-size: 15px; font-weight: 700; cursor: pointer;
            transition: all 0.15s; font-family: inherit; border: none;
        }
        .btn-prev {
            background: rgba(255,255,255,0.1); color: rgba(255,255,255,0.7);
            box-shadow: 0 4px 0 rgba(255,255,255,0.05);
        }
        .btn-prev:hover { background: rgba(255,255,255,0.15); color: #fff; transform: translateY(-2px); box-shadow: 0 6px 0 rgba(255,255,255,0.08); }
        .btn-prev:active { transform: translateY(2px); box-shadow: 0 2px 0 rgba(255,255,255,0.05); }

        .btn-next, .btn-submit {
            background: linear-gradient(135deg, #A78BFA, #7C3AED);
            color: #fff;
            box-shadow: 0 5px 0 #5B21B6;
            margin-left: auto;
        }
        .btn-next:hover, .btn-submit:hover { filter: brightness(1.1); transform: translateY(-2px); box-shadow: 0 7px 0 #5B21B6; }
        .btn-next:active, .btn-submit:active { transform: translateY(3px); box-shadow: 0 2px 0 #5B21B6; }

        /* ── Attack Alert Overlay ── */
        .attack-alert {
            position: fixed; top: 80px; left: 50%; transform: translateX(-50%);
            background: linear-gradient(135deg, #EF4444, #DC2626);
            color: #fff; padding: 14px 28px; border-radius: 14px;
            font-weight: 700; z-index: 9999;
            box-shadow: 0 10px 30px rgba(239,68,68,0.5);
            font-size: 17px;
            animation: attackAlertIn 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        @keyframes attackAlertIn {
            0% { opacity: 0; transform: translateX(-50%) translateY(-30px) scale(0.8); }
            100% { opacity: 1; transform: translateX(-50%) translateY(0) scale(1); }
        }

        /* ── MODAL ── */
        .modal-bg { display:none; position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,0.7); backdrop-filter:blur(8px); align-items:center; justify-content:center; }
        .modal-bg.open { display:flex; }
        .modal-box {
            background: var(--card-bg-solid);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 20px; padding: 36px; max-width: 420px; width: 90%;
            animation: modalScale .3s cubic-bezier(0.34, 1.56, 0.64, 1);
            text-align: center; color: #fff;
        }
        @keyframes modalScale { from { opacity:0; transform:scale(0.9); } to { opacity:1; transform:scale(1); } }

        /* ── RESPONSIVE ── */
        @media (max-width: 768px) {
            .quiz-body { padding: 16px 12px; }
            .q-card { padding: 20px 16px; border-radius: 14px; }
            .q-text { font-size: 17px; margin-bottom: 20px; }
            .options-grid { grid-template-columns: 1fr; gap: 10px; }
            .option-item { min-height: 60px; padding: 16px 20px; }
            .gamification-stats { gap: 6px; }
            .stat-badge { padding: 5px 10px; font-size: 13px; }
            .quiz-logo-name { display: none; }
            .btn-nav { width: 100%; justify-content: center; padding: 12px 20px; }
            .nav-buttons { flex-direction: column; }
            .btn-next, .btn-submit { margin-left: 0; }
        }
    </style>
</head>
<body>

<!-- Header -->
<header class="quiz-header">
    <div class="quiz-logo">
        <div class="quiz-logo-icon">P</div>
        <div class="quiz-logo-name">Paham<span style="color:#A78BFA">Aja</span></div>
    </div>

    <!-- Gamification Stats -->
    <div class="gamification-stats">
        <button id="attackBtn" class="powerup-btn" onclick="triggerAttack()" title="Klik untuk menyerang pemain lain!">
            <i class="fa-solid fa-crosshairs"></i> Serang!
        </button>
        <div class="stat-badge streak" id="streakBadge">
            <i class="fa-solid fa-fire"></i> <span id="streakCount">0</span>
        </div>
        <div class="stat-badge score" id="scoreBadge">
            <i class="fa-solid fa-star"></i> <span id="scoreCount">0</span>
        </div>
    </div>

    <div id="timerEl" class="timer-pill">
        <i class="fa-solid fa-clock"></i> <span id="timerDisplay">--:--</span>
    </div>
</header>

<!-- Progress Bar -->
<div class="progress-bar-wrap">
    <div class="progress-label">
        <span id="progressLabel">Soal 1 dari {{ count($quiz->questions) }}</span>
        <span style="color: #A78BFA;"><i class="fa-solid fa-bolt"></i> Live Mode</span>
    </div>
    <div class="progress-track">
        <div class="progress-fill" id="progressFill" style="width: 0%"></div>
    </div>
</div>

<!-- Main Quiz -->
<div class="quiz-body" id="quizBody">
    <div class="q-card-wrapper" id="qCardWrapper">
        <div class="q-card">
            <div class="q-number" id="questionNumber">SOAL 1</div>
            <div class="q-text" id="questionText">Memuat soal...</div>

            <form id="quizForm" onsubmit="return false;">
                <div class="options-grid" id="optionsGrid">
                    <!-- Rendered by JS -->
                </div>
            </form>

            <!-- Navigation -->
            <div class="nav-buttons">
                <button class="btn-nav btn-prev" id="btnPrev" onclick="prevQuestion()" style="display:none;">
                    <i class="fa-solid fa-arrow-left"></i> Sebelumnya
                </button>
                <button class="btn-nav btn-next" id="btnNext" onclick="forceNextQuestion()">
                    Selanjutnya <i class="fa-solid fa-arrow-right"></i>
                </button>
                <button class="btn-nav btn-submit" id="btnSubmit" onclick="confirmSubmit()" style="display:none;">
                    <i class="fa-solid fa-flag-checkered"></i> Selesai
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Confirm Submit Modal -->
<div class="modal-bg" id="confirmModal">
    <div class="modal-box">
        <div style="width:68px; height:68px; border-radius:18px; background:linear-gradient(135deg, #FBBF24, #F59E0B); color:#78350F; display:flex; align-items:center; justify-content:center; font-size:30px; margin:0 auto 18px; box-shadow: 0 8px 20px rgba(251,191,36,0.4);">
            <i class="fa-solid fa-trophy"></i>
        </div>
        <h2 style="font-size:22px; font-weight:700; color:#fff; margin-bottom:10px;">Selesai Kuis?</h2>
        <p style="font-size:14px; color:rgba(255,255,255,0.6); line-height:1.6; margin-bottom:28px;" id="confirmText">
            Kamu mengumpulkan <strong id="finalScoreDisplay" style="color:#FBBF24">0</strong> poin. Pastikan semua jawaban sudah benar!
        </p>

        <div style="display:flex; gap:12px;">
            <button onclick="closeConfirm()" class="btn-nav btn-prev" style="flex:1; justify-content:center;">
                Kembali
            </button>
            <button onclick="submitQuiz()" class="btn-nav btn-submit" style="flex:1; justify-content:center; margin:0;" id="submitFinalBtn">
                Kumpulkan
            </button>
        </div>
    </div>
</div>

<!-- Target Selection Modal -->
<div id="targetModal" class="target-modal">
    <div class="target-modal-content">
        <div class="target-modal-title">Pilih Target Serangan</div>
        <div id="targetList" class="target-list">
            <!-- Targets will be loaded here -->
        </div>
        <div class="modal-actions">
            <button class="modal-btn cancel" onclick="closeTargetModal()">Batal</button>
            <button id="confirmAttackBtn" class="modal-btn confirm" onclick="confirmAttack()" disabled>Serang!</button>
        </div>
    </div>
</div>

<!-- Hidden submit form -->
<form id="submitForm" action="{{ route('quiz.storeAnswer', ['quiz' => $quiz->slug, 'participant' => $participant->id]) }}" method="POST" style="display:none;">
    @csrf
    <div id="hiddenAnswers"></div>
</form>

<script>
const QUESTIONS = @json($quiz->questions);
const AUTOSAVE_URL = "{{ route('quiz.autosave', ['quiz' => $quiz->slug, 'participant' => $participant->id]) }}";
const TIME_LIMIT   = {{ $remainingSeconds }};
const CSRF_TOKEN   = '{{ csrf_token() }}';
const P_ID         = '{{ $participant->id }}';
const ATTACK_URL   = "{{ route('quiz.attack', ['quiz' => $quiz->slug, 'participant' => $participant->id]) }}";

let currentIdx = 0;
const answers  = {};
let timeLeft   = TIME_LIMIT;
let timerInterval;

// Gamification State
let currentScore = 0;
let currentStreak = 0;
let isAnimating = false;
let usedAttacks = 0;

// Initialize saved answers if any
@if(isset($selected))
    const saved = @json($selected ?? []);
    Object.assign(answers, saved);
    QUESTIONS.forEach(q => {
        if(saved[q.id] && q.type !== 'essay') {
            const opt = q.options.find(o => String(o.id) === String(saved[q.id]));
            if(opt && opt.is_correct) currentScore += 100;
        }
    });
@endif

// ── GAMIFICATION LOGIC ──
function updateGamificationUI() {
    document.getElementById('scoreCount').textContent = currentScore;
    document.getElementById('streakCount').textContent = currentStreak;

    const streakBadge = document.getElementById('streakBadge');
    if(currentStreak >= 3) {
        streakBadge.style.background = 'rgba(249,115,22,0.25)';
        streakBadge.style.transform = 'scale(1.1)';
        setTimeout(() => streakBadge.style.transform = 'scale(1)', 200);
    } else {
        streakBadge.style.background = 'rgba(255,255,255,0.1)';
    }

    const btn = document.getElementById('attackBtn');
    let attacksAvailable = Math.floor(currentScore / 200) - usedAttacks;

    if (attacksAvailable > 0) {
        if (btn.disabled) showFloatingPoints(window.innerWidth/2, 80, "⚡ POWER-UP!");
        btn.disabled = false;
        btn.innerHTML = `<i class="fa-solid fa-crosshairs"></i> Serang! (${attacksAvailable}x)`;
    } else {
        btn.disabled = true;
        btn.innerHTML = `<i class="fa-solid fa-crosshairs"></i> Butuh ${ (usedAttacks + 1) * 200 } Poin`;
    }
}

function showFloatingPoints(x, y, text) {
    const el = document.createElement('div');
    el.className = 'floating-points';
    el.textContent = text;
    el.style.left = x + 'px';
    el.style.top = y + 'px';
    document.body.appendChild(el);
    setTimeout(() => el.remove(), 1200);
}

// ── RENDER QUESTION ──
function renderQuestion(idx, direction = 'right') {
    const q = QUESTIONS[idx];
    if (!q) return;

    const wrapper = document.getElementById('qCardWrapper');
    wrapper.className = 'q-card-wrapper';
    void wrapper.offsetWidth;
    wrapper.classList.add('slide-in-right');

    document.getElementById('questionNumber').textContent = `SOAL ${idx + 1}`;
    document.getElementById('questionText').innerHTML = q.text || q.question || '';
    document.getElementById('progressLabel').textContent = `Soal ${idx + 1} dari ${QUESTIONS.length}`;
    document.getElementById('progressFill').style.width = Math.round(((idx + 1) / QUESTIONS.length) * 100) + '%';

    const savedVal = answers[q.id] || "";
    const grid = document.getElementById('optionsGrid');
    grid.innerHTML = '';

    if (q.type === 'essay') {
        const wrap = document.createElement('div');
        wrap.style.gridColumn = '1 / -1';
        wrap.innerHTML = `
            <textarea class="essay-textarea" placeholder="Tuliskan jawaban analisismu di sini..." oninput="saveEssay(${q.id}, this.value)">${savedVal}</textarea>
        `;
        grid.appendChild(wrap);
        document.getElementById('btnNext').style.display = idx < QUESTIONS.length - 1 ? 'flex' : 'none';

    } else if (q.options && q.options.length > 0) {
        const labels = ['A', 'B', 'C', 'D', 'E', 'F'];
        q.options.forEach((opt, i) => {
            const letter = labels[i] || '';
            const val = String(opt.id);
            const isSel = String(savedVal) === val;

            const labelEl = document.createElement('div');
            labelEl.className = `option-item opt-${i}` +
                (isSel && opt.is_correct ? ' correct answered' : '') +
                (isSel && !opt.is_correct ? ' incorrect answered' : '') +
                (!isSel && savedVal ? ' fade-out' : '');
            labelEl.id = `opt_${val}`;

            const bubbleContent = isSel && opt.is_correct ? '<i class="fa-solid fa-check"></i>' :
                                  (isSel && !opt.is_correct ? '<i class="fa-solid fa-xmark"></i>' : letter);

            labelEl.innerHTML = `
                <div class="option-bubble">${bubbleContent}</div>
                <div class="option-label">${opt.text}</div>
            `;

            if(!savedVal) {
                labelEl.addEventListener('click', (e) => handleOptionClick(e, q, opt, i));
            }

            grid.appendChild(labelEl);
        });

        if(!savedVal) {
            document.getElementById('btnNext').style.display = 'none';
        } else {
            document.getElementById('btnNext').style.display = idx < QUESTIONS.length - 1 ? 'flex' : 'none';
        }
    }

    document.getElementById('btnPrev').style.display   = idx === 0 ? 'none' : 'flex';
    document.getElementById('btnSubmit').style.display = idx === QUESTIONS.length - 1 ? 'flex' : 'none';

    isAnimating = false;
}

function handleOptionClick(e, q, opt, optIdx) {
    if(isAnimating) return;
    isAnimating = true;

    const val = String(opt.id);
    answers[q.id] = val;
    autosave(q.id, val);

    const clickedEl = document.getElementById(`opt_${val}`);
    const allOptions = document.querySelectorAll('.option-item');

    // Mark all as answered
    allOptions.forEach(el => {
        el.classList.add('answered');
        if(el.id !== `opt_${val}`) el.classList.add('fade-out');
    });

    if(opt.is_correct) {
        clickedEl.classList.add('correct');
        clickedEl.querySelector('.option-bubble').innerHTML = '<i class="fa-solid fa-check"></i>';

        currentStreak++;
        let points = 100 + (currentStreak > 2 ? 50 : 0);
        currentScore += points;

        confetti({
            particleCount: 60, spread: 70,
            origin: { x: e.clientX / window.innerWidth, y: e.clientY / window.innerHeight },
            colors: ['#34D399', '#10B981', '#FBBF24', '#ffffff']
        });

        showFloatingPoints(e.clientX, e.clientY - 30, '+' + points);
        updateGamificationUI();

    } else {
        clickedEl.classList.add('incorrect');
        clickedEl.querySelector('.option-bubble').innerHTML = '<i class="fa-solid fa-xmark"></i>';

        const wrapper = document.getElementById('qCardWrapper');
        wrapper.classList.remove('slide-in-right');
        void wrapper.offsetWidth;
        wrapper.classList.add('shake-animation');

        currentStreak = 0;
        updateGamificationUI();

        // Reveal correct answer
        const correctOpt = q.options.find(o => o.is_correct);
        if(correctOpt) {
            const correctEl = document.getElementById(`opt_${correctOpt.id}`);
            if(correctEl) {
                correctEl.classList.remove('fade-out');
                correctEl.classList.add('correct');
            }
        }
    }

    setTimeout(() => {
        if(currentIdx < QUESTIONS.length - 1) {
            transitionToNext();
        } else {
            document.getElementById('btnSubmit').style.display = 'flex';
            isAnimating = false;
        }
    }, 1500);
}

function transitionToNext() {
    const wrapper = document.getElementById('qCardWrapper');
    wrapper.className = 'q-card-wrapper slide-out-left';
    setTimeout(() => {
        currentIdx++;
        renderQuestion(currentIdx, 'right');
    }, 350);
}

let essayTimers = {};
function saveEssay(qId, val) {
    answers[qId] = val;
    if (essayTimers[qId]) clearTimeout(essayTimers[qId]);
    essayTimers[qId] = setTimeout(() => { autosave(qId, val); }, 1000);
}

function forceNextQuestion() {
    if(isAnimating) return;
    if (currentIdx < QUESTIONS.length - 1) transitionToNext();
}
function prevQuestion() {
    if(isAnimating) return;
    if (currentIdx > 0) {
        const wrapper = document.getElementById('qCardWrapper');
        wrapper.className = 'q-card-wrapper slide-out-left';
        setTimeout(() => {
            currentIdx--;
            renderQuestion(currentIdx, 'right');
        }, 350);
    }
}

// ── AUTOSAVE ──
async function autosave(questionId, answer) {
    try {
        await fetch(AUTOSAVE_URL, {
            method:'POST',
            headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF_TOKEN},
            body: JSON.stringify({ question_id: questionId, answer })
        });
    } catch(e) { console.error("Autosave failed"); }
}

// ── TIMER ──
function startTimer() {
    const el   = document.getElementById('timerDisplay');
    const pill = document.getElementById('timerEl');

    const updateDisplay = () => {
        if (timeLeft < 0) timeLeft = 0;
        const h = Math.floor(timeLeft / 3600);
        const m = Math.floor((timeLeft % 3600) / 60);
        const s = Math.floor(timeLeft % 60);

        el.textContent = h > 0
            ? `${String(h).padStart(2, '0')}:${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`
            : `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;

        if (timeLeft <= 60) pill.className = 'timer-pill danger';
        else if (timeLeft <= 300) pill.className = 'timer-pill warning';
        else pill.className = 'timer-pill';
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
    document.getElementById('finalScoreDisplay').textContent = currentScore;
    document.getElementById('confirmModal').classList.add('open');
    confetti({ particleCount: 150, spread: 100, origin: { y: 0.6 }, colors: ['#FBBF24', '#A78BFA', '#34D399', '#F87171'] });
}
function closeConfirm() { document.getElementById('confirmModal').classList.remove('open'); }

function submitQuiz() {
    clearInterval(timerInterval);
    const btn = document.getElementById('submitFinalBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Memproses...';

    const container = document.getElementById('hiddenAnswers');
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

// ── MULTIPLAYER ATTACK API ──
let selectedTargetId = null;
const ACTIVE_TARGETS_URL = "{{ route('quiz.active-targets', ['quiz' => $quiz->slug, 'participant' => $participant->id]) }}";

async function triggerAttack() {
    let attacksAvailable = Math.floor(currentScore / 200) - usedAttacks;
    if (attacksAvailable <= 0) return;

    const btn = document.getElementById('attackBtn');
    if(btn.disabled) return;

    // Show modal and fetch targets
    document.getElementById('targetModal').classList.add('active');
    const targetList = document.getElementById('targetList');
    targetList.innerHTML = '<div style="color:#aaa; text-align:center;">Memuat target...</div>';
    document.getElementById('confirmAttackBtn').disabled = true;
    selectedTargetId = null;

    try {
        const res = await fetch(ACTIVE_TARGETS_URL);
        const targets = await res.json();

        targetList.innerHTML = '';
        if (targets.length === 0) {
            targetList.innerHTML = '<div style="color:#ff4d4d; text-align:center;">Tidak ada pemain lain yang aktif.</div>';
            return;
        }

        targets.forEach(t => {
            const el = document.createElement('div');
            el.className = 'target-item';
            el.innerHTML = `<span>${t.name}</span> <i class="fa-solid fa-crosshairs"></i>`;
            el.onclick = () => selectTarget(t.id, el);
            targetList.appendChild(el);
        });
    } catch(e) {
        targetList.innerHTML = '<div style="color:#ff4d4d; text-align:center;">Gagal memuat target.</div>';
    }
}

function selectTarget(id, element) {
    selectedTargetId = id;
    document.querySelectorAll('.target-item').forEach(el => el.classList.remove('selected'));
    element.classList.add('selected');
    document.getElementById('confirmAttackBtn').disabled = false;
}

function closeTargetModal() {
    document.getElementById('targetModal').classList.remove('active');
    selectedTargetId = null;
}

async function confirmAttack() {
    if (!selectedTargetId) return;
    
    const targetIdToSend = selectedTargetId; // Save it before closing modal
    closeTargetModal();

    const btn = document.getElementById('attackBtn');
    btn.disabled = true;
    usedAttacks++;
    updateGamificationUI();

    const types = ['freeze', 'glitch', 'blind'];
    const attackType = types[Math.floor(Math.random() * types.length)];
    const durationMs = 3000 + (currentScore * 10); // Base 3s, +1s for every 100 points

    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyerang...';

    try {
        const res = await fetch(ATTACK_URL, {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN},
            body: JSON.stringify({ attack_type: attackType, duration: durationMs, target_id: targetIdToSend })
        });
        const data = await res.json();

        if(data.ok) {
            btn.innerHTML = `<i class="fa-solid fa-check"></i> ${data.target_name} diserang!`;
            setTimeout(() => { updateGamificationUI(); }, 3000);
        } else {
            console.error("Attack failed:", data);
            btn.innerHTML = `<i class="fa-solid fa-xmark"></i> ${data.message || 'Gagal'}`;
            usedAttacks--;
            setTimeout(() => { updateGamificationUI(); }, 3000);
        }
    } catch(e) {
        console.error("Attack error:", e);
        usedAttacks--;
        updateGamificationUI();
    }
}

// ── POLLING FOR ATTACKS (FALLBACK) ──
const CHECK_ATTACK_URL = "{{ route('quiz.check-attack', ['quiz' => $quiz->slug, 'participant' => $participant->id]) }}";

setInterval(async () => {
    try {
        const res = await fetch(CHECK_ATTACK_URL, {
            headers: {'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json'}
        });
        const data = await res.json();
        if (data.attacked) {
            receiveAttack(data.attacker_name, data.attack_type, data.duration);
        }
    } catch(e) {
        // ignore errors for polling
    }
}, 2000);

function receiveAttack(attackerName, attackType, duration = 5000) {
    const body = document.getElementById('quizBody');

    const alertEl = document.createElement('div');
    alertEl.className = 'attack-alert';
    alertEl.innerHTML = `⚠️ Ditembak ${attackType.toUpperCase()} oleh ${attackerName} selama ${Math.round(duration/1000)}s!`;
    document.body.appendChild(alertEl);

    if(attackType === 'freeze') {
        body.classList.add('effect-freeze');
        setTimeout(() => { body.classList.remove('effect-freeze'); alertEl.remove(); }, duration);
    } else if(attackType === 'glitch') {
        body.classList.add('effect-glitch');
        setTimeout(() => { body.classList.remove('effect-glitch'); alertEl.remove(); }, duration);
    } else if(attackType === 'blind') {
        body.classList.add('effect-blind');
        setTimeout(() => { body.classList.remove('effect-blind'); alertEl.remove(); }, duration);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    updateGamificationUI();
    renderQuestion(currentIdx);
    startTimer();
});
</script>
</body>
</html>
