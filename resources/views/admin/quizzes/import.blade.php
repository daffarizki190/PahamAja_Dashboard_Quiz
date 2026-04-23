@extends('layouts.app')

@section('title', 'Import Asesmen – PahamAja')
@section('meta_description', 'Import soal kuis dari file Excel atau CSV')
@section('page_title', 'Import Asesmen')
@section('page_subtitle', 'Upload file untuk memuat soal secara massal')

@section('topbar_left')
    <a href="{{ route('admin.quizzes.index') }}" class="btn btn-ghost" style="padding:9px 12px; font-size:12px; background:rgba(124,58,237,0.08); border:1px solid rgba(124,58,237,0.15); color:var(--purple); margin-right:8px;">
        <i class="fa-solid fa-arrow-left"></i> Kembali
    </a>
@endsection

@section('topbar_actions')
@endsection

@section('head_extra')
<style>
    .upload-zone {
        border: 2px dashed rgba(124,58,237,0.35); border-radius: 18px;
        padding: 52px 32px; text-align: center; cursor: pointer;
        transition: all 0.22s; background: rgba(124,58,237,0.04); position: relative;
    }
    .upload-zone:hover, .upload-zone.dragging {
        border-color: #7C3AED; background: rgba(124,58,237,0.08);
    }
    .format-pill {
        display: inline-flex; align-items: center; gap: 6px;
        border-radius: 7px; padding: 5px 12px; font-size: 12px; font-weight: 800;
        cursor: default;
    }
    .pill-csv  { background: #10B981; color: #fff; }
    .pill-json { background: #F59E0B; color: #fff; }

    /* Range slider style */
    input[type=range] {
        -webkit-appearance: none; width: 100%; height: 5px;
        background: #E5E3F0; border-radius: 99px; outline: none;
    }
    input[type=range]::-webkit-slider-thumb {
        -webkit-appearance: none; width: 18px; height: 18px;
        background: #7C3AED; border-radius: 50%; cursor: pointer;
        box-shadow: 0 2px 8px rgba(124,58,237,0.4);
    }
    .slider-label { display: flex; justify-content: space-between; font-size: 11px; color: #9CA3AF; font-weight: 600; margin-top: 4px; }
</style>
@endsection

@section('content')
<!-- Page Header with illustration -->
<div style="display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:28px;">
    <div>
        <h2 style="font-size:26px; font-weight:900; color:#1E1B4B; margin-bottom:4px;">Import Asesmen</h2>
        <p style="font-size:13px; color:#6B7280;">Upload file soal CSV atau JSON untuk membuat kuis secara massal</p>
    </div>
    <!-- Illustration top right -->
    <div style="flex-shrink:0; margin-left:20px;">
        <svg viewBox="0 0 180 120" fill="none" xmlns="http://www.w3.org/2000/svg" style="width:160px; height:auto;">
            <!-- Folders -->
            <rect x="100" y="20" width="60" height="45" rx="6" fill="#7C3AED" opacity=".8"/>
            <rect x="95" y="25" width="60" height="50" rx="6" fill="#4F46E5"/>
            <rect x="90" y="30" width="62" height="52" rx="6" fill="#6D28D9"/>
            <!-- Arrow up -->
            <circle cx="155" cy="45" r="14" fill="#F0FFF4" stroke="#10B981" stroke-width="1.5"/>
            <path d="M155 52 L155 39 M150 44 L155 39 L160 44" stroke="#10B981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <!-- Documents left -->
            <rect x="20" y="40" width="36" height="48" rx="5" fill="#fff" stroke="#E5E3F0" stroke-width="1.5"/>
            <rect x="26" y="50" width="24" height="3" rx="1.5" fill="#C4BFDF"/>
            <rect x="26" y="57" width="20" height="3" rx="1.5" fill="#E5E3F0"/>
            <rect x="26" y="64" width="22" height="3" rx="1.5" fill="#E5E3F0"/>
            <rect x="26" y="71" width="18" height="3" rx="1.5" fill="#E5E3F0"/>
            <!-- Arrow lines connecting -->
            <path d="M58 65 C75 55, 82 55, 90 52" stroke="#7C3AED" stroke-width="1.5" stroke-dasharray="3,2" stroke-linecap="round"/>
        </svg>
    </div>
</div>

<div style="display:grid; grid-template-columns:1fr 320px; gap:20px;" class="fade-up">

    <!-- LEFT: Dropzone -->
    <div>
        @if(session('error'))
        <div style="background:rgba(239,68,68,0.08); border:1px solid rgba(239,68,68,0.2); color:#DC2626; padding:12px 16px; border-radius:10px; margin-bottom:18px; font-size:13px; font-weight:700;">
            <i class="fa-solid fa-triangle-exclamation" style="margin-right:6px;"></i>{{ session('error') }}
        </div>
        @endif
        @if($errors->any())
        <div style="background:rgba(239,68,68,0.08); border:1px solid rgba(239,68,68,0.2); color:#DC2626; padding:12px 16px; border-radius:10px; margin-bottom:18px; font-size:13px; font-weight:700;">
            @foreach($errors->all() as $err)<div>{{ $err }}</div>@endforeach
        </div>
        @endif

        <form action="{{ route('admin.quizzes.import.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Dropzone -->
            <div class="upload-zone" id="uploadZone">
                <input type="file" name="file" id="fileInput" accept=".xlsx,.xls,.csv,.json" required
                       style="position:absolute; inset:0; opacity:0; cursor:pointer; z-index:10;"
                       onchange="onFileChange(this)">
                <!-- Cloud upload illustration -->
                <div style="margin-bottom:16px;">
                    <svg viewBox="0 0 80 60" fill="none" xmlns="http://www.w3.org/2000/svg" style="width:80px; height:60px; margin:0 auto; display:block;">
                        <ellipse cx="40" cy="35" rx="28" ry="18" fill="rgba(56,189,248,0.15)" stroke="rgba(56,189,248,0.4)" stroke-width="1.5"/>
                        <ellipse cx="25" cy="40" rx="14" ry="10" fill="rgba(124,58,237,0.1)" stroke="rgba(124,58,237,0.3)" stroke-width="1.5"/>
                        <ellipse cx="55" cy="38" rx="12" ry="8" fill="rgba(56,189,248,0.1)" stroke="rgba(56,189,248,0.3)" stroke-width="1.5"/>
                        <path d="M40 30 L40 14 M34 20 L40 14 L46 20" stroke="#7C3AED" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <!-- Floating docs -->
                        <rect x="8" y="12" width="14" height="18" rx="3" fill="#EF4444" opacity=".7" transform="rotate(-15 8 12)"/>
                        <rect x="58" y="10" width="14" height="18" rx="3" fill="#EF4444" opacity=".7" transform="rotate(15 58 10)"/>
                        <rect x="12" y="32" width="10" height="13" rx="2" fill="#EF4444" opacity=".5" transform="rotate(-10 12 32)"/>
                    </svg>
                </div>
                <div id="fileName" style="font-size:15px; font-weight:800; color:#1E1B4B; margin-bottom:6px;">
                    Seret & Lepas file di sini
                </div>
                <div style="font-size:12px; color:#6B7280; font-weight:500; margin-bottom:16px;">atau Pilih File</div>
                <div style="display:flex; gap:8px; justify-content:center; margin-bottom:12px;">
                    <span class="format-pill pill-csv">CSV</span>
                    <span class="format-pill pill-json">JSON</span>
                </div>
                <a href="#" style="font-size:13px; font-weight:700; color:#7C3AED; text-decoration:none;" onclick="return false;">Unduh Template</a>
            </div>

            <!-- Import button -->
            <button type="submit" class="btn btn-primary" id="importBtn"
                    style="width:100%; justify-content:center; padding:14px; font-size:14px; border-radius:12px; margin-top:16px;">
                Import Asesmen
            </button>
        </form>
    </div>

    <!-- RIGHT: Detail Asesmen -->
    <div>
        <div class="card" style="padding:24px;">
            <div style="font-size:15px; font-weight:800; color:#1E1B4B; margin-bottom:20px;">Detail Asesmen</div>

            <form>
                <!-- Judul Asesmen -->
                <div style="margin-bottom:18px;">
                    <label style="font-size:12px; font-weight:700; color:#1E1B4B; display:flex; align-items:center; gap:7px; margin-bottom:8px;">
                        <i class="fa-solid fa-pencil" style="color:#7C3AED; font-size:11px;"></i> Judul Asesmen
                    </label>
                    <input type="text" class="form-input" placeholder="Nama kuis..." name="title" value="{{ old('title') }}">
                </div>

                <!-- Durasi -->
                <div style="margin-bottom:20px;">
                    <label style="font-size:12px; font-weight:700; color:#1E1B4B; display:flex; align-items:center; gap:7px; margin-bottom:10px;">
                        <i class="fa-regular fa-clock" style="color:#F59E0B; font-size:13px;"></i> Durasi (Menit)
                    </label>
                    <input type="range" id="durasiSlider" min="5" max="120" value="30" oninput="document.getElementById('durasiVal').textContent=this.value">
                    <div class="slider-label">
                        <span>5 menit</span>
                        <span id="durasiVal" style="font-weight:700; color:#7C3AED; font-size:12px;">30</span>
                        <span>120 menit</span>
                    </div>
                </div>

                <!-- Nilai Kelulusan -->
                <div style="margin-bottom:8px;">
                    <label style="font-size:12px; font-weight:700; color:#1E1B4B; display:flex; align-items:center; gap:7px; margin-bottom:10px;">
                        <i class="fa-solid fa-trophy" style="color:#D97706; font-size:12px;"></i> Nilai Kelulusan (0-100)
                    </label>
                    <input type="range" id="nilaiSlider" min="0" max="100" value="70" oninput="document.getElementById('nilaiVal').textContent=this.value">
                    <div class="slider-label">
                        <span>0</span>
                        <span id="nilaiVal" style="font-weight:700; color:#7C3AED; font-size:12px;">70</span>
                        <span>100</span>
                    </div>
                </div>
            </form>
        </div>

        <!-- Format Guide -->
        <div class="card" style="padding:20px; margin-top:16px;">
            <div style="font-size:12px; font-weight:800; color:#1E1B4B; margin-bottom:12px;">
                <i class="fa-solid fa-circle-info" style="color:#7C3AED; margin-right:5px;"></i> Format Kolom
            </div>
            <div style="display:flex; flex-direction:column; gap:8px; font-size:11px;">
                @foreach([
                    ['question','Pertanyaan',true],
                    ['option_a','Opsi A',true],
                    ['option_b','Opsi B',true],
                    ['correct_answer','Jawaban (A/B/C/D)',true],
                    ['explanation','Penjelasan',false],
                ] as [$col, $label, $req])
                <div style="display:flex; align-items:center; gap:8px;">
                    <code style="background:#F3F2FB; border:1px solid #E5E3F0; border-radius:5px; padding:2px 7px; font-size:10px; color:#7C3AED; font-family:monospace; flex-shrink:0;">{{ $col }}</code>
                    <span style="color:#6B7280; flex:1;">{{ $label }}</span>
                    @if($req)
                        <span style="font-size:9px; font-weight:800; color:#DC2626; border:1px solid rgba(220,38,38,0.2); border-radius:4px; padding:1px 5px;">REQ</span>
                    @else
                        <span style="font-size:9px; font-weight:800; color:#9CA3AF; border:1px solid #E5E3F0; border-radius:4px; padding:1px 5px;">OPT</span>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/prevent-double-submit.js') }}"></script>
<script>
function onFileChange(input) {
    const zone   = document.getElementById('uploadZone');
    const nameEl = document.getElementById('fileName');
    if (input.files[0]) {
        nameEl.textContent = input.files[0].name;
        zone.style.borderColor = '#10B981';
        zone.style.background  = 'rgba(16,185,129,0.06)';
    } else {
        nameEl.textContent = 'Seret & Lepas file di sini';
        zone.style.borderColor = 'rgba(124,58,237,0.35)';
        zone.style.background  = 'rgba(124,58,237,0.04)';
    }
}
const zone = document.getElementById('uploadZone');
if (zone) {
    zone.addEventListener('dragover',  e => { e.preventDefault(); zone.classList.add('dragging'); });
    zone.addEventListener('dragleave', () => zone.classList.remove('dragging'));
    zone.addEventListener('drop',      e => { e.preventDefault(); zone.classList.remove('dragging'); });
}
// Mobile collapse
const grid = document.querySelector('[style*="grid-template-columns:1fr 320px"]');
if (grid && window.innerWidth < 900) grid.style.gridTemplateColumns = '1fr';
</script>
@endsection
