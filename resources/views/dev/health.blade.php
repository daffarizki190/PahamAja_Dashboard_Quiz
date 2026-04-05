<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Health Monitor — PahamAja</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg: #080c17;
            --surface: rgba(255,255,255,0.04);
            --border: rgba(255,255,255,0.08);
            --text: #e2e8f0;
            --muted: #64748b;
            --green: #10b981;
            --green-bg: rgba(16, 185, 129, 0.12);
            --green-border: rgba(16, 185, 129, 0.3);
            --yellow: #f59e0b;
            --yellow-bg: rgba(245, 158, 11, 0.12);
            --yellow-border: rgba(245, 158, 11, 0.3);
            --red: #ef4444;
            --red-bg: rgba(239, 68, 68, 0.12);
            --red-border: rgba(239, 68, 68, 0.3);
            --indigo: #6366f1;
        }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background-image:
                radial-gradient(ellipse at 10% 0%, rgba(99,102,241,0.1) 0%, transparent 50%),
                radial-gradient(ellipse at 90% 90%, rgba(16,185,129,0.06) 0%, transparent 50%);
        }

        /* ── Header ── */
        header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.25rem 2rem;
            border-bottom: 1px solid var(--border);
            background: rgba(8,12,23,0.8);
            backdrop-filter: blur(12px);
            position: sticky; top: 0; z-index: 100;
        }
        .logo { display: flex; align-items: center; gap: 10px; }
        .logo-icon { font-size: 1.4rem; }
        .logo h1 { font-size: 1rem; font-weight: 700; color: var(--text); }
        .logo span { font-size: 0.7rem; color: var(--muted); display: block; }

        .header-right { display: flex; align-items: center; gap: 1rem; }
        .refresh-btn {
            display: flex; align-items: center; gap: 6px;
            background: rgba(99,102,241,0.15);
            border: 1px solid rgba(99,102,241,0.3);
            color: #a5b4fc;
            padding: 6px 14px;
            border-radius: 8px;
            font-size: 0.8rem; font-weight: 500; font-family: inherit;
            cursor: pointer; transition: all 0.2s;
        }
        .refresh-btn:hover { background: rgba(99,102,241,0.25); }
        .refresh-btn.spinning svg { animation: spin 1s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }

        .logout-link {
            color: var(--muted); font-size: 0.8rem; text-decoration: none;
            transition: color 0.2s;
        }
        .logout-link:hover { color: var(--red); }

        /* ── Main ── */
        main { max-width: 1100px; margin: 2rem auto; padding: 0 1.5rem 4rem; }

        /* ── Overall Banner ── */
        #overall-banner {
            border-radius: 16px;
            padding: 1.5rem 2rem;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 1.25rem;
            transition: all 0.4s ease;
        }
        #overall-banner.green { background: var(--green-bg); border: 1px solid var(--green-border); }
        #overall-banner.yellow { background: var(--yellow-bg); border: 1px solid var(--yellow-border); }
        #overall-banner.red { background: var(--red-bg); border: 1px solid var(--red-border); }
        #overall-banner.loading { background: var(--surface); border: 1px solid var(--border); }

        .big-lamp { font-size: 2.8rem; line-height: 1; }
        .overall-info h2 { font-size: 1.2rem; font-weight: 700; }
        .overall-info p { font-size: 0.85rem; color: var(--muted); margin-top: 2px; }
        #overall-banner.green .overall-info h2 { color: var(--green); }
        #overall-banner.yellow .overall-info h2 { color: var(--yellow); }
        #overall-banner.red .overall-info h2 { color: var(--red); }

        .meta-pills { display: flex; gap: 8px; flex-wrap: wrap; margin-left: auto; }
        .pill {
            background: rgba(255,255,255,0.06);
            border: 1px solid var(--border);
            border-radius: 100px;
            padding: 4px 10px;
            font-size: 0.72rem;
            color: var(--muted);
            font-family: 'JetBrains Mono', monospace;
        }

        /* ── Section title ── */
        .section-title {
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 1rem;
        }

        /* ── Check Cards ── */
        .checks-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .check-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
            transition: border-color 0.3s;
        }
        .check-card.green { border-color: var(--green-border); }
        .check-card.yellow { border-color: var(--yellow-border); }
        .check-card.red { border-color: var(--red-border); }

        .card-header {
            display: flex; align-items: center;
            padding: 1rem 1.25rem;
            gap: 10px;
            border-bottom: 1px solid var(--border);
        }
        .lamp { font-size: 1.1rem; }
        .card-label { font-size: 0.875rem; font-weight: 600; flex: 1; }
        .status-badge {
            font-size: 0.65rem; font-weight: 700; text-transform: uppercase;
            padding: 2px 8px; border-radius: 100px; letter-spacing: 0.05em;
        }
        .check-card.green .status-badge { background: var(--green-bg); color: var(--green); border: 1px solid var(--green-border); }
        .check-card.yellow .status-badge { background: var(--yellow-bg); color: var(--yellow); border: 1px solid var(--yellow-border); }
        .check-card.red .status-badge { background: var(--red-bg); color: var(--red); border: 1px solid var(--red-border); }

        .card-body { padding: 1rem 1.25rem; }
        .card-message { font-size: 0.83rem; color: var(--text); margin-bottom: 0.75rem; }

        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4px 8px;
            margin-bottom: 0.75rem;
        }
        .detail-row { font-size: 0.72rem; }
        .detail-key { color: var(--muted); }
        .detail-val { color: var(--text); font-family: 'JetBrains Mono', monospace; word-break: break-all; }

        .suggestions { margin-top: 0.75rem; border-top: 1px solid var(--border); padding-top: 0.75rem; }
        .suggestion-title {
            font-size: 0.68rem; font-weight: 600; text-transform: uppercase;
            letter-spacing: 0.07em; color: var(--yellow); margin-bottom: 6px;
        }
        li.suggestion {
            font-size: 0.78rem; color: #cbd5e1;
            list-style: none; padding-left: 1rem;
            position: relative; margin-bottom: 4px;
        }
        li.suggestion::before {
            content: '→';
            position: absolute; left: 0; color: var(--yellow);
        }

        /* Table for complex details */
        .detail-table { width: 100%; border-collapse: collapse; font-size: 0.73rem; margin-top: 4px; }
        .detail-table td { padding: 3px 0; color: var(--muted); }
        .detail-table td:last-child { text-align: right; }
        .chip-ok { color: var(--green); }
        .chip-no { color: var(--red); }

        /* ── Loader ── */
        .skeleton {
            background: linear-gradient(90deg, rgba(255,255,255,0.04) 25%, rgba(255,255,255,0.07) 50%, rgba(255,255,255,0.04) 75%);
            background-size: 200% 100%;
            animation: shimmer 1.4s infinite;
            border-radius: 8px;
        }
        @keyframes shimmer { to { background-position: -200% 0; } }

        .timestamp { font-size: 0.72rem; color: var(--muted); text-align: right; margin-top: 1rem; font-family: 'JetBrains Mono'; }
    </style>
