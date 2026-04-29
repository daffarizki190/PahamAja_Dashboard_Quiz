<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Konfirmasi Profil – {{ $quiz->title }}">
    <title>Konfirmasi Profil – {{ $quiz->title }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root { --purple:#7C3AED; --indigo:#4F46E5; --green:#10B981; }
        * { box-sizing:border-box; margin:0; padding:0; }
        body {
            font-family:'Plus Jakarta Sans',sans-serif;
            background: linear-gradient(135deg, #F0EFFF 0%, #EAEAFF 100%);
            color: #1E1B4B; min-height:100vh;
            display:flex; align-items:center; justify-content:center;
            padding:24px;
        }

        .confirm-card {
            background:#ffffff; border-radius:32px; padding:48px 40px; 
            max-width:500px; width:100%; text-align:center;
            animation:fadeUp .5s ease; box-shadow:0 30px 60px rgba(0,0,0,0.08);
            border:1px solid #ffffff;
        }

        .profile-section { margin-bottom:32px; position:relative; display:inline-block; }
        .avatar-main {
            width:120px; height:120px; border-radius:40px;
            background:linear-gradient(135deg,#7C3AED,#4F46E5);
            display:flex; align-items:center; justify-content:center;
            font-size:44px; font-weight:800; color:#fff;
            margin:0 auto; box-shadow:0 12px 32px rgba(124,58,237,0.25);
            object-fit:cover; border:4px solid #fff;
        }
        .check-badge {
            position:absolute; bottom:-5px; right:-5px;
            width:36px; height:36px; border-radius:50%;
            background:#10B981; color:#fff; border:3px solid #fff;
            display:flex; align-items:center; justify-content:center; font-size:16px;
            box-shadow:0 4px 10px rgba(16,185,129,0.3);
        }

        .employee-name { font-size:24px; font-weight:900; color:#1E1B4B; margin-bottom:6px; }
        .employee-id   { font-size:14px; font-weight:700; color:#9CA3AF; margin-bottom:24px; letter-spacing:.02em; }

        .info-grid {
            display:grid; grid-template-columns:1fr 1fr; gap:12px;
            margin-bottom:36px; text-align:left;
        }
        .info-item {
            background:#F9F8FD; border:1px solid #F0EFFF;
            border-radius:16px; padding:12px 16px;
        }
        .info-label { font-size:10px; font-weight:800; color:#9CA3AF; text-transform:uppercase; letter-spacing:.12em; margin-bottom:4px; }
        .info-val   { font-size:13px; font-weight:700; color:#4E4C6A; }

        .ready-text { font-size:16px; font-weight:800; color:#1E1B4B; margin-bottom:28px; }

        .btn-start {
            width:100%; padding:18px; border-radius:16px;
            background:linear-gradient(135deg,#10B981,#059669);
            color:#fff; font-size:16px; font-weight:800;
            border:none; cursor:pointer; font-family:inherit;
            transition:all .3s; box-shadow:0 12px 28px rgba(16,185,129,0.25);
            display:flex; align-items:center; justify-content:center; gap:12px;
        }
        .btn-start:hover { transform:translateY(-3px); box-shadow:0 18px 40px rgba(16,185,129,0.35); }
        .btn-start:active { transform:translateY(-1px); }

        .btn-back {
            display:inline-block; margin-top:24px; font-size:13px; font-weight:700;
            color:#9CA3AF; text-decoration:none; transition:color .2s;
        }
        .btn-back:hover { color:#7C3AED; }

        @keyframes fadeUp { from{opacity:0;transform:translateY(20px);} to{opacity:1;transform:translateY(0);} }

        /* 3D Enhancements */
        .avatar-container {
            perspective: 1000px;
            display: inline-block;
        }
    </style>
</head>
<body>

<div class="confirm-card">
    <div class="profile-section avatar-container" data-tilt data-tilt-max="20" data-tilt-speed="400" data-tilt-perspective="1000">
        
            <img src="{{ avatar_url($employee->avatar, $employee->name) }}" class="avatar-main">
        
        <div class="check-badge"><i class="fa-solid fa-check"></i></div>
    </div>

    <h2 class="employee-name">{{ $employee->name }}</h2>
    <div class="employee-id">NIK: {{ $employee->nim }}</div>

    <div class="info-grid">
        <div class="info-item">
            <div class="info-label">Posisi</div>
            <div class="info-val">{{ $employee->position ?? '-' }}</div>
        </div>
        <div class="info-item">
            <div class="info-label">Departemen</div>
            <div class="info-val">{{ $employee->department ?? '-' }}</div>
        </div>
    </div>

    <div class="ready-text">Sudah siap untuk mengerjakan?</div>

    <form action="{{ route('quiz.start', ['quiz' => $quiz->slug, 'employee' => $employee->id]) }}" method="POST">
        @csrf
        <button type="submit" class="btn-start">
            <i class="fa-solid fa-rocket"></i> YA, MULAI SEKARANG
        </button>
    </form>

    <a href="{{ route('quiz.join', $quiz->slug) }}" class="btn-back">
        <i class="fa-solid fa-arrow-left" style="margin-right:6px;"></i> Bukan saya? Ganti akun
    </a>
</div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/vanilla-tilt/1.8.1/vanilla-tilt.min.js"></script>
</body>
</html>
