@extends('layouts.app')

@section('title', 'Edit Quiz – PahamAja')
@section('meta_description', 'Edit kuis assessment yang sudah ada')
@section('page_title', 'Edit Kuis')
@section('page_subtitle', 'Assessment Editor – Sesuaikan kuis Anda')

@section('topbar_left')
    <a href="{{ route('admin.quizzes.show', $quiz->slug) }}" class="btn btn-ghost" style="padding:9px 12px; font-size:12px; background:rgba(124,58,237,0.08); border:1px solid rgba(124,58,237,0.15); color:var(--purple); margin-right:8px;">
        <i class="fa-solid fa-arrow-left"></i> Batal Edit
    </a>
@endsection

@section('topbar_actions')
    <button type="button" class="btn btn-primary" style="padding:8px 14px; font-size:12px;" onclick="document.getElementById('quizForm').requestSubmit()">
        <i class="fa-solid fa-floppy-disk"></i> Update Kuis
    </button>
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
        margin-bottom: 20px;
    }
    .q-card:hover { border-color: rgba(124,58,237,0.25); }
    .q-card.active-border { border-left: 4px solid var(--purple); }

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

    .add-zone {
        border: 2px dashed rgba(124,58,237,0.3); border-radius:12px; padding:18px;
        text-align:center; cursor:pointer; transition:all .2s; font-size:14px; font-weight:700;
        color:#7C3AED; background:transparent; margin-bottom: 20px;
    }
    .add-zone:hover { border-color:#7C3AED; background:rgba(124,58,237,0.05); }

    /* Range slider custom colors */
    input[type=range] {
        -webkit-appearance: none; width: 100%; height: 5px;
        background: #E5E3F0;
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
    <span>›</span> <a href="{{ route('admin.quizzes.show', $quiz->slug) }}">{{ $quiz->title }}</a>
    <span>›</span> Edit Kuis
</div>

<form action="{{ route('admin.quizzes.update', $quiz->slug) }}" method="POST" id="quizForm" class="fade-up">
    @csrf
    @method('PATCH')

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
                        <input type="text" name="title" required value="{{ $quiz->title }}" class="form-input"
                               placeholder="Judul kuis...">
                    </div>
                    
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                        <!-- Time Limit -->
                        <div>
                            <label class="form-label" style="display:flex; align-items:center; justify-content:space-between;">
                                <span style="display:flex; align-items:center; gap:7px;">
                                    <i class="fa-regular fa-clock" style="color:#F59E0B; font-size:12px;"></i> Time Limit
                                </span>
                                <span id="timeLimitLabel" style="font-weight:700; color:#7C3AED; font-size:13px;">{{ $quiz->time_limit }} menit</span>
                            </label>
                            <input type="range" id="timeLimitRange" name="time_limit" min="5" max="180" value="{{ $quiz->time_limit }}"
                                   oninput="document.getElementById('timeLimitLabel').textContent=this.value+' menit'">
                        </div>
                        <!-- Passing Score -->
                        <div>
                            <label class="form-label" style="display:flex; align-items:center; justify-content:space-between;">
                                <span style="display:flex; align-items:center; gap:7px;">
                                    <i class="fa-solid fa-trophy" style="color:#D97706; font-size:11px;"></i> Passing Score
                                </span>
                                <span id="passScoreLabel" style="font-weight:700; color:#7C3AED; font-size:13px;">{{ $quiz->passing_score }}%</span>
                            </label>
                            <input type="range" id="passScoreRange" name="passing_score" min="0" max="100" value="{{ $quiz->passing_score }}"
                                   oninput="document.getElementById('passScoreLabel').textContent=this.value+'%'">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Questions Container -->
            <div id="questionsContainer" style="display:flex; flex-direction:column;">
                @foreach($quiz->questions as $qIndex => $question)
                <div class="q-card question-card active-border">
                    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px;">
                        <div style="font-size:12px; font-weight:800; color:#7C3AED;">Pertanyaan #{{ $qIndex + 1 }}</div>
                        @if($qIndex > 0)
                        <button type="button" class="btn btn-ghost" style="color:#EF4444; padding:5px 10px;" onclick="this.closest('.question-card').remove()">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                        @endif
                    </div>
                    
                    <input type="hidden" name="questions[{{ $qIndex }}][id]" value="{{ $question->id }}">
                    
                    <div>
                        <label class="form-label">Question Text</label>
                        <textarea name="questions[{{ $qIndex }}][text]" required rows="2" class="form-input" 
                                  placeholder="Teks pertanyaan..." style="margin-bottom:14px;">{{ $question->text }}</textarea>
                    </div>

                    <div style="display:flex; flex-direction:column; gap:8px; margin-bottom:16px;">
                        <label class="form-label">Pilihan Jawaban</label>
                        @foreach($question->options as $oIndex => $option)
                        <label class="option-row" style="cursor:pointer;">
                            <input type="hidden" name="questions[{{ $qIndex }}][options][{{ $oIndex }}][id]" value="{{ $option->id }}">
                            <input type="radio" name="questions[{{ $qIndex }}][correct_option]" value="{{ $oIndex }}" {{ $option->is_correct ? 'checked' : '' }}
                                   style="accent-color:#7C3AED; width:16px; height:16px; flex-shrink:0;">
                            <div class="option-letter">{{ chr(65+$oIndex) }}</div>
                            <input type="text" name="questions[{{ $qIndex }}][options][{{ $oIndex }}][text]" required value="{{ $option->text }}"
                                   class="form-input" placeholder="Opsi {{ chr(65+$oIndex) }}" style="flex:1;">
                        </label>
                        @endforeach
                    </div>

                    <!-- AI Explanation -->
                    <div style="background:#F9FAFB; border:1px solid #F3F4F6; padding:16px; border-radius:12px;">
                        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:10px;">
                            <label class="form-label" style="margin-bottom:0; display:flex; align-items:center; gap:6px;">
                                <i class="fa-solid fa-lightbulb" style="color:#F59E0B;"></i> Pembahasan
                            </label>
                            <button type="button" onclick="suggestAiExplanation(this)" class="btn btn-ghost" style="font-size:11px; color:#7C3AED; background:rgba(124,58,237,0.05); border:1px solid rgba(124,58,237,0.1); padding:4px 10px;">
                                <span class="ai-spinner hidden" style="margin-right:4px;"><span class="dots-wave purple sm"><span></span><span></span><span></span></span></span>
                                <span class="ai-text">✨ Sugesti AI</span>
                            </button>
                        </div>
                        <textarea name="questions[{{ $qIndex }}][explanation]" rows="2" class="form-input" 
                                  placeholder="Penjelasan mengapa jawaban tersebut benar..." style="background:#fff;">{{ $question->explanation }}</textarea>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Add Question Button -->
            <div class="add-zone" onclick="addQuestion()" id="addZone">
                <i class="fa-solid fa-plus-circle" style="margin-right:8px;"></i> Tambah Pertanyaan Baru
            </div>

            <!-- Update Button Mobile Only -->
            <button type="submit" class="btn btn-primary lg-hidden" 
                    style="justify-content:center; padding:14px; font-size:14px; border-radius:12px; width:100%; margin-top:10px;">
                <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
            </button>
        </div>

        <!-- RIGHT: Sidebar Info -->
        <div style="display:flex; flex-direction:column; gap:16px; position:sticky; top:88px;">
            <div class="card" style="padding:20px;">
                <div style="display:flex; align-items:center; gap:10px; margin-bottom:14px;">
                    <div style="width:36px; height:36px; border-radius:10px; background:rgba(245,158,11,0.1); display:flex; align-items:center; justify-content:center;">
                        <i class="fa-solid fa-circle-info" style="color:#D97706; font-size:16px;"></i>
                    </div>
                    <div style="font-size:14px; font-weight:800; color:#1E1B4B;">Informasi Editor</div>
                </div>
                <p style="font-size:12px; color:#6B7280; line-height:1.7; margin-bottom:12px;">
                    Anda sedang mengedit kuis yang sudah ada. Perubahan akan langsung berdampak pada peserta yang belum mengerjakan.
                </p>
                <div style="display:flex; flex-direction:column; gap:8px;">
                    <div style="display:flex; align-items:center; gap:10px; font-size:12px; font-weight:700; color:#4B5563;">
                        <i class="fa-solid fa-check-circle" style="color:#059669; font-size:14px;"></i> Total Soal: {{ $quiz->questions->count() }}
                    </div>
                    <div style="display:flex; align-items:center; gap:10px; font-size:12px; font-weight:700; color:#4B5563;">
                        <i class="fa-solid fa-clock" style="color:#7C3AED; font-size:14px;"></i> Terakhir Update: {{ $quiz->updated_at->diffForHumans() }}
                    </div>
                </div>
            </div>
            
            <div class="card" style="padding:16px;">
                <div style="font-size:11px; font-weight:700; color:#6B7280; margin-bottom:12px; text-transform:uppercase; letter-spacing:.08em;">Bantuan AI</div>
                <p style="font-size:11px; color:#9CA3AF; margin-bottom:12px;">Gunakan bantuan AI untuk menghasilkan penjelasan jawaban secara otomatis.</p>
                <button type="button" class="btn btn-ghost" disabled style="justify-content:flex-start; width:100%; font-size:12px; padding:9px 12px;">
                    <i class="fa-solid fa-wand-magic-sparkles" style="color:#7C3AED;"></i> AI Optimizer
                </button>
            </div>
        </div>
    </div>
</form>
@endsection

@section('scripts')
<script src="{{ asset('js/prevent-double-submit.js') }}"></script>
<script>
    let questionCount = {{ $quiz->questions->count() }};
    const container = document.getElementById('questionsContainer');
    
    function addQuestion() {
        const index = questionCount++;
        const card = document.createElement('div');
        card.className = 'q-card question-card active-border fade-up';
        card.innerHTML = `
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px;">
                <div style="font-size:12px; font-weight:800; color:#7C3AED;">Pertanyaan #${index + 1}</div>
                <button type="button" class="btn btn-ghost" style="color:#EF4444; padding:5px 10px;" onclick="this.closest('.question-card').remove()">
                    <i class="fa-solid fa-trash-can"></i>
                </button>
            </div>

            <div>
                <label class="form-label">Question Text</label>
                <textarea name="questions[${index}][text]" required rows="2" class="form-input" placeholder="Teks pertanyaan..." style="margin-bottom:14px;"></textarea>
            </div>

            <div style="display:flex; flex-direction:column; gap:8px; margin-bottom:16px;">
                <label class="form-label">Pilihan Jawaban</label>
                ${[0,1,2,3].map(i => `
                    <label class="option-row" style="cursor:pointer;">
                        <input type="radio" name="questions[${index}][correct_option]" value="${i}" ${i==0?'checked':''}
                               style="accent-color:#7C3AED; width:16px; height:16px; flex-shrink:0;">
                        <div class="option-letter">${String.fromCharCode(65+i)}</div>
                        <input type="text" name="questions[${index}][options][${i}][text]" required
                               class="form-input" placeholder="Opsi ${String.fromCharCode(65+i)}" style="flex:1;">
                    </label>
                `).join('')}
            </div>

            <div style="background:#F9FAFB; border:1px solid #F3F4F6; padding:16px; border-radius:12px;">
                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:10px;">
                    <label class="form-label" style="margin-bottom:0; display:flex; align-items:center; gap:6px;">
                        <i class="fa-solid fa-lightbulb" style="color:#F59E0B;"></i> Pembahasan
                    </label>
                    <button type="button" onclick="suggestAiExplanation(this)" class="btn btn-ghost" style="font-size:11px; color:#7C3AED; background:rgba(124,58,237,0.05); border:1px solid rgba(124,58,237,0.1); padding:4px 10px;">
                        <span class="ai-spinner hidden" style="margin-right:4px;"><span class="dots-wave purple sm"><span></span><span></span><span></span></span></span>
                        <span class="ai-text">✨ Sugesti AI</span>
                    </button>
                </div>
                <textarea name="questions[${index}][explanation]" rows="2" class="form-input" 
                          placeholder="Penjelasan mengapa jawaban tersebut benar..." style="background:#fff;"></textarea>
            </div>
        `;
        container.appendChild(card);
        card.scrollIntoView({ behavior:'smooth', block:'center' });
    }

    async function suggestAiExplanation(btn) {
        const card = btn.closest('.question-card');
        const qText = card.querySelector('textarea[name*="[text]"]').value;
        const correctRadio = card.querySelector('input[type="radio"]:checked');
        
        if (!qText || !correctRadio) {
            alert('Tolong isi teks pertanyaan dan pilih jawaban benar terlebih dahulu.');
            return;
        }

        const correctInput = correctRadio.closest('.option-row').querySelector('input[type="text"]');
        const correctText = correctInput ? correctInput.value : '';

        if (!correctText) {
            alert('Isi teks pada pilihan jawaban yang benar.');
            return;
        }

        const spinner = btn.querySelector('.ai-spinner');
        const aiText = btn.querySelector('.ai-text');
        const textarea = card.querySelector('textarea[name*="[explanation]"]');

        btn.disabled = true;
        spinner.classList.remove('hidden');
        aiText.textContent = 'Memproses...';

        try {
            const response = await fetch('{{ route("admin.quizzes.suggest-explanation") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    text: qText,
                    correct_answer: correctText
                })
            });

            const data = await response.json();
            if (data.explanation) {
                textarea.value = data.explanation;
            } else if (data.error) {
                alert('Error AI: ' + data.error);
            }
        } catch (err) {
            console.error(err);
            alert('Gagal menghubungi AI.');
        } finally {
            btn.disabled = false;
            spinner.classList.add('hidden');
            aiText.textContent = '✨ Sugesti AI';
        }
    }
</script>
@endsection
