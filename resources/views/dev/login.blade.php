<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dev Login — PahamAja Monitor</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #0a0e1a;
            background-image:
                radial-gradient(ellipse at 20% 50%, rgba(99, 102, 241, 0.12) 0%, transparent 50%),
                radial-gradient(ellipse at 80% 20%, rgba(16, 185, 129, 0.08) 0%, transparent 50%);
            font-family: 'Inter', sans-serif;
            padding: 1rem;
        }

        .card {
            width: 100%;
            max-width: 420px;
            background: rgba(255,255,255,0.04);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 20px;
            padding: 2.5rem;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #10b981;
            font-size: 0.72rem;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 100px;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            margin-bottom: 1.25rem;
        }

        .dot {
            width: 6px; height: 6px;
            border-radius: 50%;
            background: #10b981;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }

        h1 { font-size: 1.6rem; font-weight: 700; color: #f1f5f9; margin-bottom: 0.4rem; }
        p.sub { color: #64748b; font-size: 0.875rem; margin-bottom: 2rem; }

        .error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
            margin-bottom: 1.25rem;
        }

        label { display: block; color: #94a3b8; font-size: 0.8rem; font-weight: 500; margin-bottom: 0.4rem; }

        input[type="password"] {
            width: 100%;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 10px;
            padding: 0.75rem 1rem;
            color: #f1f5f9;
            font-size: 0.9rem;
            font-family: inherit;
            outline: none;
            transition: border-color 0.2s;
            margin-bottom: 1.5rem;
        }
        input[type="password"]:focus { border-color: rgba(99, 102, 241, 0.5); }

        button {
            width: 100%;
            padding: 0.8rem;
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 0.9rem;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            transition: opacity 0.2s, transform 0.1s;
        }
        button:hover { opacity: 0.9; }
        button:active { transform: scale(0.99); }

        .footer { text-align: center; margin-top: 1.5rem; color: #334155; font-size: 0.75rem; }
    </style>
</head>
<body>
    <div class="card">
        <div class="badge"><span class="dot"></span> Dev Portal</div>
        <h1>Health Monitor</h1>
        <p class="sub">Masuk menggunakan password tim developer.</p>

        @if(session('error'))
            <div class="error">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('dev.login.submit') }}">
            @csrf
            <label for="password">Password Developer</label>
            <input type="password" id="password" name="password" placeholder="••••••••••" autofocus required>
            <button type="submit">Masuk →</button>
        </form>

        <div class="footer">PahamAja Dashboard Quiz &middot; Internal Use Only</div>
    </div>
</body>
</html>
