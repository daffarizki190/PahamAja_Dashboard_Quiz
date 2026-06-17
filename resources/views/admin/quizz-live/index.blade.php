@extends('layouts.app')

@section('title', 'Quizz Live – PahamAja')
@section('meta_description', 'Mode gamifikasi kuis dengan Power-Ups dan saling serang')
@section('search_placeholder', 'Cari kuis...')
@section('show_search', true)

@section('topbar_actions')
    <a href="{{ route('admin.quizzes.create') }}" class="btn btn-primary" style="padding:8px 16px; font-size:13px;">
        <i class="fa-solid fa-plus"></i> Tambah Kuis
    </a>
@endsection

@section('head_extra')
<style>
    .live-hero {
        background: linear-gradient(135deg, #1E1B4B, #312E81, #4C1D95);
        border-radius: 24px; padding: 36px 40px;
        color: #fff; margin-bottom: 28px;
        position: relative; overflow: hidden;
    }
    .live-hero::before {
        content: ''; position: absolute; top: -50%; right: -20%; width: 60%; height: 200%;
        background: radial-gradient(circle, rgba(167,139,250,0.15) 0%, transparent 70%);
        pointer-events: none;
    }
    .live-hero-title { font-size: 28px; font-weight: 900; margin-bottom: 8px; position: relative; z-index: 1; }
    .live-hero-sub { font-size: 14px; color: rgba(255,255,255,0.65); font-weight: 500; line-height: 1.6; position: relative; z-index: 1; max-width: 600px; }

    .live-features {
        display: flex; gap: 24px; margin-top: 20px; position: relative; z-index: 1;
    }
    .live-feature {
        display: flex; align-items: center; gap: 10px;
        background: rgba(255,255,255,0.08); border-radius: 12px; padding: 10px 16px;
        font-size: 13px; font-weight: 700; color: #fff;
    }
    .live-feature i { font-size: 18px; }
    .live-feature .feat-freeze { color: #93C5FD; }
    .live-feature .feat-fire { color: #F97316; }
    .live-feature .feat-star { color: #FBBF24; }

    /* Quiz Live Card */
    .live-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
        gap: 20px;
    }
    .live-card {
        background: var(--bg-card); border: 1px solid var(--border);
        border-radius: 20px; padding: 24px; transition: all 0.25s;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        display: flex; flex-direction: column; gap: 16px;
        position: relative; overflow: hidden;
    }
    .live-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 32px rgba(124,58,237,0.12);
        border-color: rgba(124,58,237,0.3);
    }
    .live-card-header { display: flex; align-items: flex-start; gap: 14px; }
    .live-card-icon {
        width: 52px; height: 52px; border-radius: 14px;
        background: linear-gradient(135deg, #7C3AED, #4F46E5);
        display: flex; align-items: center; justify-content: center;
        font-size: 24px; flex-shrink: 0; color: #fff;
        box-shadow: 0 4px 12px rgba(124,58,237,0.35);
    }
    .live-card-title { font-size: 15px; font-weight: 800; color: var(--text-primary); line-height: 1.3; margin-bottom: 4px; }
    .live-card-meta { font-size: 12px; color: var(--text-muted); font-weight: 500; display: flex; gap: 12px; }
    .live-card-meta i { font-size: 10px; }

    .live-card-stats {
        display: flex; gap: 10px;
    }
    .live-stat {
        flex: 1; background: rgba(124,58,237,0.04);
        border-radius: 12px; padding: 12px; text-align: center;
        border: 1px solid rgba(124,58,237,0.08);
    }
    .live-stat-value { font-size: 22px; font-weight: 900; color: var(--purple); }
    .live-stat-label { font-size: 10px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; margin-top: 2px; }

    .live-card-actions { display: flex; gap: 8px; }
    .live-btn {
        flex: 1; display: flex; align-items: center; justify-content: center; gap: 6px;
        padding: 10px; border-radius: 10px; font-size: 12px; font-weight: 800;
        cursor: pointer; transition: all 0.18s; font-family: inherit;
        text-decoration: none; border: none; white-space: nowrap;
    }
    .live-btn-play {
        background: linear-gradient(135deg, #7C3AED, #4F46E5);
        color: #fff; box-shadow: 0 4px 0 #4C1D95;
    }
    .live-btn-play:hover { filter: brightness(1.1); transform: translateY(-2px); box-shadow: 0 6px 0 #4C1D95; }
    .live-btn-play:active { transform: translateY(2px); box-shadow: 0 2px 0 #4C1D95; }

    .live-btn-link {
        background: rgba(124,58,237,0.06);
        color: var(--purple);
        border: 1px solid rgba(124,58,237,0.15);
    }
    .live-btn-link:hover { background: rgba(124,58,237,0.12); }

    /* Copy URL mini-modal */
    .copy-toast {
        position: fixed; bottom: 32px; left: 50%; transform: translateX(-50%);
        background: #1E1B4B; color: #fff; padding: 12px 24px; border-radius: 12px;
        font-weight: 700; font-size: 14px; z-index: 9999;
        box-shadow: 0 10px 30px rgba(30,27,75,0.3);
        animation: fadeUp 0.3s ease forwards;
        display: none;
    }

    @media (max-width: 640px) {
        .live-grid { grid-template-columns: 1fr; }
        .live-hero { padding: 24px 20px; border-radius: 18px; }
        .live-hero-title { font-size: 22px; }
        .live-features { flex-direction: column; gap: 10px; }
    }
</style>
@endsection

@section('content')
<!-- Hero Banner -->
<div class="live-hero fade-up">
    <div class="live-hero-title"><i class="fa-solid fa-gamepad"></i> Quizz Live</div>
    <div class="live-hero-sub">
        Mode kuis interaktif bergaya Wayground — dengan animasi real-time, skor & streak, 
        serta fitur <strong>Power-Up Saling Serang</strong> antar peserta! Pilih kuis di bawah untuk memulai.
    </div>
    <div class="live-features">
        <div class="live-feature">
            <i class="fa-solid fa-fire feat-fire"></i> Streak & Skor
        </div>
        <div class="live-feature">
            <i class="fa-solid fa-crosshairs feat-freeze"></i> Power-Up Serangan
        </div>
        <div class="live-feature">
            <i class="fa-solid fa-star feat-star"></i> Confetti & Animasi
        </div>
    </div>
</div>

<div style="display:flex; flex-direction:column; align-items:center; justify-content:center; padding: 60px 20px; background: var(--bg-card); border: 1px solid var(--border); border-radius: 24px; text-align:center; box-shadow: 0 10px 30px rgba(0,0,0,0.02); margin-top: 24px;">
    <div style="font-size: 80px; margin-bottom: 24px; position:relative; display:inline-block;">
        🚀
        <div style="position:absolute; top:-10px; right:-20px; background:#EF4444; color:#fff; font-size:11px; font-weight:900; padding:4px 10px; border-radius:12px; transform:rotate(10deg); box-shadow:0 4px 10px rgba(239,68,68,0.3);">WIP</div>
    </div>
    <h3 style="font-size: 28px; font-weight: 900; color: var(--text-primary); margin: 0 0 12px;">Coming Soon!</h3>
    <p style="font-size: 15px; color: var(--text-muted); font-weight: 500; max-width: 480px; margin: 0 0 32px; line-height:1.6;">
        Fitur <strong>Quizz Live Multiplayer</strong> sedang dalam tahap pengembangan akhir. Nantikan pengalaman kuis interaktif dengan skor real-time dan power-up serangan yang lebih stabil!
    </p>
    <a href="{{ route('admin.quizzes.index') }}" class="live-btn live-btn-play" style="width: auto; padding: 14px 32px; font-size:14px;">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard
    </a>
</div>

<!-- Copy Toast -->
<div class="copy-toast" id="copyToast">
    <i class="fa-solid fa-check" style="color:#34D399; margin-right: 8px;"></i> Link Quizz Live berhasil disalin!
</div>
@endsection

@section('scripts')
<script>
function copyLiveLink(slug) {
    // Participants join via the public quiz page, then they get redirected to /take
    // The "live" experience uses /take-live instead. We copy the public quiz URL.
    const url = `${window.location.origin}/quiz/${slug}`;
    navigator.clipboard.writeText(url).then(() => {
        const toast = document.getElementById('copyToast');
        toast.style.display = 'block';
        setTimeout(() => { toast.style.display = 'none'; }, 2500);
    });
}

// Search integration
window.addEventListener('pahamaja-search', function (e) {
    const q = e.detail.toLowerCase().trim();
    document.querySelectorAll('#liveGrid .live-card').forEach(card => {
        const name = card.dataset.name || '';
        card.style.display = (!q || name.includes(q)) ? '' : 'none';
    });
});
</script>
@endsection
