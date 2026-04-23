@extends('layouts.app')

@section('title', 'Tinjau & Terbitkan – AI Quiz Generator')
@section('meta_description', 'Tinjau dan edit soal yang dihasilkan AI sebelum diterbitkan')
@section('page_title', 'Tinjau & Terbitkan')
@section('page_subtitle', $title . ' – Edit soal & pilih jawaban benar')

@section('topbar_actions')
    @if(isset($source_text) && isset($question_count))
    <form action="{{ route('admin.quizzes.ai-generate') }}" method="POST" style="display:inline;">
        @csrf
        <input type="hidden" name="title" value="{{ $title }}">
        <textarea name="content_text" style="display:none">{{ $source_text }}</textarea>
        <input type="hidden" name="question_count" value="{{ $question_count }}">
        <input type="hidden" name="difficulty" value="{{ $difficulty }}">
        <input type="hidden" name="language" value="{{ $language ?? 'id' }}">
        <input type="hidden" name="time_limit" value="{{ $time_limit }}">
        <input type="hidden" name="passing_score" value="{{ $passing_score }}">
        <input type="hidden" name="qc" value="{{ !empty($qc_enabled) ? '1' : '0' }}">
        <input type="hidden" name="strict_mode" value="{{ !empty($strict_mode) ? '1' : '0' }}">
        <input type="hidden" name="regen_token" value="{{ uniqid('regen_',true) }}">
        <button type="submit" class="btn btn-ghost" style="padding:9px 16px; font-size:12px;">
            <i class="fa-solid fa-rotate-right"></i> Generate Ulang
        </button>
    </form>
    @endif
    <a href="{{ route('admin.quizzes.ai-create') }}" class="btn btn-ghost" style="padding:9px 16px; font-size:12px;">
        <i class="fa-solid fa-arrow-left"></i> Ubah Materi
    </a>
@endsection

