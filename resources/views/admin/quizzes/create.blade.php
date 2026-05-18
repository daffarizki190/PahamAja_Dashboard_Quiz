@extends('layouts.app')

@section('title', 'Buat Kuis – PahamAja')
@section('meta_description', 'Buat kuis assessment baru dengan builder manual')
@section('page_title', 'Buat Kuis')
@section('page_subtitle', 'Assessment Builder – Buat soal secara manual')

@section('topbar_left')
    <a href="{{ route('admin.quizzes.index') }}" class="btn btn-ghost" style="padding:9px 12px; font-size:12px; background:rgba(124,58,237,0.08); border:1px solid rgba(124,58,237,0.15); color:var(--purple); margin-right:8px;">
        <i class="fa-solid fa-arrow-left"></i> Kembali
    </a>
@endsection

@section('topbar_actions')
    <button type="button" class="btn btn-ghost" style="padding:8px 14px; font-size:12px;" onclick="document.getElementById('quizForm').requestSubmit()">
        Save Draft
    </button>
    <a href="{{ route('admin.quizzes.ai-create') }}" class="btn btn-primary" style="padding:8px 14px; font-size:12px;">
        Preview
    </a>
@endsection

@section('head_extra')
<style>
    /* Breadcrumb */
    .breadcrumb { display:flex; align-items:center; gap:8px; font-size:13px; color:#6B7280; font-weight:600; margin-bottom:20px; }
    .breadcrumb span { color:#9CA3AF; }
    .breadcrumb a { color:#7C3AED; text-decoration:none; font-weight:700; }

    /* Question card */
    .q-card {
        background: #fff;
        border: 1px solid #E5E3F0;
        border-radius: 14px; padding: 22px; position: relative;
        transition: border-color .2s; box-shadow: 0 1px 4px rgba(0,0,0,0.04);
    }
    .q-card:hover { border-color: rgba(124,58,237,0.25); }
    .option-row   { display:flex; align-items:center; gap:12px; margin-bottom:10px; }
    .option-letter {
        width:30px; height:30px; flex-shrink:0; border-radius:8px;
        display:flex; align-items:center; justify-content:center;
        font-size:12px; font-weight:800; background:#F3F2FB; color:#6B7280;
        border: 1.5px solid #E5E3F0;
    }
    .option-row input[type=radio]:checked ~ .option-letter {
        background: #7C3AED; color: #fff; border-color: #7C3AED;
    }

    /* Highlighted option (when radio checked) */
    .option-row.is-correct { background: rgba(124,58,237,0.06); border-radius:10px; padding:4px 8px; }

    .add-zone {
        border: 2px dashed rgba(124,58,237,0.3); border-radius:12px; padding:18px;
        text-align:center; cursor:pointer; transition:all .2s; font-size:14px; font-weight:700;
        color:#7C3AED; background:transparent;
    }
    .add-zone:hover { border-color:#7C3AED; background:rgba(124,58,237,0.05); }

    /* Range slider */
    input[type=range] {
        -webkit-appearance: none; width: 100%; height: 5px;
        background: linear-gradient(90deg, #7C3AED 0%, #E5E3F0 0%);
        border-radius: 99px; outline: none;
    }
    input[type=range]::-webkit-slider-thumb {
        -webkit-appearance: none; width: 17px; height: 17px;
        background: #7C3AED; border-radius: 50%; cursor: pointer;
        box-shadow: 0 2px 8px rgba(124,58,237,0.4);
    }
</style>
@endsection

@section('content')
<!-- Breadcrumb -->
<div class="breadcrumb fade-up">
    <span>›</span> <a href="{{ route('admin.quizzes.index') }}">Dashboard</a>
    <span>›</span> Buat Kuis
</div>

<!-- Illustration Header -->
<div style="display:flex; justify-content:center; margin-bottom:20px;">
    <svg viewBox="0 0 200 140" fill="none" xmlns="http://www.w3.org/2000/svg" style="width:160px; height:auto;">
        <!-- Notebook -->
        <rect x="50" y="20" width="100" height="85" rx="8" fill="#fff" stroke="#E5E3F0" stroke-width="1.5"/>
        <rect x="50" y="20" width="100" height="18" rx="8" fill="#7C3AED" opacity=".8"/>
        <rect x="50" y="32" width="100" height="6" rx="0" fill="#6D28D9" opacity=".5"/>
        <!-- Checkmarks -->
        <rect x="65" y="48" width="40" height="4" rx="2" fill="#E5E3F0"/>
        <circle cx="60" cy="50" r="5" fill="rgba(124,58,237,0.15)" stroke="#7C3AED" stroke-width="1"/>
        <path d="M57.5 50 L59.5 52 L62.5 48" stroke="#7C3AED" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        <rect x="65" y="60" width="50" height="4" rx="2" fill="#E5E3F0"/>
        <circle cx="60" cy="62" r="5" fill="rgba(124,58,237,0.15)" stroke="#7C3AED" stroke-width="1"/>
        <path d="M57.5 62 L59.5 64 L62.5 60" stroke="#7C3AED" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        <rect x="65" y="72" width="45" height="4" rx="2" fill="#E5E3F0"/>
        <circle cx="60" cy="74" r="5" fill="rgba(124,58,237,0.15)" stroke="#7C3AED" stroke-width="1"/>
        <!-- Pencil -->
        <rect x="135" y="15" width="8" height="30" rx="3" fill="#F59E0B" transform="rotate(35 135 15)"/>
        <path d="M147 14 L152 22 L148 23 Z" fill="#E5E3F0"/>
        <!-- Sparkles -->
        <circle cx="42" cy="30" r="3" fill="#7C3AED" opacity=".6"/>
        <circle cx="165" cy="50" r="2" fill="#EF4444" opacity=".7"/>
        <circle cx="155" cy="28" r="3.5" fill="#7C3AED" opacity=".5"/>
        <circle cx="40" cy="75" r="2.5" fill="#F59E0B" opacity=".6"/>
    </svg>
</div>

<form action="{{ route('admin.quizzes.store') }}" method="POST" id="quizForm" class="fade-up">
    @csrf

    <div style="display:grid; grid-template-columns:1fr 280px; gap:20px; align-items:start;">

        <!-- LEFT: Form -->
        <div style="display:flex; flex-direction:column; gap:18px;">

            <!-- General Settings -->
            <div class="card" style="padding:24px;">
                <div style="font-size:15px; font-weight:800; color:#1E1B4B; margin-bottom:20px;">Pengaturan Umum</div>
                <div style="display:flex; flex-direction:column; gap:16px;">
                    <!-- Quiz Title -->
                    <div>
                        <label class="form-label" style="display:flex; align-items:center; gap:7px;">
                            <i class="fa-solid fa-pencil" style="color:#7C3AED; font-size:11px;"></i> Quiz Title
                        </label>
                        <input type="text" name="title" required value="{{ old('title') }}" class="form-input"
                               placeholder="Judul kuis...">
                    </div>
                    <!-- Time Limit -->
                    <div>
                        <label class="form-label" style="display:flex; align-items:center; justify-content:space-between;">
                            <span style="display:flex; align-items:center; gap:7px;">
                                <i class="fa-regular fa-clock" style="color:#F59E0B; font-size:12px;"></i> Time Limit
                            </span>
                            <span id="timeLimitLabel" style="font-weight:700; color:#7C3AED; font-size:13px;">60 menit</span>
                        </label>
                        <input type="range" id="timeLimitRange" name="time_limit" min="5" max="180" value="{{ old('time_limit', 60) }}"
                               oninput="document.getElementById('timeLimitLabel').textContent=this.value+' menit'">
                    </div>
                    <!-- Passing Score -->
                    <div>
                        <label class="form-label" style="display:flex; align-items:center; justify-content:space-between;">
                            <span style="display:flex; align-items:center; gap:7px;">
                                <i class="fa-solid fa-trophy" style="color:#D97706; font-size:11px;"></i> Passing Score
                            </span>
                        @php $defaultPass = \App\Models\Setting::get('default_passing_score', 70); @endphp
                        <span id="passScoreLabel" style="font-weight:700; color:#7C3AED; font-size:13px;">{{ old('passing_score', $defaultPass) }}%</span>
                    </label>
                    <input type="range" id="passScoreRange" name="passing_score" min="0" max="100" value="{{ old('passing_score', $defaultPass) }}"
                           oninput="document.getElementById('passScoreLabel').textContent=this.value+'%'">
                    </div>
                    <!-- Is Public -->
                    <div>
                        <label class="form-label" style="display:flex; align-items:center; justify-content:space-between; cursor:pointer;">
                            <span style="display:flex; align-items:center; gap:7px;">
                                <i class="fa-solid fa-globe" style="color:#059669; font-size:12px;"></i> Mode Kuis Umum (Public)
                            </span>
                            <div style="display:flex; align-items:center; gap:10px;">
                                <span style="font-size:11px; color:#6B7280; font-weight:600;">Izinkan siapapun ikut</span>
                                <label style="position:relative; display:inline-block; width:36px; height:20px;">
                                    <input type="checkbox" name="is_public" value="1" {{ old('is_public') ? 'checked' : '' }} style="opacity:0; width:0; height:0;" onchange="this.nextElementSibling.style.background = this.checked ? '#10B981' : '#E5E3F0'; this.nextElementSibling.querySelector('span').style.transform = this.checked ? 'translateX(16px)' : 'translateX(2px)';">
                                    <div style="position:absolute; cursor:pointer; top:0; left:0; right:0; bottom:0; background-color:#E5E3F0; border-radius:34px; transition:.4s;">
                                        <span style="position:absolute; content:''; height:16px; width:16px; left:0px; bottom:2px; background-color:white; border-radius:50%; transition:.4s; transform:translateX(2px); box-shadow:0 1px 3px rgba(0,0,0,0.1);"></span>
                                    </div>
                                </label>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Questions Container -->
            <div id="questionsContainer" style="display:flex; flex-direction:column; gap:14px;">
                <!-- Question #1 -->
                <div class="q-card question-card">
                    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px;">
                        <div style="font-size:12px; font-weight:800; color:#7C3AED;">Pertanyaan #1</div>
                    </div>
                    <div>
                        <label class="form-label">Question Text</label>
                        <input type="text" name="questions[0][text]" required class="form-input"
                               placeholder="Question Text" style="margin-bottom:14px;">
                    </div>
                    <div style="display:flex; flex-direction:column; gap:8px;">
                        @for($i=0; $i<4; $i++)
                        <label class="option-row" style="cursor:pointer;">
                            <input type="radio" name="questions[0][correct_option]" value="{{ $i }}" {{ $i===0?'checked':'' }}
                                   style="accent-color:#7C3AED; width:15px; height:15px; flex-shrink:0;">
                            <div class="option-letter">{{ chr(65+$i) }}</div>
                            <input type="text" name="questions[0][options][{{ $i }}][text]" required
                                   class="form-input" placeholder="Option {{ chr(65+$i) }}" style="flex:1;">
                        </label>
                        @endfor
                    </div>
                </div>
            </div>

            <!-- Add Question Button -->
            <div class="add-zone" onclick="addQuestion()" id="addZone">
                <i class="fa-solid fa-plus" style="margin-right:8px;"></i> Tambah Pertanyaan
            </div>

            <!-- Save Button -->
            <button type="submit" class="btn btn-primary" id="saveBtn"
                    style="justify-content:center; padding:14px; font-size:14px; border-radius:12px; width:100%;">
                <i class="fa-solid fa-floppy-disk"></i> Simpan Kuis
            </button>
        </div>

        <!-- RIGHT: Tips -->
        <div style="display:flex; flex-direction:column; gap:16px; position:sticky; top:88px;">
            <div class="card" style="padding:20px;">
                <div style="display:flex; align-items:center; gap:10px; margin-bottom:14px;">
                    <div style="width:36px; height:36px; border-radius:10px; background:rgba(245,158,11,0.1); display:flex; align-items:center; justify-content:center;">
                        <i class="fa-solid fa-lightbulb" style="color:#D97706; font-size:16px;"></i>
                    </div>
                    <div style="font-size:14px; font-weight:800; color:#1E1B4B;">Tips & Panduan</div>
                </div>
                <p style="font-size:12px; color:#6B7280; line-height:1.7; margin-bottom:10px;">
                    Helpful quiz terlibara can immove the weeecho data send and mopino communis.
                    Tips our read your professional ssimiurssione main helpful wample tips.
                </p>
                <ul style="font-size:12px; color:#6B7280; line-height:2; list-style:none; padding:0; margin:0;">
                    <li style="display:flex; align-items:flex-start; gap:6px;"><span style="color:#7C3AED;">→</span> Klik radio untuk tandai jawaban benar</li>
                    <li style="display:flex; align-items:flex-start; gap:6px;"><span style="color:#7C3AED;">→</span> Semua field pertanyaan wajib diisi</li>
                    <li style="display:flex; align-items:flex-start; gap:6px;"><span style="color:#7C3AED;">→</span> Gunakan AI Generator untuk soal otomatis</li>
                </ul>
            </div>
            <div class="card" style="padding:16px;">
                <div style="font-size:11px; font-weight:700; color:#6B7280; margin-bottom:10px; text-transform:uppercase; letter-spacing:.08em;">Alternatif</div>
                <a href="{{ route('admin.quizzes.ai-create') }}" class="btn btn-ghost" style="justify-content:flex-start; width:100%; font-size:12px; padding:9px 12px; margin-bottom:6px;">
                    <i class="fa-solid fa-robot" style="color:#7C3AED;"></i> Generator AI
                </a>
                <a href="{{ route('admin.quizzes.import') }}" class="btn btn-ghost" style="justify-content:flex-start; width:100%; font-size:12px; padding:9px 12px;">
                    <i class="fa-solid fa-file-import" style="color:#059669;"></i> Import Excel
                </a>
            </div>
        </div>
    </div>
</form>
@endsection

@section('scripts')
<script src="{{ asset('js/prevent-double-submit.js') }}"></script>
<script>
let qIdx = 1;

function syncCount() {}

function addQuestion() {
    const i = qIdx++;
    const card = document.createElement('div');
    card.className = 'q-card question-card';
    card.innerHTML = `
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px;">
            <div style="font-size:12px; font-weight:800; color:#7C3AED;">Pertanyaan #${i+1}</div>
            <button type="button" onclick="this.closest('.question-card').remove();"
                    class="btn btn-danger" style="padding:5px 10px; font-size:11px;">
                <i class="fa-solid fa-trash"></i>
            </button>
        </div>
        <div>
            <label class="form-label">Question Text</label>
            <input type="text" name="questions[${i}][text]" required class="form-input" placeholder="Question Text" style="margin-bottom:14px;">
        </div>
        <div style="display:flex; flex-direction:column; gap:8px;">
            ${['A','B','C','D'].map((l,j) => `
                <label class="option-row" style="cursor:pointer;">
                    <input type="radio" name="questions[${i}][correct_option]" value="${j}" ${j===0?'checked':''} style="accent-color:#7C3AED; width:15px; height:15px; flex-shrink:0;">
                    <div class="option-letter">${l}</div>
                    <input type="text" name="questions[${i}][options][${j}][text]" required class="form-input" placeholder="Option ${l}" style="flex:1;">
                </label>
            `).join('')}
        </div>
    `;
    document.getElementById('questionsContainer').appendChild(card);
    card.scrollIntoView({ behavior:'smooth', block:'center' });
}

// Mobile grid collapse
const g = document.querySelector('[style*="grid-template-columns:1fr 280px"]');
if (g && window.innerWidth < 900) g.style.gridTemplateColumns = '1fr';
</script>
@endsection
