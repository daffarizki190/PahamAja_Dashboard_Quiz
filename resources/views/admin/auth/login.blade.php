<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="PahamAja – Masuk ke Portal Admin">
    <title>Masuk – PahamAja</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            overflow: hidden;
            background: linear-gradient(135deg, #C8C6F5 0%, #B8B5EF 30%, #D0D8F8 60%, #C5CAF5 100%);
        }

        /* ── LEFT PANEL ── */
        .left-panel {
            flex: 1;
            display: flex; align-items: center; justify-content: center;
            position: relative; padding: 40px;
        }

        /* ── RIGHT PANEL (Card style) ── */
        .right-panel {
            display: flex; align-items: center; justify-content: center;
            padding: 40px 60px;
            min-width: 460px; max-width: 500px;
            background: transparent;
        }
        .login-card {
            background: #fff;
            border-radius: 20px;
            padding: 44px 40px;
            width: 100%;
            box-shadow: 0 8px 40px rgba(0,0,0,0.12);
            animation: fadeUp 0.6s cubic-bezier(0.16,1,0.3,1) forwards;
        }

        .card-title {
            font-size: 32px; font-weight: 900;
            color: #6D28D9; text-align: center;
            margin-bottom: 6px;
        }
        .card-sub {
            font-size: 13px; color: #6B7280; text-align: center;
            font-weight: 500; margin-bottom: 32px;
        }

        .field-group { margin-bottom: 18px; }
        .field-label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 7px; }
        .field-input {
            width: 100%; border: 1.5px solid #E5E7EB; border-radius: 10px;
            padding: 11px 16px; font-size: 14px; font-weight: 500;
            color: #1F2937; font-family: inherit; outline: none;
            transition: all 0.18s; background: #fff;
        }
        .field-input:focus { border-color: #7C3AED; box-shadow: 0 0 0 3px rgba(124,58,237,0.1); }
        .field-input::placeholder { color: #D1D5DB; }
        .field-input-wrap { position: relative; }
        .field-input-wrap .eye-btn {
            position: absolute; right: 14px; top: 50%; transform: translateY(-50%);
            background: none; border: none; cursor: pointer; color: #9CA3AF;
            font-size: 14px; transition: color 0.2s;
        }
        .field-input-wrap .eye-btn:hover { color: #7C3AED; }

        .btn-submit {
            width: 100%; padding: 13px; border-radius: 10px;
            background: #7C3AED;
            color: #fff; font-size: 15px; font-weight: 800;
            border: none; cursor: pointer; font-family: inherit;
            transition: all 0.2s;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            margin-top: 8px;
        }
        .btn-submit:hover { background: #6D28D9; transform: translateY(-1px); box-shadow: 0 4px 18px rgba(109,40,217,0.4); }
        .btn-submit:active { transform: translateY(0); }

        .forgot-link {
            display: block; text-align: center; margin-top: 16px;
            font-size: 13px; font-weight: 700; color: #7C3AED; text-decoration: none;
        }
        .forgot-link:hover { text-decoration: underline; }

        .alert-error {
            background: rgba(239,68,68,0.06); border: 1px solid rgba(239,68,68,0.2);
            color: #DC2626; border-radius: 8px; padding: 10px 14px;
            font-size: 13px; font-weight: 700; margin-bottom: 18px;
            display: flex; align-items: center; gap: 8px;
        }

        @keyframes fadeUp   { from{opacity:0;transform:translateY(20px);}to{opacity:1;transform:translateY(0);} }
        @keyframes float    { 0%,100%{transform:translateY(0);}50%{transform:translateY(-12px);} }

        @media (max-width: 900px) {
            .left-panel { display: none; }
            .right-panel { min-width: 0; width: 100%; max-width: none; }
            body { background: linear-gradient(135deg, #C8C6F5, #D0D8F8); }
        }
    </style>
</head>
<body>
    <!-- Left: Isometric Illustration -->
    <div class="left-panel">
        <!-- Isometric people & speech bubbles SVG -->
        <svg viewBox="0 0 500 440" fill="none" xmlns="http://www.w3.org/2000/svg"
             style="width:100%;max-width:500px;animation:float 5s ease-in-out infinite;">

            <!-- Platform / floor -->
            <ellipse cx="250" cy="390" rx="190" ry="28" fill="rgba(255,255,255,0.3)"/>
            <rect x="80" y="340" width="340" height="55" rx="20" fill="rgba(255,255,255,0.35)"/>
            <rect x="80" y="330" width="340" height="18" rx="9" fill="rgba(255,255,255,0.55)"/>

            <!-- Person 1 (leftmost, dark skin, laptop) -->
            <!-- body -->
            <rect x="90" y="270" width="44" height="58" rx="8" fill="#3B82F6"/>
            <!-- head -->
            <circle cx="112" cy="255" r="18" fill="#7C3CD8"/>
            <!-- laptop -->
            <rect x="70" y="318" width="64" height="8" rx="3" fill="#E5E7EB"/>
            <rect x="74" y="300" width="56" height="22" rx="4" fill="#fff" stroke="#D1D5DB" stroke-width="1.5"/>
            <rect x="78" y="303" width="48" height="13" rx="2" fill="#EDE9FE"/>
            <!-- legs -->
            <rect x="97" y="326" width="12" height="16" rx="4" fill="#1E40AF"/>
            <rect x="111" y="326" width="12" height="16" rx="4" fill="#1E40AF"/>

            <!-- Person 2 (second, female, orange top) -->
            <rect x="148" y="268" width="42" height="60" rx="8" fill="#F97316"/>
            <circle cx="169" cy="252" r="18" fill="#FDE68A"/>
            <!-- hair -->
            <ellipse cx="169" cy="237" rx="16" ry="8" fill="#92400E"/>
            <rect x="165" y="270" width="9" height="58" rx="4" fill="#F97316"/>
            <!-- laptop on lap -->
            <rect x="148" y="320" width="48" height="7" rx="3" fill="#D1D5DB"/>
            <rect x="151" y="304" width="42" height="18" rx="3" fill="#fff" stroke="#E5E7EB" stroke-width="1"/>
            <rect x="154" y="306" width="36" height="10" rx="2" fill="#DBEAFE"/>
            <!-- legs -->
            <rect x="154" y="326" width="11" height="14" rx="4" fill="#7C3CD8"/>
            <rect x="168" y="326" width="11" height="14" rx="4" fill="#7C3CD8"/>

            <!-- Person 3 (center, ponytail, blue jacket) -->
            <rect x="210" y="265" width="46" height="65" rx="8" fill="#7C3CD8"/>
            <circle cx="233" cy="248" r="19" fill="#FDE68A"/>
            <!-- ponytail -->
            <rect x="228" y="228" width="8" height="20" rx="4" fill="#92400E"/>
            <ellipse cx="232" cy="229" rx="10" ry="6" fill="#92400E"/>
            <!-- laptop on lap -->
            <rect x="208" y="320" width="52" height="7" rx="3" fill="#D1D5DB"/>
            <rect x="211" y="302" width="46" height="20" rx="3" fill="#fff" stroke="#E5E7EB" stroke-width="1"/>
            <rect x="214" y="305" width="40" height="12" rx="2" fill="#EDE9FE"/>
            <!-- legs -->
            <rect x="217" y="326" width="12" height="14" rx="4" fill="#4F46E5"/>
            <rect x="234" y="326" width="12" height="14" rx="4" fill="#4F46E5"/>

            <!-- Person 4 (pink top, dark skin) -->
            <rect x="272" y="268" width="44" height="60" rx="8" fill="#EC4899"/>
            <circle cx="294" cy="252" r="18" fill="#7C3CD8"/>
            <!-- laptop on leg -->
            <rect x="270" y="320" width="50" height="7" rx="3" fill="#D1D5DB"/>
            <rect x="272" y="305" width="46" height="17" rx="3" fill="#fff" stroke="#E5E7EB" stroke-width="1"/>
            <rect x="276" y="308" width="38" height="10" rx="2" fill="#FCE7F3"/>
            <!-- legs -->
            <rect x="278" y="327" width="12" height="13" rx="4" fill="#1E40AF"/>
            <rect x="293" y="327" width="12" height="13" rx="4" fill="#1E40AF"/>

            <!-- Person 5 (rightmost, tablet) -->
            <rect x="336" y="270" width="42" height="58" rx="8" fill="#10B981"/>
            <circle cx="357" cy="255" r="17" fill="#FDE68A"/>
            <!-- tablet in hands -->
            <rect x="328" y="298" width="50" height="34" rx="5" fill="#fff" stroke="#E5E7EB" stroke-width="1.5"/>
            <rect x="332" y="302" width="42" height="26" rx="3" fill="#D1FAE5"/>
            <!-- legs -->
            <rect x="342" y="326" width="11" height="14" rx="4" fill="#065F46"/>
            <rect x="356" y="326" width="11" height="14" rx="4" fill="#065F46"/>

            <!-- Speech bubble 1 (large green, center-left) -->
            <rect x="155" y="80" width="90" height="70" rx="16" fill="#34D399"/>
            <polygon points="175,150 188,170 202,150" fill="#34D399"/>
            <rect x="167" y="100" width="66" height="8" rx="4" fill="rgba(255,255,255,0.7)"/>
            <rect x="167" y="114" width="50" height="8" rx="4" fill="rgba(255,255,255,0.7)"/>
            <rect x="167" y="128" width="58" height="8" rx="4" fill="rgba(255,255,255,0.7)"/>

            <!-- Speech bubble 2 (purple with ?) -->
            <rect x="100" y="100" width="70" height="70" rx="16" fill="#7C3CD8"/>
            <polygon points="112,170 128,192 144,170" fill="#7C3CD8"/>
            <text x="125" y="145" text-anchor="middle" font-size="38" font-weight="900" fill="white" font-family="Arial">?</text>

            <!-- Speech bubble 3 (orange small) -->
            <rect x="270" y="100" width="70" height="55" rx="14" fill="#F97316"/>
            <polygon points="282,155 296,174 310,155" fill="#F97316"/>
            <rect x="281" y="117" width="48" height="7" rx="3" fill="rgba(255,255,255,0.7)"/>
            <rect x="281" y="130" width="36" height="7" rx="3" fill="rgba(255,255,255,0.7)"/>

            <!-- ? bubbles top -->
            <rect x="80" y="50" width="55" height="55" rx="14" fill="#7C3CD8"/>
            <polygon points="90,105 104,124 118,105" fill="#7C3CD8"/>
            <text x="107" y="87" text-anchor="middle" font-size="30" font-weight="900" fill="white" font-family="Arial">?</text>

            <!-- Chat bubble top right -->
            <rect x="340" y="60" width="70" height="55" rx="14" fill="#7C3CD8" opacity=".8"/>
            <polygon points="352,115 366,135 380,115" fill="#7C3CD8" opacity=".8"/>
            <rect x="350" y="77" width="50" height="7" rx="3" fill="rgba(255,255,255,0.6)"/>
            <rect x="350" y="91" width="38" height="7" rx="3" fill="rgba(255,255,255,0.6)"/>

            <!-- Stars -->
            <path d="M410 120 L414 132 L426 132 L416 140 L420 152 L410 144 L400 152 L404 140 L394 132 L406 132 Z" fill="#FBBF24"/>
            <path d="M72 158 L75 167 L84 167 L77 172 L80 181 L72 176 L64 181 L67 172 L60 167 L69 167 Z" fill="#FBBF24"/>
            <path d="M440 200 L442 207 L449 207 L443 211 L445 218 L440 214 L435 218 L437 211 L431 207 L438 207 Z" fill="#FBBF24" opacity=".7"/>

            <!-- Gears (right side) -->
            <circle cx="420" cy="290" r="22" fill="none" stroke="#10B981" stroke-width="4"/>
            <circle cx="420" cy="290" r="10" fill="#10B981"/>
            <!-- gear teeth simplified -->
            <rect x="414" y="264" width="12" height="10" rx="3" fill="#10B981"/>
            <rect x="414" y="306" width="12" height="10" rx="3" fill="#10B981"/>
            <rect x="394" y="284" width="10" height="12" rx="3" fill="#10B981"/>
            <rect x="436" y="284" width="10" height="12" rx="3" fill="#10B981"/>

            <circle cx="65" cy="240" r="16" fill="none" stroke="#10B981" stroke-width="3"/>
            <circle cx="65" cy="240" r="7" fill="#10B981"/>
            <rect x="60" y="220" width="10" height="8" rx="2" fill="#10B981"/>
            <rect x="60" y="252" width="10" height="8" rx="2" fill="#10B981"/>
            <rect x="45" y="235" width="8" height="10" rx="2" fill="#10B981"/>
            <rect x="77" y="235" width="8" height="10" rx="2" fill="#10B981"/>

            <!-- Book (top center) -->
            <rect x="285" y="28" width="44" height="56" rx="5" fill="#60A5FA"/>
            <rect x="282" y="28" width="6" height="56" rx="3" fill="#2563EB"/>
            <rect x="291" y="38" width="30" height="4" rx="2" fill="rgba(255,255,255,0.7)"/>
            <rect x="291" y="48" width="24" height="4" rx="2" fill="rgba(255,255,255,0.5)"/>
            <rect x="291" y="58" width="28" height="4" rx="2" fill="rgba(255,255,255,0.5)"/>
            <!-- bookmark ribbon -->
            <path d="M310 28 L322 28 L322 50 L316 44 L310 50 Z" fill="#EF4444"/>

            <!-- Chat dot bubble (bottom right speech) -->
            <rect x="350" y="160" width="55" height="38" rx="12" fill="#34D399" opacity=".9"/>
            <polygon points="360,198 374,215 388,198" fill="#34D399" opacity=".9"/>
            <circle cx="362" cy="179" r="5" fill="white" opacity=".8"/>
            <circle cx="377" cy="179" r="5" fill="white" opacity=".8"/>
            <circle cx="392" cy="179" r="5" fill="white" opacity=".8"/>
        </svg>
    </div>

    <!-- Right: Login Card -->
    <div class="right-panel">
        <div class="login-card">
            <h1 class="card-title">PahamAja</h1>
            <p class="card-sub">Selamat Datang, Masuk ke akun Anda</p>
            <div style="background:rgba(124,58,237,0.05); border:1px solid rgba(124,58,237,0.1); border-radius:12px; padding:12px; margin-bottom:24px; text-align:center;">
                <p style="font-size:11px; font-weight:700; color:#6D28D9; text-transform:uppercase; letter-spacing:0.05em;">Input Dibutuhkan</p>
                <p style="font-size:12px; color:#7C3AED; margin-top:2px;">Silakan masukkan <b>Password Admin</b> Anda di bawah.</p>
            </div>

            @if(session('error'))
                <div class="alert-error">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    {{ session('error') }}
                </div>
            @endif
            @if($errors->any())
                <div class="alert-error">
                    <i class="fa-solid fa-circle-xmark"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login.submit') }}" id="loginForm">
                @csrf

                <!-- Password -->
                <div class="field-group">
                    <label class="field-label" for="password">Password</label>
                    <div class="field-input-wrap">
                        <input class="field-input" type="password" id="password" name="password"
                               placeholder="••••••••" required style="padding-right:44px;">
                        <button type="button" class="eye-btn" id="eyeBtn" onclick="toggleEye()">
                            <i class="fa-regular fa-eye-slash" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-submit" id="loginBtn">
                    Masuk <i class="fa-solid fa-arrow-right"></i>
                </button>
            </form>

            <a href="#" class="forgot-link">Lupa Password?</a>
        </div>
    </div>

    <script src="{{ asset('js/prevent-double-submit.js') }}"></script>
    <script>
        function toggleEye() {
            const input = document.getElementById('password');
            const icon  = document.getElementById('eyeIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'fa-regular fa-eye';
            } else {
                input.type = 'password';
                icon.className = 'fa-regular fa-eye-slash';
            }
        }
    </script>
</body>
</html>
