<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $quiz->title }} – Bergabung ke kuis">
    <title>{{ $quiz->title }} – Bergabung</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root { --purple:#7C3AED; --indigo:#4F46E5; }
        * { box-sizing:border-box; margin:0; padding:0; }
        body {
            font-family:'Plus Jakarta Sans',sans-serif;
            background: linear-gradient(135deg, #F0EFFF 0%, #EAEAFF 100%);
            color: #1E1B4B; min-height:100vh;
            display:flex; align-items:center; justify-content:center;
            padding:24px;
        }

        .join-wrapper {
            position:relative; z-index:1;
            display:grid; grid-template-columns:1fr 1fr; gap:32px;
            max-width:1000px; width:100%; align-items:center;
        }

        /* LEFT: INFO */
        .join-left {
            padding:20px; display:flex; flex-direction:column; justify-content:center;
            animation:fadeLeft .7s ease;
        }
        .quiz-badge {
            display:inline-flex; align-items:center; gap:8px;
            background:rgba(124,58,237,0.12); border:1px solid rgba(124,58,237,0.2);
            border-radius:10px; padding:7px 14px; font-size:11px; font-weight:800;
            color:#7C3AED; text-transform:uppercase; letter-spacing:.12em;
            margin-bottom:24px; width:fit-content;
        }
        .join-title { font-size:32px; font-weight:900; color:#1E1B4B; line-height:1.2; margin-bottom:16px; }
        .join-desc  { font-size:14px; color:#6B7280; line-height:1.7; font-weight:600; margin-bottom:36px; max-width:90%; }
        .quiz-meta-grid { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
        .meta-card {
            background:#ffffff; border:1px solid #E5E3F0; box-shadow:0 4px 16px rgba(0,0,0,0.03);
            border-radius:18px; padding:18px 20px;
        }
        .meta-card-label { font-size:11px; font-weight:800; color:#9CA3AF; text-transform:uppercase; letter-spacing:.12em; margin-bottom:6px; }
        .meta-card-value { font-size:24px; font-weight:900; color:#1E1B4B; }
        .meta-card-unit  { font-size:12px; color:#6B7280; font-weight:600; }

        /* RIGHT: FORM */
        .join-right { 
            background:#ffffff; border-radius:32px; padding:48px 40px; 
            animation:fadeUp .5s ease; box-shadow:0 24px 48px rgba(0,0,0,0.05);
            border:1px solid #ffffff;
        }
        .logo-row { display:flex; align-items:center; gap:12px; margin-bottom:36px; }
        .logo-icon {
            width:40px; height:40px; border-radius:12px;
            background:linear-gradient(135deg,#7C3AED,#4F46E5);
            display:flex; align-items:center; justify-content:center;
            font-size:18px; font-weight:900; font-style:italic; color:#fff;
            box-shadow:0 4px 14px rgba(124,58,237,0.3);
        }
        .logo-text h1 { font-size:18px; font-weight:800; color:#1E1B4B; margin:0;}
        .logo-text p  { font-size:10px; font-weight:800; color:#9CA3AF; text-transform:uppercase; letter-spacing:.15em; margin:0;}

        .form-head { margin-bottom:28px; }
        .form-head h2 { font-size:24px; font-weight:900; color:#1E1B4B; margin-bottom:6px; }
        .form-head p  { font-size:13px; color:#6B7280; font-weight:600; }

        .alert {
            border-radius:12px; padding:12px 16px; font-size:13px; font-weight:700;
            margin-bottom:18px; display:flex; align-items:center; gap:10px;
        }
        .alert-err { background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.25); color:#DC2626; }

        .field-group { margin-bottom:20px; }
        .field-label {
            display:block; font-size:11px; font-weight:800; text-transform:uppercase;
            letter-spacing:.15em; color:#6B7280; margin-bottom:9px;
        }
        .field-wrap { position:relative; }
        .field-wrap i { position:absolute; left:16px; top:50%; transform:translateY(-50%); color:#9CA3AF; font-size:14px; }
        .field-input {
            width:100%; background:#F9F8FD; border:1px solid #E5E3F0;
            border-radius:14px; padding:15px 16px 15px 44px; color:#1E1B4B;
            font-size:14px; font-weight:600; font-family:inherit; outline:none; transition:all .2s;
        }
        .field-input:focus { background:#fff; border-color:#7C3AED; box-shadow:0 0 0 4px rgba(124,58,237,0.12); }
        .field-input::placeholder { color:#9CA3AF; font-weight:500;}

        .btn-join {
            width:100%; padding:16px; border-radius:14px;
            background:linear-gradient(135deg,#7C3AED,#4F46E5);
            color:#fff; font-size:15px; font-weight:800;
            border:none; cursor:pointer; font-family:inherit;
            transition:all .2s; box-shadow:0 8px 24px rgba(124,58,237,0.3);
            display:flex; align-items:center; justify-content:center; gap:10px; margin-top:8px;
        }
        .btn-join:hover { box-shadow:0 12px 32px rgba(124,58,237,0.4); transform:translateY(-2px); }

        @keyframes fadeUp   { from{opacity:0;transform:translateY(20px);} to{opacity:1;transform:translateY(0);} }
        @keyframes fadeLeft { from{opacity:0;transform:translateX(-20px);} to{opacity:1;transform:translateX(0);} }

        @media (max-width:850px) {
            .join-wrapper { grid-template-columns:1fr; max-width:500px; gap:20px; }
            .join-left    { display:none; }
            .join-right   { padding:40px 32px; }
        }
    </style>
</head>
<body>

<div class="join-wrapper">
    <!-- LEFT: Quiz Info -->
    <div class="join-left">
        <div class="quiz-badge">
            <i class="fa-solid fa-clipboard-list"></i> Assessment
        </div>
        <h1 class="join-title">{{ $quiz->title }}</h1>
        <p class="join-desc">
            Kerjakan kuis ini dengan jujur sesuai kemampuan Anda.
            Jawaban akan direkam dan dinilai secara otomatis.
        </p>
        <div class="quiz-meta-grid">
            <div class="meta-card">
                <div class="meta-card-label">Durasi</div>
                <div class="meta-card-value">{{ $quiz->time_limit }} <span class="meta-card-unit">mnt</span></div>
            </div>
            <div class="meta-card">
                <div class="meta-card-label">Passing</div>
                <div class="meta-card-value">{{ $quiz->passing_score }}<span class="meta-card-unit">%</span></div>
            </div>
            <div class="meta-card">
                <div class="meta-card-label">Total Soal</div>
                <div class="meta-card-value">{{ $quiz->questions_count ?? $quiz->questions->count() }} <span class="meta-card-unit">soal</span></div>
            </div>
            <div class="meta-card">
                <div class="meta-card-label">Tipe Kuis</div>
                <div class="meta-card-value" style="font-size:16px; margin-top:4px;">Pilihan Ganda</div>
            </div>
        </div>
    </div>

    <!-- RIGHT: Join Form -->
    <div class="join-right">
        <div class="logo-row">
            <div class="logo-icon">P</div>
            <div class="logo-text">
                <h1>Paham<span style="color:#7C3AED">Aja</span></h1>
                <p>Assessment Platform</p>
            </div>
        </div>

        <div class="form-head">
            <h2>Masuk ke Kuis</h2>
            <p>Perkenalkan diri Anda untuk memulai</p>
        </div>

        @if(session('error'))
        <div class="alert alert-err"><i class="fa-solid fa-triangle-exclamation"></i>{{ session('error') }}</div>
        @endif
        @if($errors->any())
        <div class="alert alert-err"><i class="fa-solid fa-circle-xmark"></i>{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('quiz.join.process', $quiz->slug) }}" id="joinForm">
            @csrf

            <div class="field-group">
                <label class="field-label" for="nim">NIK</label>
                <div class="field-wrap">
                    <i class="fa-solid fa-id-badge"></i>
                    <input class="field-input" type="text" id="nim" name="nim"
                           value="{{ old('nim') }}" required placeholder="Ketik NIK Anda" autofocus>
                </div>
            </div>

            @if($quiz->is_public)
            <div class="field-group">
                <label class="field-label" for="name">Nama Lengkap</label>
                <div class="field-wrap">
                    <i class="fa-solid fa-user"></i>
                    <input class="field-input" type="text" id="name" name="name"
                           value="{{ old('name') }}" required placeholder="Ketik nama lengkap Anda">
                </div>
            </div>

            <div class="field-group">
                <label class="field-label" for="location">Lokasi</label>
                <div class="field-wrap">
                    <i class="fa-solid fa-location-dot"></i>
                    <input class="field-input" type="text" id="location" name="location"
                           value="{{ old('location') }}" required placeholder="Ketik lokasi Anda">
                </div>
            </div>
            @endif

            @if(isset($sessions) && $sessions->count() > 0)
            <div class="field-group">
                <label class="field-label" for="quiz_session_id">Sesi Pelaksanaan</label>
                <div class="field-wrap">
                    <i class="fa-solid fa-layer-group"></i>
                    <select id="quiz_session_id" name="quiz_session_id"
                            class="field-input" style="padding-left:44px; appearance:none; cursor:pointer;">
                        <option value="">Pilih jadawal sesi Anda...</option>
                        @foreach($sessions as $session)
                        <option value="{{ $session->id }}" {{ old('quiz_session_id') == $session->id ? 'selected' : '' }}>
                            {{ $session->name }}
                        </option>
                        @endforeach
                    </select>
                    <i class="fa-solid fa-chevron-down" style="left:auto; right:16px; font-size:11px; pointer-events:none;"></i>
                </div>
            </div>
            @endif

            <button type="submit" class="btn-join" id="joinBtn">
                @if($quiz->is_public)
                <i class="fa-solid fa-person-walking-arrow-right"></i> Masuk Ruang Tunggu
                @else
                <i class="fa-solid fa-play"></i> Mulai Kuis Sekarang
                @endif
            </button>
        </form>
    </div>
</div>

<script src="{{ asset('js/prevent-double-submit.js') }}"></script>
</body>
</html>
