@extends('layouts.app')

@section('title', $employee->name . ' – Analisis Performa')
@section('meta_description', 'Analisis performa dan riwayat kuis karyawan')
@section('page_title', $employee->name)
@section('page_subtitle', $employee->position . ' · ' . $employee->department)

@section('topbar_left')
    <a href="{{ route('admin.employees.index') }}" class="btn btn-ghost" style="padding:9px 12px; font-size:12px; background:rgba(124,58,237,0.08); border:1px solid rgba(124,58,237,0.15); color:var(--purple); margin-right:8px;">
        <i class="fa-solid fa-arrow-left"></i> Daftar Karyawan
    </a>
@endsection

@section('topbar_actions')
    <div style="display:flex; align-items:center; gap:12px;">
        <div style="text-align:right;">
            <div style="font-size:10px; font-weight:800; color:#94A3B8; text-transform:uppercase; letter-spacing:0.05em;">Skor Rerata</div>
            <div style="font-size:16px; font-weight:900; color:var(--purple);">{{ number_format($employee->average_score, 1) }}%</div>
        </div>
        <div style="width:1px; height:24px; background:#E5E3F0;"></div>
        <div style="background:var(--purple); color:#fff; width:36px; height:36px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-weight:900; font-size:14px; box-shadow:0 4px 12px rgba(124,58,237,0.3); overflow:hidden;">
            @if($employee->avatar)
                <img src="/storage/{{ $employee->avatar }}" style="width:100%; height:100%; object-fit:cover;">
            @else
                {{ $employee->name[0] }}
            @endif
        </div>
    </div>
@endsection

