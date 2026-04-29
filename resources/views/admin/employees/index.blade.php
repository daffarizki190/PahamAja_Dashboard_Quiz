@extends('layouts.app')

@section('title', 'Data Karyawan – PahamAja')
@section('meta_description', 'Kelola data karyawan dan pantau performa assessment')
@section('search_placeholder', 'Cari nama atau NIM karyawan...')
@section('show_search', true)

@section('head_extra')
<style>
    /* ── Employee Card ── */
    .emp-card {
        background: #fff; border: 1px solid #E5E3F0; border-radius: 20px;
        padding: 24px 22px; transition: all 0.2s;
        box-shadow: 0 4px 16px rgba(0,0,0,0.03);
        display: flex; flex-direction: column;
        min-width: 0;
    }
    .emp-card:hover { transform: translateY(-4px); box-shadow: 0 12px 30px rgba(0,0,0,0.08); border-color: rgba(124,58,237,0.3); }

    /* ── 3D Avatar Wrapper ── */
    .emp-avatar {
        width: 60px; height: 60px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 24px; font-weight: 800; color: #fff; flex-shrink: 0;
        position: relative;
    }

    /* Clickable avatar with 3D photo effect */
    .avatar-photo-wrap {
        position: relative;
        width: 60px; height: 60px;
        flex-shrink: 0;
        cursor: pointer;
    }
    .avatar-photo-wrap img {
        width: 100%; height: 100%; object-fit: cover;
        border-radius: 50%;
        transition: transform 0.35s cubic-bezier(.34,1.56,.64,1), box-shadow 0.35s;
        display: block;
    }
    /* If PNG with transparency: 3D floating effect */
    .avatar-photo-wrap.has-photo img {
        border-radius: 50%;
        box-shadow:
            0 2px 0 rgba(0,0,0,0.06),
            0 6px 16px rgba(0,0,0,0.18),
            0 14px 32px rgba(0,0,0,0.10),
            0 0 0 3px rgba(255,255,255,0.9),
            0 0 0 5px rgba(124,58,237,0.12);
        transform: perspective(400px) rotateX(3deg) translateY(-2px) scale(1.02);
        filter: drop-shadow(0 8px 16px rgba(80,40,180,0.22));
    }
    .avatar-photo-wrap.has-photo:hover img {
        transform: perspective(400px) rotateX(6deg) translateY(-6px) scale(1.07);
        box-shadow:
            0 4px 0 rgba(0,0,0,0.08),
            0 12px 28px rgba(80,40,180,0.3),
            0 24px 48px rgba(0,0,0,0.12),
            0 0 0 3px rgba(255,255,255,1),
            0 0 0 6px rgba(124,58,237,0.25);
        filter: drop-shadow(0 12px 24px rgba(80,40,180,0.35));
    }
    /* Zoom hint icon */
    .avatar-photo-wrap .zoom-hint {
        position: absolute; bottom: -2px; right: -2px;
        width: 20px; height: 20px; border-radius: 50%;
        background: linear-gradient(135deg, #7C3AED, #5B21B6);
        display: flex; align-items: center; justify-content: center;
        color: #fff; font-size: 9px;
        box-shadow: 0 2px 8px rgba(124,58,237,0.4);
        opacity: 0; transform: scale(0.6);
        transition: opacity 0.2s, transform 0.2s;
        pointer-events: none;
    }
    .avatar-photo-wrap:hover .zoom-hint { opacity: 1; transform: scale(1); }

    .emp-name {
        font-size: 16px; font-weight: 800; color: #1E1B4B;
        margin-top: 18px; line-height: 1.3; overflow: hidden;
        text-overflow: ellipsis; white-space: nowrap;
    }
    .emp-id { font-size: 13px; font-weight: 600; color: #9CA3AF; margin-top: 4px; }

    .emp-bottom { margin-top: 28px; display: flex; justify-content: space-between; align-items: flex-end; }

    /* SVG Donut text */
    .donut-container { position: relative; width: 56px; height: 56px; }
    .donut-svg { transform: rotate(-90deg); width: 100%; height: 100%; }
    .donut-text {
        position: absolute; inset: 0; display: flex; align-items: center; justify-content: center;
        font-size: 13px; font-weight: 800;
    }

    .btn-icon {
        width: 32px; height: 32px; border-radius: 8px;
        background: #F3F2FB; border: 1px solid #E5E3F0;
        display: flex; align-items: center; justify-content: center;
        color: #4B5563; font-size: 11px; cursor: pointer; transition: all 0.2s;
        text-decoration: none; border: none; padding: 0;
    }
    .btn-icon:hover { background: #EDE9FE; color: #7C3AED; border-color: #7C3AED; }
    .btn-icon.text-danger:hover { background: rgba(239,68,68,0.1); color: #DC2626; border-color: rgba(239,68,68,0.2); }

    .card-actions { display: flex; align-items: center; gap: 6px; }
    .card-actions form { margin: 0; display: flex; }

    /* Responsive Grid */
    .emp-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 22px;
    }
    @media (max-width: 640px) {
        .emp-grid { grid-template-columns: 1fr; }
    }

    /* ── Photo Lightbox ── */
    #photoLightbox {
        position: fixed; inset: 0; z-index: 9999;
        display: flex; align-items: center; justify-content: center;
        background: rgba(10, 8, 30, 0.0);
        backdrop-filter: blur(0px);
        -webkit-backdrop-filter: blur(0px);
        opacity: 0; pointer-events: none;
        transition: opacity 0.3s ease, background 0.3s ease;
    }
    #photoLightbox.open {
        opacity: 1; pointer-events: all;
        background: rgba(10, 8, 30, 0.82);
        backdrop-filter: blur(18px);
        -webkit-backdrop-filter: blur(18px);
    }
    #photoLightbox .lb-inner {
        position: relative;
        transform: scale(0.7) translateY(30px);
        transition: transform 0.4s cubic-bezier(.34,1.56,.64,1);
    }
    #photoLightbox.open .lb-inner {
        transform: scale(1) translateY(0);
    }
    #photoLightbox .lb-img {
        max-width: 86vw; max-height: 82vh;
        width: auto; height: auto;
        border-radius: 24px;
        display: block;
        /* 3D depth shadow for photo in lightbox */
        box-shadow:
            0 0 0 4px rgba(255,255,255,0.12),
            0 0 0 8px rgba(124,58,237,0.18),
            0 30px 80px rgba(0,0,0,0.6),
            0 8px 24px rgba(80,40,180,0.4);
        filter: drop-shadow(0 20px 40px rgba(80,40,180,0.4));
        transform: perspective(900px) rotateX(2deg);
        transition: transform 0.5s cubic-bezier(.34,1.56,.64,1);
    }
    #photoLightbox.open .lb-img { transform: perspective(900px) rotateX(0deg); }
    #photoLightbox .lb-name {
        margin-top: 20px;
        text-align: center;
        color: #fff;
        font-size: 18px;
        font-weight: 800;
        text-shadow: 0 2px 12px rgba(0,0,0,0.5);
        letter-spacing: 0.02em;
    }
    #photoLightbox .lb-sub {
        text-align: center;
        color: rgba(255,255,255,0.55);
        font-size: 13px;
        font-weight: 600;
        margin-top: 4px;
    }
    #photoLightbox .lb-close {
        position: absolute; top: -18px; right: -18px;
        width: 40px; height: 40px; border-radius: 50%;
        background: rgba(255,255,255,0.15);
        border: 1.5px solid rgba(255,255,255,0.3);
        color: #fff; font-size: 16px;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; transition: all 0.2s;
        backdrop-filter: blur(8px);
    }
    #photoLightbox .lb-close:hover {
        background: rgba(239,68,68,0.7);
        border-color: rgba(239,68,68,0.5);
        transform: scale(1.1);
    }
    /* Floating particles in lightbox */
    #photoLightbox .lb-glow {
        position: absolute; border-radius: 50%;
        background: radial-gradient(circle, rgba(124,58,237,0.25) 0%, transparent 70%);
        pointer-events: none;
    }