</head>
<body>

<header>
    <div class="logo">
        <span class="logo-icon">🛡️</span>
        <div>
            <h1>System Health Monitor</h1>
            <span>PahamAja Dashboard Quiz &middot; Dev Team Only</span>
        </div>
    </div>
    <div class="header-right">
        <button class="refresh-btn" id="refreshBtn" onclick="loadHealth()">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M23 4v6h-6M1 20v-6h6"/>
                <path d="M3.51 9a9 9 0 0114.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0020.49 15"/>
            </svg>
            Refresh
        </button>
        <form method="POST" action="{{ route('dev.logout') }}" style="display:inline">
            @csrf
            <button type="submit" class="logout-link" style="background:none;border:none;cursor:pointer;font-family:inherit">Keluar</button>
        </form>
    </div>
</header>

<main>

    <!-- Overall Banner -->
    <div id="overall-banner" class="loading">
        <div class="big-lamp" id="bigLamp">⏳</div>
        <div class="overall-info">
            <h2 id="overallTitle">Memeriksa sistem...</h2>
            <p id="overallSub">Mohon tunggu</p>
        </div>
        <div class="meta-pills" id="metaPills">
            <div class="pill skeleton" style="width:80px;height:20px;">&nbsp;</div>
            <div class="pill skeleton" style="width:100px;height:20px;">&nbsp;</div>
        </div>
    </div>

    <!-- Check Cards -->
    <div class="section-title">Detail Pemeriksaan</div>
    <div class="checks-grid" id="checksGrid">
        <!-- Loading skeletons -->
        <div class="check-card" style="height:160px">
            <div class="card-header">
                <div class="skeleton" style="width:60%;height:14px;">&nbsp;</div>
            </div>
            <div class="card-body">
                <div class="skeleton" style="width:80%;height:12px;margin-bottom:8px;">&nbsp;</div>
                <div class="skeleton" style="width:50%;height:12px;">&nbsp;</div>
            </div>
        </div>
        <div class="check-card" style="height:160px">
            <div class="card-header">
                <div class="skeleton" style="width:60%;height:14px;">&nbsp;</div>
            </div>
            <div class="card-body">
                <div class="skeleton" style="width:80%;height:12px;margin-bottom:8px;">&nbsp;</div>
                <div class="skeleton" style="width:50%;height:12px;">&nbsp;</div>
            </div>
        </div>
    </div>

    <div class="timestamp" id="lastChecked"></div>