@section('head_extra')
<style>
    /* Header Card */
    .emp-header-card {
        background: #fff; border: 1px solid #E5E3F0; border-radius: 16px; padding: 24px;
        display: flex; align-items: center; gap: 20px; margin-bottom: 24px;
    }
    .emp-avatar {
        width: 64px; height: 64px; border-radius: 20px; background: linear-gradient(135deg, var(--purple) 0%, #6D28D9 100%);
        display: flex; align-items: center; justify-content: center; color: #fff; font-size: 24px; font-weight: 900;
        box-shadow: 0 8px 16px rgba(124,58,237,0.2);
        overflow: hidden;
    }
    
    .avatar-photo-wrap {
        position: relative;
        width: 64px; height: 64px;
        flex-shrink: 0;
        cursor: pointer;
    }
    .avatar-photo-wrap img {
        width: 100%; height: 100%; object-fit: cover;
        border-radius: 20px;
        transition: transform 0.35s cubic-bezier(.34,1.56,.64,1), box-shadow 0.35s, filter 0.35s;
        display: block;
        box-shadow:
            0 2px 0 rgba(0,0,0,0.06),
            0 6px 16px rgba(0,0,0,0.18),
            0 14px 32px rgba(0,0,0,0.10),
            0 0 0 3px rgba(255,255,255,0.9),
            0 0 0 5px rgba(124,58,237,0.15);
        transform: perspective(500px) rotateX(3deg) translateY(-2px) scale(1.01);
        filter: drop-shadow(0 8px 16px rgba(80,40,180,0.22));
    }
    .avatar-photo-wrap:hover img {
        transform: perspective(500px) rotateX(6deg) translateY(-6px) scale(1.07);
        box-shadow:
            0 4px 0 rgba(0,0,0,0.08),
            0 12px 28px rgba(80,40,180,0.3),
            0 24px 48px rgba(0,0,0,0.12),
            0 0 0 3px rgba(255,255,255,1),
            0 0 0 7px rgba(124,58,237,0.28);
        filter: drop-shadow(0 12px 24px rgba(80,40,180,0.38));
    }
    .avatar-photo-wrap .zoom-hint {
        position: absolute; bottom: -2px; right: -2px;
        width: 22px; height: 22px; border-radius: 50%;
        background: linear-gradient(135deg, #7C3AED, #5B21B6);
        display: flex; align-items: center; justify-content: center;
        color: #fff; font-size: 9px;
        box-shadow: 0 2px 8px rgba(124,58,237,0.4);
        opacity: 0; transform: scale(0.6);
        transition: opacity 0.2s, transform 0.2s;
        pointer-events: none;
    }
    .avatar-photo-wrap:hover .zoom-hint { opacity: 1; transform: scale(1); }

    .badge-card {
        background: #fff; border: 1px solid #E5E3F0; border-radius: 14px; padding: 14px 18px;
        display: flex; align-items: center; gap: 12px; transition: transform 0.2s;
    }
    .badge-card:hover { transform: translateY(-2px); border-color: var(--purple-light); }
    .badge-icon {
        width: 38px; height: 38px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 18px;
    }
    
    .history-item {
        padding: 18px; border-bottom: 1px solid #F3F2FB; display: flex; align-items: center; justify-content: space-between;
        transition: background 0.2s; text-decoration: none;
    }
    .history-item:hover { background: #F9FAFB; }
    .history-item:last-child { border-bottom: none; }
    
    .score-pill {
        padding: 4px 10px; border-radius: 8px; font-size: 13px; font-weight: 800;
    }
    .score-high { background: rgba(16,185,129,0.1); color: #059669; }
    .score-med { background: rgba(245,158,11,0.1); color: #D97706; }
    .score-low { background: rgba(239,68,68,0.1); color: #DC2626; }

    /* Simple Photo Lightbox */
    #photoLightbox {
        position: fixed; inset: 0; z-index: 9999;
        display: flex; align-items: center; justify-content: center;
        background: rgba(10,5,30,0.85); 
        backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);
        opacity: 0; pointer-events: none;
        transition: opacity 0.3s ease;
    }
    #photoLightbox.open {
        opacity: 1; pointer-events: auto;
    }
    #photoLightbox img {
        max-width: 90%; max-height: 85vh; border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.4);
        transform: scale(0.9); transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    #photoLightbox.open img { transform: scale(1); }
    
    #photoLightbox .lb-close {
        position: fixed; top: 24px; right: 28px;
        width: 44px; height: 44px; border-radius: 50%;
        background: rgba(255,255,255,0.1); border: 1.5px solid rgba(255,255,255,0.2);
        color: #fff; font-size: 17px; display: flex; align-items: center; justify-content: center;
        cursor: pointer; transition: all 0.2s; z-index: 10000;
    }
    #photoLightbox .lb-close:hover { 
        background: rgba(239,68,68,0.6); border-color: rgba(239,68,68,0.5); transform: scale(1.1); 
    }

    #photoLightbox .lb-close {
        position: fixed; top: 24px; right: 28px;
        width: 44px; height: 44px; border-radius: 50%;
        background: rgba(255,255,255,0.10);
        border: 1.5px solid rgba(255,255,255,0.2);
        color: #fff; font-size: 17px;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; transition: all 0.2s; z-index: 10000;
        backdrop-filter: blur(8px);
    }
    #photoLightbox .lb-close:hover {
        background: rgba(239,68,68,0.6); border-color: rgba(239,68,68,0.5); transform: scale(1.1);
    }
</style>
@endsection