</style>
@endsection

@section('content')
<!-- Header Area -->
<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:28px; flex-wrap:wrap; gap:16px;">
    <div style="display:flex; align-items:center; gap:24px;">
        <h1 style="font-size:26px; font-weight:900; color:#1E1B4B; margin:0;">Direktori Karyawan</h1>
        <!-- Group Illustration placeholder (matches the mock up feel) -->
        <div style="height:60px; filter:drop-shadow(0 4px 6px rgba(0,0,0,0.1));">
            <svg viewBox="0 0 200 60" height="100%" fill="none" xmlns="http://www.w3.org/2000/svg">
                <!-- Decorative people/group illustration -->
                <!-- Left person -->
                <circle cx="40" cy="20" r="10" fill="#FDE68A"/>
                <rect x="25" y="32" width="30" height="28" rx="8" fill="#3B82F6"/>
                <!-- Center person -->
                <circle cx="100" cy="15" r="12" fill="#FCA5A5"/>
                <rect x="80" y="29" width="40" height="31" rx="10" fill="#7C3AED"/>
                <!-- Right person -->
                <circle cx="160" cy="22" r="10" fill="#A78BFA"/>
                <rect x="145" y="34" width="30" height="26" rx="8" fill="#10B981"/>
                <!-- Connecting lines/charts -->
                <path d="M50 40 L80 20 L110 35 L140 15" stroke="#F59E0B" stroke-width="3" stroke-linecap="round"/>
                <circle cx="80" cy="20" r="4" fill="#F59E0B"/>
                <circle cx="110" cy="35" r="4" fill="#F59E0B"/>
            </svg>
        </div>
    </div>

    <!-- Export & Add actions -->
    <div style="display:flex; align-items:center; gap:12px;">
        <a href="{{ route('admin.reports.global-excel') }}" class="btn btn-ghost" title="Export Excel" style="padding:10px 14px; border-radius:12px;">
            <i class="fa-solid fa-file-excel" style="color:#10B981; font-size:16px;"></i>
        </a>
        <a href="{{ route('admin.reports.global-pdf') }}" class="btn btn-ghost" title="Export PDF" style="padding:10px 14px; border-radius:12px;">
            <i class="fa-solid fa-file-pdf" style="color:#EF4444; font-size:16px;"></i>
        </a>
        <button onclick="openRegisterModal()" class="btn btn-primary" style="padding:10px 20px; font-size:14px; border-radius:12px;">
            <i class="fa-solid fa-plus"></i> <span style="margin-left:6px;">Tambah</span>
        </button>
    </div>
