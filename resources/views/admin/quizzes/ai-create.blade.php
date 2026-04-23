@extends('layouts.app')

@section('title', 'Generator Kuis AI – PahamAja')
@section('meta_description', 'Generate soal kuis otomatis menggunakan AI Gemini')
@section('page_title', 'Generator Kuis AI')
@section('page_subtitle', 'Upload dokumen → AI buat soal otomatis')

@section('topbar_left')
    <a href="{{ route('admin.quizzes.index') }}" class="btn btn-ghost" style="padding:9px 12px; font-size:12px; background:rgba(124,58,237,0.08); border:1px solid rgba(124,58,237,0.15); color:var(--purple); margin-right:8px;">
        <i class="fa-solid fa-arrow-left"></i> Kembali
    </a>
@endsection

@section('topbar_actions')
@endsection

@section('head_extra')
<style>
    /* Step indicator */
    .step-indicator {
        display: flex; align-items: center; gap: 0; margin-bottom: 28px;
    }
    .step-item {
        display: flex; align-items: center; gap: 10px; padding: 11px 16px;
        background: #fff; border: 1px solid #E5E3F0;
        border-radius: 12px; flex: 1; min-width: 0;
        box-shadow: 0 1px 4px rgba(0,0,0,0.04);
    }
    .step-item + .step-item { margin-left: 8px; }
    .step-number {
        width: 32px; height: 32px; border-radius: 9px;
        display: flex; align-items: center; justify-content: center;
        font-size: 13px; font-weight: 900; flex-shrink: 0;
    }
    .step-number.active { background: linear-gradient(135deg,#7C3AED,#4F46E5); color:#fff; box-shadow: 0 4px 10px rgba(124,58,237,0.35); }
    .step-number.inactive { background: #F3F2FB; color: #9CA3AF; }
    .step-label { font-size: 10px; font-weight: 700; color: #9CA3AF; text-transform: uppercase; letter-spacing: .1em; }
    .step-name  { font-size: 13px; font-weight: 700; color: #1E1B4B; }

    .upload-area {
        border: 2px dashed rgba(124,58,237,0.3); border-radius: 14px;
        padding: 44px 24px; text-align: center; cursor: pointer;
        transition: all 0.22s; background: rgba(124,58,237,0.03); position: relative;
    }
    .upload-area:hover { border-color: #7C3AED; background: rgba(124,58,237,0.07); }
    .upload-area.dragging { border-color: #A78BFA; background: rgba(124,58,237,0.1); }

    .lang-btn {
        padding: 8px 18px; border-radius: 9px; font-size: 12px; font-weight: 700;
        cursor: pointer; transition: all 0.18s; border: none; font-family: inherit;
    }
    .lang-btn.active  { background: #7C3AED; color:#fff; }
    .lang-btn.inactive { background: transparent; color: #6B7280; }

    .diff-btn {
        padding: 7px 16px; border-radius: 8px; font-size: 12px; font-weight: 700;
        cursor: pointer; transition: all 0.18s; border: 1.5px solid #E5E3F0;
        background: #fff; color: #6B7280; font-family: inherit;
    }
    .diff-btn.active { background: #7C3AED; color: #fff; border-color: #7C3AED; }

    .source-tab {
        padding: 7px 14px; border-radius: 8px; font-size: 12px; font-weight: 700;
        cursor: pointer; transition: all 0.18s; border: none; font-family: inherit;
    }
    .source-tab.active  { background: rgba(124,58,237,0.12); color: #7C3AED; }
    .source-tab.inactive { background: transparent; color: #6B7280; }



    /* Range slider */
    input[type=range] {
        -webkit-appearance: none; width: 100%; height: 5px;
        background: linear-gradient(90deg, #7C3AED 50%, #E5E3F0 50%);
        border-radius: 99px; outline: none;
    }
    input[type=range]::-webkit-slider-thumb {
        -webkit-appearance: none; width: 16px; height: 16px;
        background: #7C3AED; border-radius: 50%; cursor: pointer;
        box-shadow: 0 2px 6px rgba(124,58,237,0.4);
    }
</style>
@endsection

@section('content')
<!-- Page Title + 3D Mascot -->
<div style="text-align:center; margin-bottom:32px; position:relative;" class="fade-up">
    <!-- Floating 3D Mascot -->
    <div class="float-3d" style="display:inline-block; margin-bottom:12px;">
        <img src="https://fonts.gstatic.com/s/e/notoemoji/latest/1f916/emoji.svg" 
             alt="Robot AI" class="depth-shadow" 
             style="width:100px; height:100px;">
    </div>
    <h2 style="font-size:32px; font-weight:900; color:#1E1B4B; margin-bottom:10px; letter-spacing:-0.02em;">Generator Kuis AI <span style="color:#7C3AED;">3D</span> ✨</h2>
    <p style="font-size:14px; font-weight:600; color:#6B7280;">Ubah dokumen statis menjadi pengalaman kuis interaktif</p>
</div>

<!-- Step Indicator -->
<div class="step-indicator fade-up delay-1" style="max-width:500px; margin:0 auto 24px;">
    <div class="step-item">
        <div class="step-number active">1</div>
        <div><div class="step-name">Step 1: Topik & Judul</div></div>
    </div>
    <div style="width:24px; height:2px; background:#E5E3F0; flex-shrink:0; margin:0 4px;"></div>
    <div class="step-item" style="opacity:.6;">
        <div class="step-number inactive">2</div>
        <div><div class="step-name">Step 2: Konfigurasi</div></div>
    </div>
</div>

@if(session('error'))
<div style="background:rgba(239,68,68,0.08); border:1px solid rgba(239,68,68,0.2); color:#DC2626; padding:12px 18px; border-radius:10px; margin-bottom:18px; font-size:13px; font-weight:700; display:flex; align-items:center; gap:10px;">
    <i class="fa-solid fa-triangle-exclamation"></i> {{ session('error') }}
</div>
@endif
@if($errors->any())
<div style="background:rgba(239,68,68,0.08); border:1px solid rgba(239,68,68,0.2); color:#DC2626; padding:12px 18px; border-radius:10px; margin-bottom:18px; font-size:13px; font-weight:700;">
    @foreach($errors->all() as $err)<div>{{ $err }}</div>@endforeach
</div>
@endif

<form action="{{ route('admin.quizzes.ai-generate') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div style="display:grid; grid-template-columns:1fr 300px; gap:20px;" class="fade-up delay-2">

        <!-- LEFT: Form -->
        <div style="display:flex; flex-direction:column; gap:18px;">

            <!-- Step 1: Topik & Judul -->
            <div class="card card-3d" style="padding:24px;" data-tilt data-tilt-max="5" data-tilt-glare="true" data-tilt-max-glare="0.1">
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                    <div>
                        <label class="form-label">Topik</label>
                        <input type="text" name="topic" value="{{ old('topic') }}" class="form-input"
                               placeholder="Contoh: SOP Parkir...">
                    </div>
                    <div>
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                            <div>
                                <label class="form-label" style="display:flex; justify-content:space-between;">
                                    Jml Pilihan Ganda <span id="mcqVal" style="color:#7C3AED; font-weight:800;">{{ old('mcq_count',10) }}</span>
                                </label>
                                <input type="range" name="mcq_count" min="0" max="50" value="{{ old('mcq_count',10) }}"
                                       oninput="document.getElementById('mcqVal').textContent=this.value">
                                <div style="display:flex; justify-content:space-between; font-size:10px; color:#9CA3AF; font-weight:600;"><span>0</span><span>50</span></div>
                            </div>
                            <div>
                                <label class="form-label" style="display:flex; justify-content:space-between;">
                                    Jml Esai <span id="esyVal" style="color:#7C3AED; font-weight:800;">{{ old('essay_count',0) }}</span>
                                </label>
                                <input type="range" name="essay_count" min="0" max="30" value="{{ old('essay_count',0) }}"
                                       oninput="document.getElementById('esyVal').textContent=this.value">
                                <div style="display:flex; justify-content:space-between; font-size:10px; color:#9CA3AF; font-weight:600;"><span>0</span><span>30</span></div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="form-label">Quiz Title</label>
                        <input type="text" name="title" required value="{{ old('title') }}" class="form-input"
                               placeholder="Judul kuis...">
                    </div>
                    <div>
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                            <div>
                                <label class="form-label">Difficulty</label>
                                <input type="hidden" name="difficulty" id="diffInput" value="{{ old('difficulty','Medium') }}">
                                <div style="display:flex; gap:6px;">
                                    <button type="button" class="diff-btn active" onclick="setDiff('Medium', this)" style="padding:6px 10px;">Sedang</button>
                                    <button type="button" class="diff-btn" onclick="setDiff('Hard', this)" style="padding:6px 10px; color:#DC2626;">Sulit</button>
                                </div>
                            </div>
                            <div>
                                <label class="form-label">Metode Penilaian Esai</label>
                                <select name="essay_grading_method" class="form-input" style="padding:8.5px 12px; font-size:12px;">
                                    <option value="ai" {{ old('essay_grading_method') == 'ai' ? 'selected' : '' }}>Otomatis (AI Gemini)</option>
                                    <option value="manual" {{ old('essay_grading_method') == 'manual' ? 'selected' : '' }}>Manual (Admin)</option>
                                </select>
                            </div>
                        </div>
                        <div style="margin-top:14px;">
                            <label class="form-label" style="display:flex; align-items:center; justify-content:space-between;">
                                Language
                                <span style="font-weight:600; color:#6B7280; font-size:12px;" id="langLabel">Indonesia</span>
                            </label>
                            <input type="hidden" name="language" id="language-input" value="{{ old('language','id') }}">
                            <div style="display:flex; align-items:center; gap:10px;">
                                <span style="font-size:12px; font-weight:600; color:#6B7280;">Indonesia</span>
                                <div onclick="toggleLang()" id="langToggleWrap" style="width:44px; height:24px; background:#E5E3F0; border-radius:99px; position:relative; cursor:pointer; transition:background .2s;">
                                    <div id="langToggleDot" style="width:18px; height:18px; background:white; border-radius:50%; position:absolute; top:3px; left:3px; transition:all .2s; box-shadow:0 1px 4px rgba(0,0,0,0.2);"></div>
                                </div>
                                <span style="font-size:12px; font-weight:600; color:#6B7280;">English</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- hidden fields -->
                @php $defaultPass = \App\Models\Setting::get('default_passing_score', 70); @endphp
                <input type="hidden" name="time_limit" value="{{ old('time_limit',30) }}">
                <input type="hidden" name="passing_score" value="{{ old('passing_score', $defaultPass) }}">
            </div>

            <!-- Source Input -->
            <div class="card card-3d" style="padding:22px;" data-tilt data-tilt-max="5" data-tilt-glare="true" data-tilt-max-glare="0.1">
                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px;">
                    <div style="font-size:14px; font-weight:800; color:#1E1B4B;" id="source-title">Upload Dokumen</div>
                    <div style="background:#F3F2FB; border:1px solid #E5E3F0; border-radius:8px; padding:3px; display:flex; gap:3px;">
                        <button type="button" onclick="switchSource('file')" id="tab-file" class="source-tab active">
                            <i class="fa-solid fa-file-arrow-up" style="margin-right:4px;"></i> File
                        </button>
                        <button type="button" onclick="switchSource('text')" id="tab-text" class="source-tab inactive">
                            <i class="fa-solid fa-align-left" style="margin-right:4px;"></i> Teks
                        </button>
                    </div>
                </div>

                <!-- File Upload -->
                <div id="section-file">
                    <div class="upload-area" id="uploadArea">
                        <input type="file" name="file" id="fileInput" accept=".pdf,.docx,.pptx"
                               style="position:absolute; inset:0; opacity:0; cursor:pointer; z-index:10;"
                               onchange="onFileChange(this)">
                        <i class="fa-solid fa-cloud-arrow-up" style="font-size:36px; color:#7C3AED; display:block; margin-bottom:12px;"></i>
                        <div id="file-name" style="font-size:14px; font-weight:700; color:#1E1B4B; margin-bottom:6px;">
                            Klik atau seret file ke sini
                        </div>
                        <div style="font-size:11px; color:#6B7280; font-weight:600;">
                            PDF · DOCX · PPTX · Maks 4MB
                        </div>
                        <div id="file-error" style="font-size:11px; color:#DC2626; font-weight:700; margin-top:8px; display:none;">
                            <i class="fa-solid fa-circle-exclamation"></i> File terlalu besar (Maks 4MB). Gunakan Tempel Teks.
                        </div>
                    </div>
                </div>

                <!-- Text Input -->
                <div id="section-text" style="display:none;">
                    <label class="form-label">Tempel Teks Materi</label>
                    <textarea name="content_text" id="content_text" rows="6" class="form-input"
                              placeholder="Masukkan atau tempel teks materi kuis di sini..."></textarea>
                </div>
            </div>
        </div>

        <!-- RIGHT: Preview Panel -->
        <div style="display:flex; flex-direction:column; gap:16px; position:sticky; top:88px; height:fit-content;">

            <!-- Pratinjau Kuis -->
            <div class="card card-3d" style="padding:22px;" data-tilt data-tilt-max="7" data-tilt-glare="true" data-tilt-max-glare="0.15">
                <div style="display:flex; align-items:center; gap:10px; margin-bottom:16px;">
                    <div style="font-size:20px;">📖</div>
                    <div style="font-size:14px; font-weight:800; color:#1E1B4B;">Pratinjau Kuis</div>
                </div>
                
                <!-- MCQ Preview -->
                <div id="preview-mcq">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                        <label class="form-label" style="margin:0;">Question PG</label>
                        <span class="badge badge-purple" style="font-size:9px;">PG</span>
                    </div>
                    <input type="text" class="form-input" placeholder="Apa yang dimaksud dengan..." style="margin-bottom:12px;" disabled>
                    <div style="display:flex; flex-direction:column; gap:7px;">
                        @foreach(['Option A','Option B','Option C','Option D'] as $opt)
                        <label style="display:flex; align-items:center; gap:8px; font-size:13px; color:#6B7280; cursor:default;">
                            <input type="radio" disabled style="accent-color:#7C3AED;"> {{ $opt }}
                        </label>
                        @endforeach
                    </div>
                </div>

                <div style="height:1px; background:#F3F2FB; margin:16px 0;"></div>

                <!-- Essay Preview -->
                <div id="preview-essay">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                        <label class="form-label" style="margin:0;">Question Esai</label>
                        <span class="badge badge-yellow" style="font-size:9px;">ESAI</span>
                    </div>
                    <input type="text" class="form-input" placeholder="Jelaskan proses dari..." style="margin-bottom:12px;" disabled>
                    <div style="background:#F9FAFB; border:1px dashed #E5E3F0; border-radius:8px; padding:12px; font-size:12px; color:#9CA3AF; text-align:center;">
                        Kolom jawaban esai (Textarea)
                    </div>
                </div>
            </div>


            <!-- Summary Card -->
            <div class="card-glow" style="border-radius:20px; padding:24px;">
                <div style="font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:.15em; color:#A78BFA; margin-bottom:16px;">
                    <i class="fa-solid fa-robot" style="margin-right:6px;"></i> Ringkasan Draft
                </div>
                <div style="display:flex; flex-direction:column; gap:12px; font-size:13px;">
                    <div style="display:flex; justify-content:space-between; color:var(--text-muted);">
                        <span>Tipe Soal</span><span style="color:var(--text-primary); font-weight:700;">Pilihan Ganda</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; color:var(--text-muted);">
                        <span>Edit Soal</span><span style="color:var(--text-primary); font-weight:700;">Langsung di Preview</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; color:var(--text-muted);">
                        <span>Penyimpanan</span><span style="color:var(--text-primary); font-weight:700;">Simpan ke Database</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; color:var(--text-muted);">
                        <span>AI Engine</span><span style="color:var(--purple); font-weight:700;">Gemini AI</span>
                    </div>
                </div>
                <div style="height:1px; background:rgba(255,255,255,0.07); margin:16px 0;"></div>
                <div style="background:rgba(124,58,237,0.1); border:1px solid rgba(124,58,237,0.2); border-radius:12px; padding:14px;">
                    <div style="font-size:10px; font-weight:800; color:#A78BFA; text-transform:uppercase; letter-spacing:.1em; margin-bottom:6px;">
                        <i class="fa-solid fa-lightbulb" style="margin-right:4px;"></i> Tips
                    </div>
                    <div style="font-size:12px; color:var(--text-primary); font-weight:600; line-height:1.6;">
                        Gunakan dokumen dengan teks yang jelas dan terstruktur untuk hasil terbaik dari AI
                    </div>
                </div>

                <!-- Quality Check -->
                <label style="display:flex; align-items:flex-start; gap:10px; margin-top:16px; cursor:pointer;">
                    <input type="checkbox" name="qc" value="1" {{ old('qc') ? 'checked' : '' }}
                           style="width:17px; height:17px; margin-top:2px; accent-color:#7C3AED; flex-shrink:0;">
                    <div>
                        <div style="font-size:13px; font-weight:700; color:var(--text-primary);">Quality Check</div>
                        <div style="font-size:11px; color:var(--text-muted); margin-top:2px;">Periksa kualitas soal sebelum review</div>
                    </div>
                </label>
            </div>

        </div>
    </div>

    <!-- Generate Button (full width below) -->
    <button type="submit" class="btn btn-primary" id="generateBtn"
            style="width:100%; justify-content:center; padding:16px; font-size:16px; border-radius:14px; margin-top:20px; box-shadow:0 6px 24px rgba(124,58,237,0.4);">
        <i class="fa-solid fa-wand-magic-sparkles"></i> Generate dengan AI ✨
    </button>
    <div style="text-align:center; font-size:11px; color:#9CA3AF; font-weight:600; margin-top:8px;">
        Proses biasanya 15–60 detik
    </div>
</form>


@endsection

@section('scripts')
<script src="{{ asset('js/prevent-double-submit.js') }}"></script>
<script>
let langIsEn = {{ old('language') === 'en' ? 'true' : 'false' }};
function toggleLang() {
    langIsEn = !langIsEn;
    const wrap = document.getElementById('langToggleWrap');
    const dot  = document.getElementById('langToggleDot');
    const inp  = document.getElementById('language-input');
    if (langIsEn) {
        wrap.style.background = '#7C3AED';
        dot.style.left = '23px';
        inp.value = 'en';
    } else {
        wrap.style.background = '#E5E3F0';
        dot.style.left = '3px';
        inp.value = 'id';
    }
}

function setDiff(val, btn) {
    document.getElementById('diffInput').value = val;
    document.querySelectorAll('.diff-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
}

function onFileChange(input) {
    const file = input.files[0];
    const errorDiv = document.getElementById('file-error');
    const nameDiv = document.getElementById('file-name');
    const generateBtn = document.getElementById('generateBtn');
    const area = document.getElementById('uploadArea');

    if (!file) {
        nameDiv.textContent = 'Klik atau seret file ke sini';
        errorDiv.style.display = 'none';
        return;
    }

    nameDiv.textContent = file.name;
    
    // 4MB limit (4 * 1024 * 1024 bytes)
    const maxSize = 4 * 1024 * 1024;
    
    if (file.size > maxSize) {
        errorDiv.style.display = 'block';
        area.style.borderColor = '#DC2626';
        area.style.background = 'rgba(239,68,68,0.05)';
        if (generateBtn) generateBtn.disabled = true;
        if (generateBtn) generateBtn.style.opacity = '0.5';
        if (generateBtn) generateBtn.style.cursor = 'not-allowed';
    } else {
        errorDiv.style.display = 'none';
        area.style.borderColor = '#7C3AED';
        area.style.background = 'rgba(124,58,237,0.07)';
        if (generateBtn) generateBtn.disabled = false;
        if (generateBtn) generateBtn.style.opacity = '1';
        if (generateBtn) generateBtn.style.cursor = 'pointer';
    }
}

function switchSource(type) {
    const sFile = document.getElementById('section-file');
    const sText = document.getElementById('section-text');
    const tFile = document.getElementById('tab-file');
    const tText = document.getElementById('tab-text');
    const fileInput = document.getElementById('fileInput');
    const textInput = document.getElementById('content_text');

    if (type === 'file') {
        sFile.style.display = ''; sText.style.display = 'none';
        tFile.className = 'source-tab active'; tText.className = 'source-tab inactive';
        fileInput.required = true; if(textInput) textInput.required = false;
    } else {
        sFile.style.display = 'none'; sText.style.display = '';
        tText.className = 'source-tab active'; tFile.className = 'source-tab inactive';
        fileInput.required = false; if(textInput) { textInput.required = true; textInput.focus(); }
    }
}

const area = document.getElementById('uploadArea');
if (area) {
    area.addEventListener('dragover', e => { e.preventDefault(); area.classList.add('dragging'); });
    area.addEventListener('dragleave', () => area.classList.remove('dragging'));
    area.addEventListener('drop', e => { e.preventDefault(); area.classList.remove('dragging'); });
}

// Mobile collapse
const grid = document.querySelector('[style*="grid-template-columns:1fr 300px"]');
if (grid && window.innerWidth < 900) grid.style.gridTemplateColumns = '1fr';
</script>
@endsection