@section('content')
<div class="fade-up">
    <!-- Employee Header -->
    <div class="emp-header-card shadow-sm">
        @if($employee->avatar)
        <div class="avatar-photo-wrap"
             onclick="openPhotoLightbox('/storage/{{ $employee->avatar }}')"
             title="Klik untuk memperbesar foto">
            <img src="/storage/{{ $employee->avatar }}" alt="{{ $employee->name }}">
            <span class="zoom-hint"><i class="fa-solid fa-magnifying-glass-plus"></i></span>
        </div>
        @else
        <div class="emp-avatar">
            {{ $employee->name[0] }}
        </div>
        @endif

        <div style="flex:1;">
            <div style="display:flex; align-items:center; gap:8px; margin-bottom:4px;">
                <span style="font-size:11px; font-weight:800; background:rgba(124,58,237,0.1); color:var(--purple); padding:3px 8px; border-radius:6px; text-transform:uppercase;">{{ $employee->nim }}</span>
                <span style="font-size:11px; font-weight:800; background:#F3F4F6; color:#6B7280; padding:3px 8px; border-radius:6px; text-transform:uppercase;">{{ $employee->department }}</span>
            </div>
            <h2 style="font-size:22px; font-weight:900; color:#1E1B4B; margin:0;">{{ $employee->name }}</h2>
            <p style="font-size:13px; color:#6B7280; margin:2px 0 0 0;">{{ $employee->position }}</p>
        </div>

        <div style="text-align:right;">
            <div style="font-size:11px; font-weight:800; color:#94A3B8; text-transform:uppercase; margin-bottom:4px;">Total Kuis</div>
            <div style="font-size:24px; font-weight:900; color:#1E1B4B;">{{ $participations->count() }}</div>
        </div>
    </div>

    <!-- Achievements -->
    <div style="margin-bottom:32px;">
        <div style="font-size:14px; font-weight:800; color:#1E1B4B; margin-bottom:16px; display:flex; align-items:center; gap:8px;">
            <i class="fa-solid fa-medal" style="color:#F59E0B;"></i> Achievements & Badges
        </div>
        <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(240px, 1fr)); gap:16px;">
            @forelse($employee->achievements as $achievement)
                <div class="badge-card shadow-sm">
                    <div class="badge-icon" style="background:{{ $achievement->icon === 'trophy' ? 'rgba(245,158,11,0.1)' : 'rgba(124,58,237,0.1)' }}; color:{{ $achievement->icon === 'trophy' ? '#D97706' : 'var(--purple)' }};">
                        <i class="fa-solid fa-{{ $achievement->icon }}"></i>
                    </div>
                    <div>
                        <div style="font-size:13px; font-weight:800; color:#1E1B4B;">{{ $achievement->name }}</div>
                        <div style="font-size:11px; color:#6B7280;">{{ $achievement->description }}</div>
                    </div>
                </div>
            @empty
                <div style="grid-column:1/-1; padding:20px; background:#fff; border:1px dashed #E5E3F0; border-radius:14px; text-align:center; color:#9CA3AF; font-size:13px; font-weight:600;">
                    Belum ada achievement yang terbuka
                </div>
            @endforelse
        </div>
    </div>

    <div style="display:grid; grid-template-columns:1fr 400px; gap:24px; align-items:start;">
        <!-- LEFT: Performance History -->
        <div style="display:flex; flex-direction:column; gap:24px;">
            <div class="card" style="padding:0; overflow:hidden;">
                <div style="padding:18px 24px; border-bottom:1px solid #F3F2FB; font-size:14px; font-weight:800; color:#1E1B4B; display:flex; align-items:center; justify-content:space-between;">
                    Riwayat Penilaian
                    <span style="font-size:11px; font-weight:700; color:#94A3B8;">{{ $participations->count() }} records</span>
                </div>
                <div style="display:flex; flex-direction:column;">
                    @forelse($participations as $participation)
                        <a href="{{ route('admin.participant.answers', ['quiz' => $participation->quiz->slug, 'participant' => $participation->id]) }}" class="history-item">
                            <div style="display:flex; align-items:center; gap:16px;">
                                <div style="width:42px; height:42px; border-radius:12px; background:#F3F2FB; display:flex; align-items:center; justify-content:center; color:var(--purple); font-size:16px;">
                                    <i class="fa-solid fa-file-lines"></i>
                                </div>
                                <div>
                                    <div style="font-size:14px; font-weight:800; color:#1E1B4B; margin-bottom:2px;">{{ $participation->quiz->title }}</div>
                                    <div style="font-size:11px; font-weight:700; color:#94A3B8; display:flex; align-items:center; gap:8px;">
                                        <i class="fa-regular fa-calendar" style="font-size:10px;"></i> {{ $participation->updated_at->format('d M Y') }}
                                        <span style="width:3px; height:3px; background:#CBD5E1; border-radius:50%;"></span>
                                        <i class="fa-regular fa-clock" style="font-size:10px;"></i> {{ $participation->updated_at->format('H:i') }}
                                    </div>
                                </div>
                            </div>
                            <div style="display:flex; align-items:center; gap:16px;">
                                <div class="score-pill {{ $participation->score >= 80 ? 'score-high' : ($participation->score >= 60 ? 'score-med' : 'score-low') }}">
                                    {{ $participation->score }}
                                </div>
                                <i class="fa-solid fa-chevron-right" style="font-size:10px; color:#CBD5E1;"></i>
                            </div>
                        </a>
                    @empty
                        <div style="padding:48px 24px; text-align:center;">
                            <div style="font-size:32px; margin-bottom:12px;">📄</div>
                            <div style="font-size:14px; font-weight:700; color:#4B5563;">Belum ada riwayat</div>
                            <div style="font-size:12px; color:#9CA3AF;">Karyawan ini belum menyelesaikan kuis apapun.</div>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Kuis Belum Dikerjakan -->
            <div class="card" style="padding:0; overflow:hidden;">
                <div style="padding:18px 24px; border-bottom:1px solid #F3F2FB; font-size:14px; font-weight:800; color:#1E1B4B; display:flex; align-items:center; justify-content:space-between;">
                    Kuis Belum Dikerjakan
                    <span style="font-size:11px; font-weight:700; color:#DC2626;">{{ $uncompletedQuizzes->count() }} kuis</span>
                </div>
                <div style="display:flex; flex-direction:column;">
                    @forelse($uncompletedQuizzes as $uncompletedQuiz)
                        <div class="history-item" style="cursor:default;">
                            <div style="display:flex; align-items:center; gap:16px;">
                                <div style="width:42px; height:42px; border-radius:12px; background:rgba(220,38,38,0.05); display:flex; align-items:center; justify-content:center; color:#DC2626; font-size:16px;">
                                    <i class="fa-solid fa-clock-rotate-left"></i>
                                </div>
                                <div>
                                    <div style="font-size:14px; font-weight:800; color:#1E1B4B; margin-bottom:2px;">{{ $uncompletedQuiz->title }}</div>
                                    <div style="font-size:11px; font-weight:700; color:#94A3B8; display:flex; align-items:center; gap:8px;">
                                        <i class="fa-solid fa-hourglass-half" style="font-size:10px;"></i> Waktu: {{ $uncompletedQuiz->time_limit }} menit
                                    </div>
                                </div>
                            </div>
                            <div style="display:flex; align-items:center; gap:16px;">
                                <span style="font-size:11px; font-weight:700; color:#DC2626; background:rgba(220,38,38,0.1); padding:4px 10px; border-radius:8px;">Belum Mengerjakan</span>
                            </div>
                        </div>
                    @empty
                        <div style="padding:48px 24px; text-align:center;">
                            <div style="font-size:32px; margin-bottom:12px;">🎉</div>
                            <div style="font-size:14px; font-weight:700; color:#4B5563;">Semua kuis telah dikerjakan!</div>
                            <div style="font-size:12px; color:#9CA3AF;">Karyawan ini tidak memiliki tunggakan kuis.</div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- RIGHT: Growth Chart -->
        <div style="display:flex; flex-direction:column; gap:20px; position:sticky; top:88px;">
            <div class="card" style="padding:24px;">
                <div style="font-size:14px; font-weight:800; color:#1E1B4B; margin-bottom:24px; display:flex; align-items:center; gap:8px;">
                    <i class="fa-solid fa-chart-line" style="color:var(--purple);"></i> Tren Perkembangan
                </div>
                <div style="height:280px; position:relative;">
                    <canvas id="growthChart"></canvas>
                </div>
                <div style="margin-top:20px; padding-top:16px; border-top:1px solid #F3F2F6; display:flex; justify-content:space-between; align-items:center;">
                    <div style="font-size:11px; color:#6B7280; font-weight:600;">Status Terkini</div>
                    @php
                        $lastScore = $participations->first()->score ?? 0;
                        $prevScore = $participations->skip(1)->first()->score ?? 0;
                        $trend = $lastScore - $prevScore;
                    @endphp
                    @if($participations->count() >= 2)
                        <div style="font-size:12px; font-weight:800; color:{{ $trend >= 0 ? '#059669' : '#DC2626' }}; display:flex; align-items:center; gap:4px;">
                            <i class="fa-solid fa-arrow-{{ $trend >= 0 ? 'up' : 'down' }}"></i> {{ abs($trend) }} pts
                        </div>
                    @else
                        <div style="font-size:11px; font-weight:700; color:#94A3B8;">Data tidak cukup</div>
                    @endif
                </div>
            </div>

            <div class="card" style="padding:20px; background:linear-gradient(135deg, #1E1B4B 0%, #312E81 100%); border:none;">
                <div style="font-size:12px; font-weight:800; color:rgba(255,255,255,0.6); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:12px;">Data Integrity</div>
                <p style="font-size:12px; color:rgba(255,255,255,0.9); line-height:1.6; margin-bottom:0;">
                    Semua data nilai dan riwayat diambil langsung dari sistem penilaian tersertifikasi PahamAja.
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Simple Photo Lightbox Modal -->
<div id="photoLightbox" onclick="if(event.target===this) closePhotoLightbox()">
    <button class="lb-close" onclick="closePhotoLightbox()" title="Tutup">
        <i class="fa-solid fa-xmark"></i>
    </button>
    <img id="lbImg" src="" alt="Foto">
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Simple Photo Lightbox
function openPhotoLightbox(src) {
    document.getElementById('lbImg').src = src;
    document.getElementById('photoLightbox').classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closePhotoLightbox() {
    document.getElementById('photoLightbox').classList.remove('open');
    document.body.style.overflow = '';
    setTimeout(() => { const img = document.getElementById('lbImg'); if(img) img.src=''; }, 300);
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closePhotoLightbox(); });