</div>

<!-- Content Info -->
<div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:28px;" class="fade-up">
    <div>
        <p style="font-size:14px; font-weight:600; color:#6B7280; margin:0;">Total <span style="color:#7C3AED;">{{ count($employees) }}</span> Karyawan Terdaftar</p>
    </div>
</div>
    <!-- Dropdowns like reference -->
    <div style="display:flex; gap:12px;">
        <div style="position:relative;">
            <select class="form-input" style="padding:12px 38px 12px 20px; border-radius:12px; border:none; background:#6D28D9; color:#fff; font-size:14px; font-weight:700; cursor:pointer; appearance:none;">
                <option>Departemen</option>
            </select>
            <i class="fa-solid fa-chevron-down" style="position:absolute; right:16px; top:50%; transform:translateY(-50%); color:#fff; font-size:11px; pointer-events:none;"></i>
        </div>
        <div style="position:relative;">
            <select class="form-input" style="padding:12px 38px 12px 20px; border-radius:12px; border:1px solid #E5E3F0; background:#EAE7F2; color:#1E1B4B; font-size:14px; font-weight:700; cursor:pointer; appearance:none;">
                <option>Performa</option>
            </select>
            <i class="fa-solid fa-chevron-down" style="position:absolute; right:16px; top:50%; transform:translateY(-50%); color:#6B7280; font-size:11px; pointer-events:none;"></i>
        </div>
    </div>
</div>