</main>

<script>
const STATUS_CONFIG = {
    green:  { lamp: '🟢', label: 'Semua Sistem Normal', sub: 'Tidak ada masalah yang terdeteksi.', cls: 'green' },
    yellow: { lamp: '🟡', label: 'Ada Peringatan',       sub: 'Periksa kartu berikut untuk rekomendasi.', cls: 'yellow' },
    red:    { lamp: '🔴', label: 'Ada Masalah Kritis',    sub: 'Tindakan segera diperlukan.', cls: 'red' },
    loading:{ lamp: '⏳', label: 'Memeriksa sistem...', sub: 'Mohon tunggu', cls: 'loading' },
};

function lampFor(status) {
    return { green: '🟢', yellow: '🟡', red: '🔴' }[status] ?? '⚪';
}

function renderDetails(details) {
    if (!details || typeof details !== 'object') return '';

    // Handle extension map
    if (details.extensions && typeof details.extensions === 'object') {
        const exts = details.extensions;
        const topDetails = Object.entries(details)
            .filter(([k]) => k !== 'extensions' && k !== 'tables')
            .map(([k, v]) => `<tr><td class="detail-key">${k}</td><td class="detail-val">${v}</td></tr>`)
            .join('');

        const extRows = Object.entries(exts)
            .map(([k, v]) => `<tr><td class="detail-key">${k}</td><td class="${v ? 'chip-ok' : 'chip-no'}">${v ? '✓ aktif' : '✗ tidak ada'}</td></tr>`)
            .join('');

        const tableRows = details.tables
            ? Object.entries(details.tables).map(([k,v]) => `<tr><td class="detail-key">${k}</td><td class="detail-val">${v} rows</td></tr>`).join('')
            : '';

        return `<table class="detail-table">${topDetails}${extRows}${tableRows}</table>`;
    }

    return Object.entries(details)
        .map(([k, v]) => {
            if (typeof v === 'object' && v !== null) {
                return Object.entries(v).map(([k2, v2]) => `
                    <div class="detail-row"><span class="detail-key">${k} → ${k2}: </span><span class="detail-val">${v2}</span></div>
                `).join('');
            }
            return `<div class="detail-row"><span class="detail-key">${k}: </span><span class="detail-val">${v}</span></div>`;
        }).join('');
}

function buildCard(check) {
    const { label, status, message, details, suggestions } = check;
    const hasDetails = details && Object.keys(details).length > 0;
    const hasSuggestions = suggestions && suggestions.length > 0;

    return `
        <div class="check-card ${status}">
            <div class="card-header">
                <span class="lamp">${lampFor(status)}</span>
                <span class="card-label">${label}</span>
                <span class="status-badge">${status.toUpperCase()}</span>
            </div>
            <div class="card-body">
                <p class="card-message">${message}</p>
                ${hasDetails ? renderDetails(details) : ''}
                ${hasSuggestions ? `
                    <div class="suggestions">
                        <div class="suggestion-title">💡 Saran Perbaikan</div>
                        <ul>
                            ${suggestions.map(s => `<li class="suggestion">${s}</li>`).join('')}
                        </ul>
                    </div>
                ` : ''}
            </div>
        </div>
    `;
}

async function loadHealth() {
    const btn = document.getElementById('refreshBtn');
    btn.classList.add('spinning');

    try {
        const res = await fetch('{{ route("dev.health.json") }}', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await res.json();

        // Update banner
        const cfg = STATUS_CONFIG[data.overall] ?? STATUS_CONFIG.loading;
        const banner = document.getElementById('overall-banner');
        banner.className = `${cfg.cls}`;
        banner.id = 'overall-banner';
        document.getElementById('bigLamp').textContent = cfg.lamp;
        document.getElementById('overallTitle').textContent = cfg.label;
        document.getElementById('overallSub').textContent = cfg.sub;
        document.getElementById('metaPills').innerHTML = `
            <div class="pill">Laravel ${data.app_version}</div>
            <div class="pill">PHP ${data.php_version}</div>
            <div class="pill">${data.environment}</div>
        `;

        // Render cards
        const grid = document.getElementById('checksGrid');
        grid.innerHTML = Object.values(data.checks).map(buildCard).join('');

        // Timestamp
        const ts = new Date(data.timestamp);
        document.getElementById('lastChecked').textContent =
            `Terakhir diperiksa: ${ts.toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'medium' })}`;

    } catch (e) {
        document.getElementById('overallTitle').textContent = 'Gagal memuat data';
        document.getElementById('overallSub').textContent = e.message;
    } finally {
        btn.classList.remove('spinning');
    }
}

// Auto-load on page open
loadHealth();

// Auto-refresh every 60 seconds
setInterval(loadHealth, 60000);
</script>

</body>
</html>
