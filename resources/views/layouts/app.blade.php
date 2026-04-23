<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta_description', 'PahamAja – Platform Quiz & Assessment Profesional')">
    <title>@yield('title', 'PahamAja')</title>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script>
        // Dark mode init
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>
    <style>
        :root {
            --bg-base:      #EEECf5;
            --bg-card:      #FFFFFF;
            --border:       #E8E6F0;
            --purple:       #7C3AED;
            --purple-mid:   #6D28D9;
            --indigo:       #4F46E5;
            --text-primary: #1E1B4B;
            --text-muted:   #6B7280;
            --text-dim:     #9CA3AF;
            --sidebar-bg:   #2D2B6B;
            --sidebar-w:    200px;
        }
        html.dark {
            --bg-base:      #0F172A;
            --bg-card:      #1E293B;
            --border:       #334155;
            --purple:       #8B5CF6;
            --purple-mid:   #7C3AED;
            --indigo:       #6366F1;
            --text-primary: #F8FAFC;
            --text-muted:   #94A3B8;
            --text-dim:     #64748B;
            --sidebar-bg:   #0B1120;
        }
        * { box-sizing: border-box; }
        html, body { height: 100%; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg-base);
            color: var(--text-primary);
            min-height: 100vh;
        }
        ::-webkit-scrollbar { width: 4px; height: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(124,58,237,0.2); border-radius: 99px; }

        /* ── SIDEBAR ── */
        .sidebar {
            position: fixed; top: 0; left: 0; bottom: 0;
            width: var(--sidebar-w);
            background: var(--sidebar-bg);
            display: flex; flex-direction: column;
            z-index: 50;
            transition: transform 0.3s cubic-bezier(0.4,0,0.2,1);
        }

        /* Logo area */
        .sidebar-logo {
            display: flex; flex-direction: column; align-items: center;
            padding: 28px 20px 24px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }
        .sidebar-logo-icon {
            width: 52px; height: 52px;
            background: linear-gradient(135deg, #7C3AED, #4F46E5);
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 24px; font-weight: 900; font-style: italic; color: #fff;
            box-shadow: 0 4px 16px rgba(124,58,237,0.5);
            margin-bottom: 10px;
        }
        .sidebar-logo-name { font-size: 15px; font-weight: 800; color: #fff; }
        .sidebar-logo-name span { color: #A78BFA; }

        /* Nav */
        .sidebar-nav { flex: 1; overflow-y: auto; padding: 14px 10px; display: flex; flex-direction: column; gap: 3px; }
        .nav-item {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 14px; border-radius: 10px; cursor: pointer;
            color: rgba(255,255,255,0.5); font-size: 13px; font-weight: 600;
            text-decoration: none; transition: all 0.18s;
        }
        .nav-item:hover { background: rgba(255,255,255,0.07); color: rgba(255,255,255,0.85); transform: translateX(4px); }
        .nav-item.active {
            background: rgba(124,58,237,0.35);
            color: #fff; font-weight: 700;
        }
        .nav-item i { width: 18px; text-align: center; font-size: 15px; flex-shrink: 0; }

        /* Bottom profile */
        .sidebar-profile {
            padding: 14px 10px;
            border-top: 1px solid rgba(255,255,255,0.08);
        }
        .profile-row {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 12px; border-radius: 10px; cursor: pointer;
            transition: background 0.18s;
        }
        .profile-row:hover { background: rgba(255,255,255,0.07); }
        .profile-avatar {
            width: 32px; height: 32px; border-radius: 50%;
            background: linear-gradient(135deg, #7C3AED, #4F46E5);
            display: flex; align-items: center; justify-content: center;
            font-size: 12px; font-weight: 800; color: #fff; flex-shrink: 0;
        }
        .profile-name { font-size: 13px; font-weight: 700; color: #fff; flex: 1; }
        .profile-row i { color: rgba(255,255,255,0.4); font-size: 11px; transition: color 0.18s; }
        .profile-row:hover .logout-icon { color: #ef4444 !important; transform: scale(1.1); } /* Merah saat hover */

        /* ── TOPBAR ── */
        .topbar {
            position: fixed; top: 0; right: 0;
            left: var(--sidebar-w);
            height: 66px;
            background: var(--bg-base);
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 28px; z-index: 40;
            transition: left 0.3s;
        }
        .topbar-left { display: flex; align-items: center; gap: 16px; }
        .topbar-right { display: flex; align-items: center; gap: 10px; }

        /* Logo text in topbar */
        .topbar-brand {
            font-size: 22px; font-weight: 900; color: var(--text-primary);
        }
        .topbar-brand span { color: var(--purple); }

        /* Search bar */
        .topbar-search {
            display: flex; align-items: center; gap: 9px;
            background: var(--bg-card); border: 1px solid var(--border);
            border-radius: 12px; padding: 8px 16px;
            min-width: 280px;
        }
        .topbar-search i { color: var(--text-dim); font-size: 13px; }
        .topbar-search input {
            background: transparent; border: none; outline: none;
            font-family: inherit; font-size: 13px; font-weight: 500;
            color: var(--text-primary); width: 100%;
        }
        .topbar-search input::placeholder { color: var(--text-dim); }

        /* Topbar icon buttons */
        .topbar-icon-btn {
            width: 38px; height: 38px; border-radius: 10px;
            background: var(--bg-card); border: 1px solid var(--border);
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; position: relative; transition: all 0.18s;
            color: var(--text-muted); font-size: 14px;
        }
        .topbar-icon-btn:hover { border-color: rgba(124,58,237,0.3); color: var(--purple); transform: scale(1.05); }
        .topbar-avatar {
            width: 38px; height: 38px; border-radius: 50%;
            background: linear-gradient(135deg,#7C3AED,#4F46E5);
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; font-weight: 800; color: #fff; cursor: pointer;
        }

        /* ── MAIN CONTENT ── */
        .main-content {
            margin-left: var(--sidebar-w);
            margin-top: 66px;
            min-height: calc(100vh - 66px);
            padding: 24px 28px;
            transition: margin-left 0.3s;
        }

        /* ── CARD ── */
        .card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 16px;
            transition: all 0.2s;
            box-shadow: 0 1px 4px rgba(0,0,0,0.04);
        }
        .card:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.07); transform: translateY(-2px); }

        /* ── BUTTONS ── */
        .btn {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 9px 18px; border-radius: 10px;
            font-size: 13px; font-weight: 700; cursor: pointer;
            transition: all 0.18s; border: none; text-decoration: none;
            white-space: nowrap; font-family: inherit;
        }
        .btn-primary {
            background: linear-gradient(135deg, #7C3AED, #4F46E5);
            color: #fff; box-shadow: 0 2px 10px rgba(124,58,237,0.3);
        }
        .btn-primary:hover { box-shadow: 0 4px 18px rgba(124,58,237,0.45); transform: translateY(-1px); }
        .btn-ghost {
            background: var(--bg-card); color: var(--text-muted);
            border: 1px solid var(--border);
        }
        .btn-ghost:hover { background: var(--bg-base); color: var(--text-primary); }
        .btn-danger { background: rgba(239,68,68,0.08); color: #DC2626; border: 1px solid rgba(239,68,68,0.2); }
        .btn-danger:hover { background: rgba(239,68,68,0.14); }

        /* ── STAT CARD ── */
        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 24px 28px;
            display: flex; align-items: center; justify-content: space-between;
            box-shadow: 0 1px 4px rgba(0,0,0,0.04);
            transition: all 0.2s;
        }
        .stat-card:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,0.08); }
        .stat-card-value { font-size: 36px; font-weight: 900; color: var(--text-primary); }
        .stat-card-label { font-size: 13px; font-weight: 600; color: var(--text-muted); margin-bottom: 6px; }
        .stat-card-icon { font-size: 52px; flex-shrink: 0; }

        /* ── TABLE ── */
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table thead th {
            padding: 11px 20px; text-align: left;
            font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .1em;
            color: var(--text-muted); background: rgba(0,0,0,0.02);
            border-bottom: 1px solid var(--border);
        }
        .data-table tbody tr { border-bottom: 1px solid var(--border); transition: background 0.15s; }
        .data-table tbody tr:hover { background: rgba(0,0,0,0.02); }
        .data-table tbody td { padding: 13px 20px; font-size: 13px; color: var(--text-primary); }

        /* ── BADGE ── */
        .badge {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 3px 10px; border-radius: 6px;
            font-size: 11px; font-weight: 700;
        }
        .badge-green  { background: rgba(16,185,129,0.1); color: #059669; border: 1px solid rgba(16,185,129,0.2); }
        .badge-red    { background: rgba(239,68,68,0.08); color: #DC2626; border: 1px solid rgba(239,68,68,0.15); }
        .badge-yellow { background: rgba(245,158,11,0.1); color: #D97706; border: 1px solid rgba(245,158,11,0.2); }
        .badge-purple { background: rgba(124,58,237,0.1); color: #7C3AED; border: 1px solid rgba(124,58,237,0.2); }
        .badge-blue   { background: rgba(59,130,246,0.08); color: #2563EB; border: 1px solid rgba(59,130,246,0.15); }
        .badge-aktif  { background: rgba(124,58,237,0.12); color: #7C3AED; border-radius: 20px; padding: 4px 14px; font-weight: 800; }
        .badge-selesai { background: rgba(16,185,129,0.12); color: #059669; border-radius: 20px; padding: 4px 14px; font-weight: 800; }

        /* ── INPUT ── */
        .form-input {
            width: 100%; background: var(--bg-card); border: 1px solid var(--border);
            border-radius: 10px; padding: 10px 14px; color: var(--text-primary); font-size: 14px; font-weight: 500;
            font-family: inherit; transition: all 0.18s; outline: none;
        }
        .form-input:focus { border-color: var(--purple); box-shadow: 0 0 0 3px rgba(124,58,237,0.1); }
        .form-input::placeholder { color: var(--text-dim); }
        .form-label { display: block; font-size: 12px; font-weight: 700; color: var(--text-primary); margin-bottom: 6px; }
        select.form-input option { background: #fff; color: var(--text-primary); }
        textarea.form-input { resize: vertical; min-height: 100px; }

        /* ── ANIMATIONS ── */
        @keyframes fadeUp { from { opacity:0; transform:translateY(16px); } to { opacity:1; transform:translateY(0); } }
        @keyframes spin   { to { transform: rotate(360deg); } }
        @keyframes pulse  { 0%,100% { opacity:1; } 50% { opacity:.5; } }
        .fade-up { animation: fadeUp 0.5s cubic-bezier(0.16,1,0.3,1) forwards; opacity:0; }
        .delay-1 { animation-delay:.08s; } .delay-2 { animation-delay:.16s; }
        .delay-3 { animation-delay:.24s; } .delay-4 { animation-delay:.32s; }

        /* ── APPLE DOTS WAVE LOADER ── */
        @keyframes dotsWave {
            0%, 60%, 100% { transform: translateY(0) scale(1);   opacity: 0.35; }
            30%            { transform: translateY(-9px) scale(1.15); opacity: 1;    }
        }
        .dots-wave {
            display: inline-flex; align-items: center; gap: 5px;
            vertical-align: middle;
        }
        .dots-wave span {
            display: inline-block;
            width: 7px; height: 7px;
            border-radius: 50%;
            background: currentColor;
            animation: dotsWave 1.2s ease-in-out infinite;
        }
        .dots-wave span:nth-child(1) { animation-delay: 0s; }
        .dots-wave span:nth-child(2) { animation-delay: 0.15s; }
        .dots-wave span:nth-child(3) { animation-delay: 0.3s; }
        .dots-wave span:nth-child(4) { animation-delay: 0.45s; }
        /* Size variants */
        .dots-wave.sm span { width: 5px; height: 5px; gap: 3px; }
        .dots-wave.lg span { width: 10px; height: 10px; }
        /* Color variants */
        .dots-wave.purple  { color: #7C3AED; }
        .dots-wave.white   { color: #fff; }
        .dots-wave.muted   { color: #9CA3AF; }

        /* ── PROGRESS BAR ── */
        .progress-bar-track { height: 6px; background: #E5E3F0; border-radius: 99px; overflow: hidden; }
        .progress-bar-fill  { height: 100%; border-radius: 99px; transition: width 1s cubic-bezier(0.4,0,0.2,1); }

        /* ── GRID ── */
        .grid { display: grid; }
        .grid-cols-3 { grid-template-columns: repeat(3, 1fr); }
        .grid-cols-4 { grid-template-columns: repeat(4, 1fr); }
        @media (max-width:1100px) { .grid-cols-4 { grid-template-columns: repeat(2,1fr) !important; } }
        @media (max-width:900px)  { .grid-cols-3 { grid-template-columns: 1fr !important; } }
        .gap-4 { gap: 14px; } .gap-5 { gap: 18px; } .gap-6 { gap: 22px; }
        .mb-5 { margin-bottom: 18px; } .mb-6 { margin-bottom: 22px; }
        .space-y-4 > * + * { margin-top: 16px; }

        /* ── MODAL ── */
        .modal-overlay { display:none; position:fixed; inset:0; z-index:9999; background:rgba(30,27,75,0.45); backdrop-filter:blur(6px); align-items:center; justify-content:center; padding:20px; }
        .modal-overlay.open { display:flex; }
        .modal-box {
            background: var(--bg-card); border: 1px solid var(--border);
            border-radius: 20px; padding: 28px; width: 100%; max-width: 520px;
            max-height: 90vh; overflow-y: auto;
            animation: fadeUp 0.3s ease forwards;
            box-shadow: 0 20px 60px rgba(30,27,75,0.15);
        }

        /* ── RANGE SLIDER ── */
        input[type=range] {
            -webkit-appearance: none; width: 100%; height: 5px;
            background: #E5E3F0; border-radius: 99px; outline: none;
        }
        input[type=range]::-webkit-slider-thumb {
            -webkit-appearance: none; width: 17px; height: 17px;
            background: var(--purple); border-radius: 50%; cursor: pointer;
            box-shadow: 0 2px 8px rgba(124,58,237,0.4);
        }

        /* ── MOBILE ── */
        .sidebar-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.35); z-index:49; }
        @media (max-width:1024px) {
            .sidebar { transform: translateX(-100%); width: 260px; }
            .sidebar.open { transform: translateX(0); box-shadow: 20px 0 60px rgba(0,0,0,0.15); }
            .topbar { left:0; padding: 0 16px; }
            .topbar-right { gap: 8px; }
            #menuToggle { display: flex !important; }
            .main-content { margin-left:0; padding: 20px 16px; }
            .sidebar-overlay.open { display:block; }
            .topbar-search { max-width: 260px; min-width: 0; flex: 1; }
        }
        @media (max-width:640px) {
            .topbar-search { display:none; }
            .topbar-brand { font-size: 18px; }
            .topbar-brand span { display: none; }
            .topbar-icon-btn { display: none; }
        }
        /* ── CUSTOM NOTIFICATIONS ── */
        .pa-toast-container { position: fixed; top: 24px; right: 24px; z-index: 99999; display: flex; flex-direction: column; gap: 10px; pointer-events: none; }
        .pa-toast {
            pointer-events: auto; padding: 12px 20px; border-radius: 12px; background: var(--bg-card); border: 1px solid var(--border);
            box-shadow: 0 10px 25px rgba(30,27,75,0.12); display: flex; align-items: center; gap: 12px;
            font-size: 13px; font-weight: 700; color: var(--text-primary); animation: toastIn 0.4s cubic-bezier(0.16, 1, 0.3, 1); min-width: 280px;
        }
        @keyframes toastIn { from { transform: translateX(40px); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        .pa-toast.success i { color: #10B981; }
        .pa-toast.error i   { color: #EF4444; }

        .pa-modal-overlay { position: fixed; inset: 0; background: rgba(30,27,75,0.6); backdrop-filter: blur(8px); display: none; align-items: center; justify-content: center; z-index: 999999; padding: 20px; }
        .pa-modal-overlay.open { display: flex; }
        .pa-modal {
            background: var(--bg-card); border-radius: 24px; padding: 32px; width: 100%; max-width: 440px;
            box-shadow: 0 30px 70px rgba(0,0,0,0.2); animation: modalIn 0.35s cubic-bezier(0.34, 1.56, 0.64, 1); text-align: center;
        }
        @keyframes modalIn { from { transform: scale(0.9) translateY(20px); opacity: 0; } to { transform: scale(1) translateY(0); opacity: 1; } }
        .pa-modal-icon { width: 64px; height: 64px; border-radius: 20px; display: flex; align-items: center; justify-content: center; font-size: 28px; margin: 0 auto 20px; }
        .pa-modal.warning .pa-modal-icon { background: rgba(245,158,11,0.12); color: #F59E0B; }
        .pa-modal.danger .pa-modal-icon, .pa-modal.logout .pa-modal-icon { background: rgba(239,68,68,0.12); color: #EF4444; }
        .pa-modal.success .pa-modal-icon { background: rgba(16,185,129,0.12); color: #10B981; }
        .pa-modal-title { font-size: 20px; font-weight: 900; color: var(--text-primary); margin-bottom: 12px; }
        .pa-modal-text { font-size: 14px; color: var(--text-muted); line-height: 1.6; margin-bottom: 28px; }

        /* ── 3D ENHANCEMENTS ── */
        .card-3d {
            transform-style: preserve-3d;
            perspective: 1000px;
        }
        .img-3d {
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(30,27,75,0.1), 0 1px 8px rgba(30,27,75,0.05);
            transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            transform-style: preserve-3d;
        }
        .img-3d:hover {
            transform: translateZ(20px);
        }
        .float-3d {
            animation: float3d 4s ease-in-out infinite;
        }
        @keyframes float3d {
            0%, 100% { transform: translateY(0) rotateX(0) rotateY(0); }
            50% { transform: translateY(-15px) rotateX(5deg) rotateY(5deg); }
        }
        .depth-shadow {
            filter: drop-shadow(0 15px 35px rgba(30,27,75,0.25));
        }
    </style>
    @yield('head_extra')
</head>
<body>

    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

    <!-- Sidebar -->
    <aside class="sidebar" id="mainSidebar">
        <!-- Logo -->
        <div class="sidebar-logo">
            <div class="sidebar-logo-icon">P</div>
            <div class="sidebar-logo-name">Paham<span>Aja</span></div>
        </div>

        <!-- Nav -->
        <nav class="sidebar-nav">
            <a href="{{ route('admin.quizzes.index') }}"
               class="nav-item {{ request()->routeIs('admin.quizzes.index') || request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-grid-2"></i> Dashboard
            </a>
            <a href="{{ route('admin.employees.index') }}"
               class="nav-item {{ request()->routeIs('admin.employees.*') ? 'active' : '' }}">
                <i class="fa-solid fa-users"></i> Karyawan
            </a>
            <a href="{{ route('admin.quizzes.create') }}"
               class="nav-item {{ request()->routeIs('admin.quizzes.create','admin.quizzes.ai-create','admin.quizzes.import','admin.quizzes.show','admin.quizzes.edit') ? 'active' : '' }}">
                <i class="fa-solid fa-clipboard-list"></i> Kuis
            </a>
            <a href="{{ route('admin.reports.index') }}"
               class="nav-item {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-bar"></i> Laporan
            </a>
            <a href="{{ route('admin.settings.index') }}" 
               class="nav-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                <i class="fa-solid fa-gear"></i> Pengaturan
            </a>
        </nav>

        <!-- Profile (bottom) -->
        <div class="sidebar-profile">
            <form action="{{ route('admin.logout') }}" method="POST" id="logoutForm">
                @csrf
            </form>
            <div class="profile-row" onclick="document.getElementById('logoutForm').submit()" title="Keluar / Log Out">
                <div class="profile-avatar">@auth {{ strtoupper(substr(auth()->user()->name, 0, 1)) }} @else A @endauth</div>
                <div class="profile-name">@auth {{ auth()->user()->name }} @else Admin @endauth</div>
                <i class="fa-solid fa-right-from-bracket logout-icon" style="font-size: 14px; color: rgba(255,255,255,0.7);"></i>
            </div>
        </div>
    </aside>

    <!-- Topbar -->
    <header class="topbar">
        <div class="topbar-left">
            <button onclick="toggleSidebar()" id="menuToggle"
                    style="width:38px;height:38px;border-radius:10px;background:#fff;border:1px solid #E8E6F0;display:none;align-items:center;justify-content:center;cursor:pointer;color:#6B7280;">
                <i class="fa-solid fa-bars" style="font-size:14px;"></i>
            </button>
            @yield('topbar_left')
            <!-- Brand name -->
            <div class="topbar-brand">Paham<span>Aja</span></div>
        </div>

        <!-- Search bar (center) -->
        @hasSection('show_search')
        <div class="topbar-search" style="position:absolute;left:50%;transform:translateX(-50%);">
            <i class="fa-solid fa-search"></i>
            <input type="text" id="globalSearch" placeholder="@yield('search_placeholder', 'Cari kuis atau karyawan...')" 
                   oninput="window.dispatchEvent(new CustomEvent('pahamaja-search', { detail: this.value }))">
        </div>
        @endif

        <div class="topbar-right">
            @yield('topbar_actions')
            <div class="topbar-icon-btn" onclick="toggleDarkMode()" title="Mode Gelap/Terang">
                <i class="fa-solid fa-moon" id="themeIcon"></i>
            </div>
            <div class="topbar-icon-btn" onclick="PahamAja.alert('Notifikasi', 'Belum ada notifikasi baru untuk saat ini.', 'success')">
                <i class="fa-regular fa-bell"></i>
            </div>
            <div class="topbar-avatar" onclick="PahamAja.confirm('Keluar Sistem', 'Apakah Anda yakin ingin keluar dari dashboard admin?', 'logout', () => document.getElementById('logoutForm').submit())" title="Profil & Keluar">
                @auth {{ strtoupper(substr(auth()->user()->name, 0, 1)) }} @else A @endauth
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        @yield('content')
    </main>

    <!-- Global Toast/Modal Containers -->
    <div class="pa-toast-container" id="paToastContainer"></div>
    <div class="pa-modal-overlay" id="paModalOverlay" onclick="PahamAja.closeModal()">
        <div class="pa-modal" id="paModal" onclick="event.stopPropagation()">
            <div class="pa-modal-icon" id="paModalIcon"><i class="fa-solid fa-bell"></i></div>
            <div class="pa-modal-title" id="paModalTitle">Pemberitahuan</div>
            <div class="pa-modal-text" id="paModalText">Konten pesan...</div>
            <div id="paModalActions" style="display:flex; gap:12px; margin-top:10px;">
                <button id="paModalClose" class="btn btn-primary" style="flex:1; justify-content:center; padding:14px;" onclick="PahamAja.closeModal()">Mengerti</button>
            </div>
            <div id="paConfirmActions" style="display:none; gap:12px; margin-top:10px;">
                <button class="btn btn-ghost" style="flex:1; justify-content:center; padding:14px;" onclick="PahamAja.closeModal()">Batal</button>
                <button id="paConfirmBtn" class="btn btn-primary" style="flex:1; justify-content:center; padding:14px; background:linear-gradient(135deg,#EF4444,#B91C1C);">Ya, Lanjutkan</button>
            </div>
        </div>
    </div>


    <script>
        function toggleSidebar() {
            document.getElementById('mainSidebar').classList.toggle('open');
            document.getElementById('sidebarOverlay').classList.toggle('open');
        }
        function closeSidebar() {
            document.getElementById('mainSidebar').classList.remove('open');
            document.getElementById('sidebarOverlay').classList.remove('open');
        }
        function checkWidth() {
            const btn = document.getElementById('menuToggle');
            if (btn) btn.style.display = window.innerWidth <= 1024 ? 'flex' : 'none';
        }
        checkWidth();
        window.addEventListener('resize', checkWidth);

        // Dark Mode Logic
        function toggleDarkMode() {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.theme = 'light';
                document.getElementById('themeIcon').className = 'fa-solid fa-moon';
            } else {
                document.documentElement.classList.add('dark');
                localStorage.theme = 'dark';
                document.getElementById('themeIcon').className = 'fa-solid fa-sun';
            }
        }
        document.addEventListener('DOMContentLoaded', () => {
            const icon = document.getElementById('themeIcon');
            if (icon && document.documentElement.classList.contains('dark')) {
                icon.className = 'fa-solid fa-sun';
            }
        });

        // ── PAHAM-AJA GLOBAL JS BRIDGE ──
        const PahamAja = {
            toast: function(msg, type = 'success') {
                const container = document.getElementById('paToastContainer');
                const t = document.createElement('div');
                t.className = `pa-toast ${type}`;
                const icon = type === 'success' ? 'circle-check' : 'circle-xmark';
                t.innerHTML = `<i class="fa-solid fa-${icon}"></i> <span>${msg}</span>`;
                container.appendChild(t);
                setTimeout(() => { t.style.opacity = '0'; t.style.transform = 'translateX(20px)'; setTimeout(() => t.remove(), 400); }, 3500);
            },
            alert: function(title, text, type = 'warning') {
                const overlay = document.getElementById('paModalOverlay');
                const modal   = document.getElementById('paModal');
                const icon    = document.getElementById('paModalIcon');
                
                modal.className = `pa-modal ${type}`;
                let iconHtml = '<i class="fa-solid fa-bell"></i>';
                if(type === 'warning') iconHtml = '<i class="fa-solid fa-triangle-exclamation"></i>';
                if(type === 'danger')  iconHtml = '<i class="fa-solid fa-shield-halved"></i>';
                if(type === 'success') iconHtml = '<i class="fa-solid fa-trophy"></i>';
                
                icon.innerHTML = iconHtml;
                document.getElementById('paModalTitle').textContent = title;
                document.getElementById('paModalText').textContent = text;
                
                document.getElementById('paModalActions').style.display = 'flex';
                document.getElementById('paConfirmActions').style.display = 'none';
                
                overlay.classList.add('open');
            },
            closeModal: function() {
                document.getElementById('paModalOverlay').classList.remove('open');
            },
            confirm: function(title, text, type = 'danger', onConfirm) {
                const overlay = document.getElementById('paModalOverlay');
                const modal   = document.getElementById('paModal');
                const icon    = document.getElementById('paModalIcon');
                const btn     = document.getElementById('paConfirmBtn');
                
                modal.className = `pa-modal ${type}`;
                let iconHtml = '<i class="fa-solid fa-circle-question"></i>';
                if(type === 'danger')  iconHtml = '<i class="fa-solid fa-trash-can"></i>';
                if(type === 'warning') iconHtml = '<i class="fa-solid fa-triangle-exclamation"></i>';
                if(type === 'logout')  iconHtml = '<i class="fa-solid fa-right-from-bracket"></i>';
                
                icon.innerHTML = iconHtml;
                document.getElementById('paModalTitle').textContent = title;
                document.getElementById('paModalText').textContent = text;
                
                document.getElementById('paModalActions').style.display = 'none';
                document.getElementById('paConfirmActions').style.display = 'flex';
                
                if (type === 'danger') {
                    btn.style.background = 'linear-gradient(135deg,#EF4444,#B91C1C)';
                    btn.textContent = 'Ya, Hapus';
                } else if (type === 'logout') {
                    btn.style.background = 'linear-gradient(135deg,#EF4444,#B91C1C)'; // Keep it red for logout but different icon/text
                    btn.textContent = 'Ya, Keluar';
                } else {
                    btn.style.background = 'linear-gradient(135deg,#7C3AED,#4F46E5)';
                    btn.textContent = 'Ya, Lanjutkan';
                }

                btn.onclick = () => {
                    this.closeModal();
                    if (onConfirm) onConfirm();
                };
                
                overlay.classList.add('open');
            }
        };

        @if(session('success')) PahamAja.toast("{{ session('success') }}", 'success'); @endif
        @if(session('error'))   PahamAja.toast("{{ session('error') }}", 'error');   @endif
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vanilla-tilt/1.8.1/vanilla-tilt.min.js"></script>
    <script>
        // Auto-init 3D Tilt
        function init3DTilt() {
            VanillaTilt.init(document.querySelectorAll("[data-tilt]"), {
                max: 15,
                speed: 400,
                glare: true,
                "max-glare": 0.2,
                perspective: 1000,
            });
        }
        document.addEventListener('DOMContentLoaded', init3DTilt);
        // Re-init if content spans dynamically
        window.addEventListener('pahamaja-refresh-3d', init3DTilt);
    </script>
    @yield('scripts')
</body>
</html>