<!-- Employee Grid -->
<div class="emp-grid fade-up" id="empGrid">
    @forelse($employees as $idx => $employee)
    @php
        $avg = min(100, max(0, $statsByNim[$employee->nim]['avg'] ?? 0));
        $initials = strtoupper(mb_substr($employee->name, 0, 1)) . (str_contains($employee->name, ' ') ? strtoupper(mb_substr(explode(' ', $employee->name)[1], 0, 1)) : '');
        
        // Emulate reference colors
        $palette = [
            ['bg' => '#3B82F6', 'ring' => '#3B82F6'], // Blue
            ['bg' => '#8B5CF6', 'ring' => '#8B5CF6'], // Purple
            ['bg' => '#10B981', 'ring' => '#10B981'], // Green
            ['bg' => '#F97316', 'ring' => '#F97316'], // Orange
            ['bg' => '#EF4444', 'ring' => '#EF4444'], // Red
            ['bg' => '#06B6D4', 'ring' => '#06B6D4'], // Cyan
        ];
        $theme = $palette[$idx % count($palette)];
        
        // Emulate reference emojis
        $emojis = ['🌟','🏆','📈','🎯','⚙️','💡','📋','🚀'];
        $emoji = $emojis[$idx % count($emojis)];

        // SVG Ring Math
        $r = 23;
        $circ = 2 * pi() * $r;
        $offset = $circ - ($avg / 100 * $circ);
    @endphp
    <div class="emp-card card-3d" data-name="{{ strtolower($employee->name) }}" data-nim="{{ strtolower($employee->nim) }}" data-tilt data-tilt-max="10" data-tilt-speed="400" data-tilt-glare="true" data-tilt-max-glare="0.1">
        <!-- Header -->
        <div style="display:flex; justify-content:space-between; align-items:flex-start;">
            <div class="avatar-photo-wrap has-photo"
                 onclick="openPhotoLightbox('{{ avatar_url($employee->avatar, $employee->name) }}', '{{ addslashes($employee->name) }}', '{{ addslashes($employee->position) }} · {{ addslashes($employee->department) }}')"
                 title="Klik untuk memperbesar foto">
                <img src="{{ avatar_url($employee->avatar, $employee->name) }}" alt="{{ $employee->name }}" loading="lazy">
                <span class="zoom-hint"><i class="fa-solid fa-magnifying-glass-plus"></i></span>
            </div>
            <div class="card-actions">
                <a href="{{ route('admin.employees.show', $employee->id) }}" class="btn-icon" title="Lihat Analitik"><i class="fa-solid fa-chart-column"></i></a>
                <button onclick="openEditModalFromButton(this)" data-employee='@json($employee)' class="btn-icon" title="Edit"><i class="fa-solid fa-pen" style="font-size:11px;"></i></button>
                <form action="{{ route('admin.employees.destroy', $employee->id) }}" method="POST" 
                      onsubmit="event.preventDefault(); PahamAja.confirm('Hapus Karyawan', 'Anda yakin ingin menghapus karyawan ini? Seluruh data riwayat kuis dan nilai yang bersangkutan akan ikut terhapus secara permanen.', 'danger', () => this.submit())">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-icon text-danger" title="Hapus"><i class="fa-solid fa-trash" style="font-size:11px;"></i></button>
                </form>
            </div>
        </div>

        <!-- Info -->
        <div class="emp-name" title="{{ $employee->name }}">{{ $employee->name }}</div>
        <div class="emp-id">ID {{ $employee->nim }}</div>

        <!-- Progress ring & Emoji -->
        <div class="emp-bottom">
            <div class="donut-container">
                <svg viewBox="0 0 56 56" class="donut-svg">
                    <circle cx="28" cy="28" r="{{ $r }}" fill="none" stroke="#F3F2FB" stroke-width="6"/>
                    <circle cx="28" cy="28" r="{{ $r }}" fill="none" stroke="{{ $theme['ring'] }}" stroke-width="6" stroke-linecap="round" stroke-dasharray="{{ $circ }}" stroke-dashoffset="{{ $offset }}"/>
                </svg>
                <div class="donut-text" style="color:{{ $theme['ring'] }}">{{ number_format($avg, 0) }}%</div>
            </div>
            <!-- 3D style Emoji -->
            <div style="font-size:46px; filter:drop-shadow(2px 6px 8px rgba(0,0,0,0.15)); line-height:1;">
                {{ $emoji }}
            </div>
        </div>
    </div>
    @empty
    <div style="grid-column:1/-1; text-align:center; padding:80px 20px;">
        <div style="font-size:60px; margin-bottom:16px;">👤</div>
        <div style="font-size:16px; color:#6B7280; font-weight:600; margin-bottom:16px;">Belum ada karyawan terdaftar</div>
        <button onclick="openRegisterModal()" class="btn btn-primary" style="font-size:14px; padding:12px 24px;">
            <i class="fa-solid fa-plus"></i> <span style="margin-left:8px;">Tambah Karyawan</span>
        </button>
    </div>
    @endforelse