@section('head_extra')
<style>
    .step-indicator { display:flex; gap:8px; margin-bottom:28px; }
    .step-item {
        flex:1; display:flex; align-items:center; gap:10px; padding:12px 16px;
        background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.07); border-radius:14px;
    }
    .step-num {
        width:32px; height:32px; border-radius:9px; display:flex; align-items:center; justify-content:center;
        font-size:12px; font-weight:900; flex-shrink:0;
    }
    .step-active { background:linear-gradient(135deg,#7C3AED,#4F46E5); color:#fff; }
    .step-done   { background:rgba(16,185,129,0.2); color:#34D399; }
    .step-off    { background:rgba(255,255,255,0.06); color:#8B8AAE; }

    .q-preview {
        background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.07);
        border-radius:18px; padding:28px; position:relative; transition:border-color .2s;
    }
    .q-preview:hover { border-color:rgba(124,58,237,0.2); }
    .q-accent { position:absolute; left:0; top:20%; bottom:20%; width:3px; border-radius:2px; background:linear-gradient(#7C3AED,#4F46E5); }
    .q-remove  {
        position:absolute; top:-12px; right:-12px;
        background:rgba(7,6,26,0.9); border:1px solid rgba(239,68,68,0.3);
        color:#F87171; width:32px; height:32px; border-radius:50%;
        display:flex; align-items:center; justify-content:center; font-size:13px;
        cursor:pointer; transition:all .2s; opacity:0;
    }
    .q-preview:hover .q-remove { opacity:1; }
    .q-remove:hover { background:rgba(239,68,68,0.2); border-color:rgba(239,68,68,0.5); }

    .opt-wrap {
        display:flex; align-items:center; gap:12px; padding:12px 16px;
        border:1.5px solid rgba(255,255,255,0.07); border-radius:12px;
        transition:all .2s; cursor:pointer;
    }
    .opt-wrap:has(input:checked) { border-color:rgba(16,185,129,0.4); background:rgba(16,185,129,0.06); }
    .opt-letter {
        width:30px; height:30px; border-radius:8px; flex-shrink:0;
        display:flex; align-items:center; justify-content:center;
        font-size:11px; font-weight:900; background:rgba(255,255,255,0.05); color:#8B8AAE;
    }
    .opt-wrap:has(input:checked) .opt-letter { background:rgba(16,185,129,0.2); color:#34D399; }

    .sticky-submit {
        position:sticky; bottom:24px; z-index:30;
        background:rgba(7,6,26,0.92); backdrop-filter:blur(20px);
        border:1px solid rgba(124,58,237,0.2); border-radius:18px;
        padding:16px 24px; margin-top:24px;
        display:flex; align-items:center; justify-content:space-between; gap:16px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.5);
    }
</style>
@endsection

@section('content')
<!-- Step Indicator -->
<div class="step-indicator fade-up">
    <div class="step-item" style="opacity:.5;">
        <div class="step-num step-done"><i class="fa-solid fa-check" style="font-size:10px;"></i></div>
        <div><div style="font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:.12em; color:#8B8AAE;">Langkah</div><div style="font-size:13px; font-weight:700; color:#F1F0FF;">Unggah</div></div>
    </div>
    <div class="step-item" style="border-color:rgba(124,58,237,0.3);">
        <div class="step-num step-active">2</div>
        <div><div style="font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:.12em; color:#8B8AAE;">Langkah</div><div style="font-size:13px; font-weight:700; color:#A78BFA;">Tinjau & Edit</div></div>
    </div>
    <div class="step-item" style="opacity:.4;">
        <div class="step-num step-off">3</div>
        <div><div style="font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:.12em; color:#8B8AAE;">Langkah</div><div style="font-size:13px; font-weight:700; color:#F1F0FF;">Terbitkan</div></div>
    </div>
</div>

<!-- QC Alert -->
@if(isset($qc) && is_array($qc) && count($qc) > 0)
<div style="background:rgba(245,158,11,0.1); border:1px solid rgba(245,158,11,0.25); border-radius:16px; padding:20px; margin-bottom:24px;" class="fade-up">
    <div style="display:flex; align-items:center; gap:12px; margin-bottom:14px;">
        <div style="width:40px; height:40px; border-radius:12px; background:rgba(245,158,11,0.2); color:#FBBF24; display:flex; align-items:center; justify-content:center; font-size:16px;">
            <i class="fa-solid fa-circle-exclamation"></i>
        </div>
        <div>
            <div style="font-size:14px; font-weight:800; color:#FBBF24;">Quality Check Ditemukan</div>
            <div style="font-size:12px; color:#8B8AAE;">{{ count($qc) }} soal perlu perhatian</div>
        </div>
    </div>
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
        @foreach($qc as $item)
        <div style="background:rgba(245,158,11,0.06); border:1px solid rgba(245,158,11,0.15); border-radius:12px; padding:14px;">
            <div style="font-size:12px; font-weight:800; color:#FBBF24; margin-bottom:6px;">Soal #{{ (int)$item['index'] + 1 }}</div>
            <ul style="list-style:disc; padding-left:16px; margin:0; font-size:12px; color:#D4B896; line-height:1.7;">
                @foreach($item['issues'] as $issue)<li>{{ $issue }}</li>@endforeach
            </ul>
        </div>
        @endforeach
    </div>
</div>
@endif

<!-- Quiz Meta Banner -->
<div class="card fade-up delay-1" style="border-radius:18px; padding:20px 24px; margin-bottom:24px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
    <div style="display:flex; align-items:center; gap:14px;">
        <div style="width:48px; height:48px; border-radius:14px; background:linear-gradient(135deg,#7C3AED,#4F46E5); display:flex; align-items:center; justify-content:center; font-size:22px; color:#fff;">
            <i class="fa-solid fa-robot"></i>
        </div>
        <div>
            <div style="font-size:16px; font-weight:900; color:#fff;">{{ $title }}</div>
            <div style="font-size:12px; color:#8B8AAE;"><span id="qCount">{{ count($questions) }}</span> soal dihasilkan AI</div>
        </div>
    </div>
    <div style="display:flex; gap:8px; flex-wrap:wrap;">
        <span class="badge badge-purple">{{ $difficulty }}</span>
        <span class="badge badge-blue">{{ $time_limit }}m</span>
        <span class="badge badge-yellow">Pass {{ $passing_score }}%</span>
        @if(isset($language))
        <span class="badge" style="background:rgba(255,255,255,0.05); border:1px rgba(255,255,255,0.1); color:#8B8AAE;">{{ strtoupper($language) }}</span>
        @endif
    </div>
</div>

<!-- Form: Questions -->
<form action="{{ route('admin.quizzes.ai-store') }}" method="POST" id="aiStoreForm">
    @csrf
    <input type="hidden" name="title" value="{{ $title }}">
    <input type="hidden" name="time_limit" value="{{ $time_limit }}">
    <input type="hidden" name="passing_score" value="{{ $passing_score }}">
    <input type="hidden" name="mcq_count" value="{{ $mcq_count }}">
    <input type="hidden" name="essay_count" value="{{ $essay_count }}">
    <input type="hidden" name="essay_grading_method" value="{{ $essay_grading_method }}">

    <div id="questionsRoot" style="display:flex; flex-direction:column; gap:16px;">
        @foreach($questions as $idx => $question)
        @php $type = $question['type'] ?? 'mcq'; @endphp
        <div class="q-preview question-block fade-up delay-{{ min($idx+1, 5) }}">
            <div class="q-accent" style="{{ $type === 'essay' ? 'background:linear-gradient(#FBBF24,#F59E0B);' : '' }}"></div>
            <button type="button" class="q-remove" onclick="this.closest('.question-block').remove(); syncCount();" title="Hapus soal">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <div style="padding-left:12px;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                    <div style="font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:.15em; color:#A78BFA;">Soal #{{ $idx + 1 }}</div>
                    <span class="badge {{ $type === 'essay' ? 'badge-yellow' : 'badge-purple' }}" style="font-size:9px; padding:3px 8px;">{{ strtoupper($type) }}</span>
                </div>

                <input type="hidden" name="questions[{{ $idx }}][type]" value="{{ $type }}">

                <div style="margin-bottom:16px;">
                    <label class="form-label">Pertanyaan</label>
                    <textarea name="questions[{{ $idx }}][text]" required rows="2" class="form-input">{{ $question['text'] }}</textarea>
                </div>

                @if($type === 'mcq')
                <div style="display:flex; flex-direction:column; gap:8px;">
                    <label class="form-label">Pilihan Jawaban</label>
                    @foreach($question['options'] as $oIdx => $opt)
                    <div class="opt-wrap">
                        <input type="radio" name="questions[{{ $idx }}][correct]" value="{{ $oIdx }}"
                               {{ $opt['is_correct'] ? 'checked' : '' }}
                               style="accent-color:#10B981; flex-shrink:0;">
                        <input type="hidden" name="questions[{{ $idx }}][options][{{ $oIdx }}][is_correct]"
                               value="{{ $opt['is_correct'] ? '1' : '0' }}" class="is_correct_flag">
                        
                        <div class="opt-letter">{{ chr(65+$oIdx) }}</div>
                        <input type="text" name="questions[{{ $idx }}][options][{{ $oIdx }}][text]"
                               value="{{ $opt['text'] }}" required
                               class="form-input" style="border:none; outline:none; background:transparent; padding:0; font-size:14px; flex:1; color:#D4D4FF;">
                    </div>
                    @endforeach
                </div>
                @else
                <div style="margin-bottom:16px;">
                    <label class="form-label" style="color:#FBBF24;">Kunci Jawaban Ideal (Esai)</label>
                    <textarea name="questions[{{ $idx }}][ideal_answer]" required rows="3" class="form-input" 
                              style="border-color:rgba(245,158,11,0.2); background:rgba(245,158,11,0.03);">{{ $question['ideal_answer'] ?? '' }}</textarea>
                </div>
                @endif

                <!-- AI Explanation -->
                <div style="margin-top:20px; background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.07); border-radius:14px; padding:16px;">
                    <label class="form-label" style="display:flex; align-items:center; gap:8px; color:#A78BFA; margin-bottom:10px;">
                        <i class="fa-solid fa-lightbulb"></i> Pembahasan AI
                    </label>
                    <textarea name="questions[{{ $idx }}][explanation]" rows="2" class="form-input" 
                               style="background:rgba(0,0,0,0.1); border-color:rgba(255,255,255,0.1); font-size:13px; color:#D4D4FF;">{{ $question['explanation'] ?? '' }}</textarea>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Sticky Submit -->
    <div class="sticky-submit">
        <div style="display:flex; align-items:center; gap:14px;">
            <div style="width:44px; height:44px; border-radius:12px; background:linear-gradient(135deg,#059669,#0891B2); display:flex; align-items:center; justify-content:center; color:#fff; font-size:18px;">
                <i class="fa-solid fa-rocket"></i>
            </div>
            <div>
                <div style="font-size:14px; font-weight:800; color:#fff;">Siap Terbitkan</div>
                <div style="font-size:12px; color:#8B8AAE;"><span id="qCountBottom">{{ count($questions) }}</span> soal tersedia</div>
            </div>
        </div>
        <button type="submit" class="btn btn-primary" id="deployBtn"
                style="padding:13px 28px; font-size:14px; background:linear-gradient(135deg,#059669,#0891B2);">
            <i class="fa-solid fa-paper-plane"></i> Terbitkan Kuis Sekarang
        </button>
    </div>
</form>
@endsection

@section('scripts')
<script src="{{ asset('js/prevent-double-submit.js') }}"></script>
<script>
function syncCount() {
    const count = document.querySelectorAll('.question-block').length;
    const a = document.getElementById('qCount');
    const b = document.getElementById('qCountBottom');
    const btn = document.getElementById('deployBtn');
    if (a) a.textContent = count;
    if (b) b.textContent = count;
    if (btn) { btn.disabled = count === 0; btn.style.opacity = count === 0 ? '.4' : '1'; }
}

function updateHidden(radio) {
    const block = radio.closest('.question-block');
    const radios = block.querySelectorAll('input[type=radio]');
    block.querySelectorAll('.is_correct_flag').forEach((h, i) => {
        h.value = radios[i] === radio ? '1' : '0';
    });
}

// Radio change listener
document.addEventListener('change', e => {
    if (e.target.type === 'radio') updateHidden(e.target);
});

syncCount();

// Mobile grid collapse
</script>
@endsection