document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('growthChart').getContext('2d');
    const data = @json($chartData);
    
    if (!data.scores || data.scores.length === 0) {
        // Fallback for no data
        ctx.font = "14px Plus Jakarta Sans";
        ctx.fillStyle = "#9CA3AF";
        ctx.textAlign = "center";
        ctx.fillText("Data tidak tersedia", ctx.canvas.width/2, ctx.canvas.height/2);
        return;
    }

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.labels,
            datasets: [{
                label: 'Nilai',
                data: data.scores,
                borderColor: '#7C3AED',
                backgroundColor: (context) => {
                    const chart = context.chart;
                    const {ctx, chartArea} = chart;
                    if (!chartArea) return null;
                    const gradient = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
                    gradient.addColorStop(0, 'rgba(124, 58, 237, 0)');
                    gradient.addColorStop(1, 'rgba(124, 58, 237, 0.1)');
                    return gradient;
                },
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#7C3AED',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1E1B4B',
                    padding: 12,
                    titleFont: { family: 'Plus Jakarta Sans', size: 12, weight: 'bold' },
                    bodyFont: { family: 'Plus Jakarta Sans', size: 12 },
                    displayColors: false,
                    callbacks: {
                        title: (items) => data.fullLabels ? data.fullLabels[items[0].dataIndex] : items[0].label,
                        label: (item) => 'Skor: ' + item.raw
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    grid: { color: '#F3F2F6', drawBorder: false },
                    ticks: { 
                        font: { family: 'Plus Jakarta Sans', weight: 'bold', size: 10 },
                        color: '#94A3B8',
                        padding: 8
                    }
                },
                x: {
                    grid: { display: false, drawBorder: false },
                    ticks: { 
                        font: { family: 'Plus Jakarta Sans', weight: 'bold', size: 10 },
                        color: '#94A3B8',
                        padding: 8
                    }
                }
            }
        }
    });
});
</script>
<script src="{{ asset('js/prevent-double-submit.js') }}"></script>
@endsection