</div>

<!-- Edit Modal -->
<div id="editModal" class="modal-overlay" onclick="if(event.target===this) closeEditModal()">
    <div class="modal-box" style="max-width:480px;">
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:24px;">
            <div>
                <h3 style="font-size:18px; font-weight:900; color:#1E1B4B; margin-bottom:4px;">Edit Karyawan</h3>
                <p style="font-size:12px; color:#6B7280;">Perbarui data karyawan</p>
            </div>
            <button onclick="closeEditModal()" class="btn btn-ghost" style="padding:8px 10px;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form id="editForm" method="POST" class="space-y-4" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div style="display:flex; align-items:center; gap:16px; margin-bottom:20px;">
                <div id="editAvatarPreview" style="width:70px; height:70px; border-radius:50%; background:#F3F2FB; display:flex; align-items:center; justify-content:center; font-size:24px; color:#7C3AED; border:2px solid #E5E3F0; overflow:hidden;">
                    <i class="fa-solid fa-user"></i>
                </div>
                <div style="flex:1;">
                    <label class="form-label">Foto Profil (Optional)</label>
                    <input type="file" name="avatar" class="form-input" accept="image/*" onchange="previewImage(this, 'editAvatarPreview')">
                </div>
            </div>
            <div>
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="name" id="edit_name" required class="form-input" placeholder="Nama karyawan...">
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                <div>
                    <label class="form-label">Departemen</label>
                    <input type="text" name="department" id="edit_dept" required class="form-input" placeholder="Departemen...">
                </div>
                <div>
                    <label class="form-label">Posisi</label>
                    <input type="text" name="position" id="edit_pos" required class="form-input" placeholder="Posisi...">
                </div>
            </div>
            <div>
                <label class="form-label">Status</label>
                <select name="status" id="edit_status" class="form-input">
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center; padding:13px;">
                <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
            </button>
        </form>
    </div>
</div>

<!-- Register Modal -->
<div id="registerModal" class="modal-overlay" onclick="if(event.target===this) closeRegisterModal()">
    <div class="modal-box" style="max-width:480px;">
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:24px;">
            <div>
                <h3 style="font-size:18px; font-weight:900; color:#1E1B4B; margin-bottom:4px;">Tambah Karyawan</h3>
                <p style="font-size:12px; color:#6B7280;">Daftarkan karyawan baru ke sistem</p>
            </div>
            <button onclick="closeRegisterModal()" class="btn btn-ghost" style="padding:8px 10px;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form action="{{ route('admin.employees.store') }}" method="POST" class="space-y-4" enctype="multipart/form-data">
            @csrf
            <div style="display:flex; align-items:center; gap:16px; margin-bottom:20px;">
                <div id="regAvatarPreview" style="width:70px; height:70px; border-radius:50%; background:#F3F2FB; display:flex; align-items:center; justify-content:center; font-size:24px; color:#7C3AED; border:2px solid #E5E3F0; overflow:hidden;">
                    <i class="fa-solid fa-user"></i>
                </div>
                <div style="flex:1;">
                    <label class="form-label">Foto Profil (Optional)</label>
                    <input type="file" name="avatar" class="form-input" accept="image/*" onchange="previewImage(this, 'regAvatarPreview')">
                </div>
            </div>
            <div>
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="name" required class="form-input" placeholder="Contoh: Budi Santoso">
            </div>
            <div>
                <label class="form-label">ID Karyawan (NIM)</label>
                <input type="text" name="nim" required class="form-input" placeholder="Contoh: CP-2024-001">
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                <div>
                    <label class="form-label">Departemen</label>
                    <input type="text" name="department" required class="form-input" placeholder="Contoh: IT">
                </div>
                <div>
                    <label class="form-label">Posisi</label>
                    <input type="text" name="position" required class="form-input" placeholder="Contoh: Staff">
                </div>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center; padding:13px;">
                <i class="fa-solid fa-user-plus"></i> <span style="margin-left:6px;">Daftarkan Karyawan</span>
            </button>
        </form>
    </div>
</div>

<!-- ── Photo Lightbox Modal ── -->
<div id="photoLightbox" onclick="if(event.target===this) closePhotoLightbox()">
    <!-- Glow blobs -->
    <div class="lb-glow" style="width:400px; height:400px; top:-100px; left:-80px; opacity:0.6;"></div>
    <div class="lb-glow" style="width:300px; height:300px; bottom:-60px; right:-60px; opacity:0.4;"></div>
    <div class="lb-inner">
        <button class="lb-close" onclick="closePhotoLightbox()" title="Tutup">
            <i class="fa-solid fa-xmark"></i>
        </button>
        <img id="lbImg" class="lb-img" src="" alt="Foto Karyawan">
        <div class="lb-name" id="lbName"></div>
        <div class="lb-sub" id="lbSub"></div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/prevent-double-submit.js') }}"></script>
<script>
// Search
window.addEventListener('pahamaja-search', function (e) {
    const q = e.detail.toLowerCase().trim();
    document.querySelectorAll('#empGrid .emp-card').forEach(card => {
        const name = card.dataset.name ? card.dataset.name.toLowerCase() : '';
        const nim  = card.dataset.nim ? card.dataset.nim.toLowerCase() : '';
        const dept = card.querySelector('.emp-id + div') ? card.querySelector('.emp-id + div').textContent.toLowerCase() : '';
        const pos  = card.querySelector('.badge-aktif') ? card.querySelector('.badge-aktif').textContent.toLowerCase() : '';
        
        const matches = !q || name.includes(q) || nim.includes(q) || dept.includes(q) || pos.includes(q);
        card.style.display = matches ? '' : 'none';
    });
});

// Image Preview
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `<img src="${e.target.result}" style="width:100%; height:100%; object-fit:cover;">`;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// ── Photo Lightbox ──
function openPhotoLightbox(src, name, sub) {
    const lb   = document.getElementById('photoLightbox');
    const img  = document.getElementById('lbImg');
    const nm   = document.getElementById('lbName');
    const sb   = document.getElementById('lbSub');
    img.src    = src;
    nm.textContent = name || '';
    sb.textContent = sub  || '';
    lb.classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closePhotoLightbox() {
    document.getElementById('photoLightbox').classList.remove('open');
    document.body.style.overflow = '';
    // Clear src after animation
    setTimeout(() => {
        const img = document.getElementById('lbImg');
        if (img) img.src = '';
    }, 350);
}
// ESC key closes lightbox
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closePhotoLightbox();
});

// Modal functions
function openRegisterModal() {
    document.getElementById('regAvatarPreview').innerHTML = '<i class="fa-solid fa-user"></i>';
    document.getElementById('registerModal').classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeRegisterModal() {
    document.getElementById('registerModal').classList.remove('open');
    document.body.style.overflow = '';
}
function openEditModal(employee) {
    document.getElementById('edit_name').value   = employee.name;
    document.getElementById('edit_dept').value   = employee.department;
    document.getElementById('edit_pos').value    = employee.position;
    document.getElementById('edit_status').value = employee.status || 'Active';
    document.getElementById('editForm').action   = `/admin/employees/${employee.id}`;
    
    // Avatar Preview in Edit
    const preview = document.getElementById('editAvatarPreview');
    if (employee.avatar_url) {
        preview.innerHTML = `<img src="${employee.avatar_url}" style="width:100%; height:100%; object-fit:cover;">`;
    } else {
        preview.innerHTML = '<i class="fa-solid fa-user"></i>';
    }

    document.getElementById('editModal').classList.add('open');
    document.body.style.overflow = 'hidden';
}
function openEditModalFromButton(btn) {
    const raw = btn?.dataset?.employee;
    if (!raw) return;
    openEditModal(JSON.parse(raw));
}
function closeEditModal() {
    document.getElementById('editModal').classList.remove('open');
    document.body.style.overflow = '';
}
</script>
@endsection
