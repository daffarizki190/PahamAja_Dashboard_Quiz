<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review AI Generation - PahamAja</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; background: #ffffff; color: #0f172a; }
        .sidebar { background: #0f172a; border-right: 1px solid #1e293b; }
        .card-enterprise { background: #ffffff; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1); }
        .btn-primary { background: #4f46e5; transition: all 0.2s ease; }
        .btn-primary:hover { background: #4338ca; transform: translateY(-1px); }
        .correct-option { border-color: #10b981; background: #ecfdf5; }
        .step-on { background: #4f46e5; color: #ffffff; }
        .step-off { background: #e2e8f0; color: #475569; }
    </style>
</head>
<body class="min-h-screen bg-gray-50/50">
    <div class="flex">
        <aside class="w-64 sidebar min-h-screen sticky top-0 text-white p-6 hidden md:block z-50">
            <div class="flex items-center gap-3 mb-10">
                <div class="bg-indigo-600 w-10 h-10 rounded-xl flex items-center justify-center font-black text-xl italic shadow-lg shadow-indigo-900/40">P</div>
                <div>
                    <h1 class="text-xl font-bold tracking-tight">Paham<span class="text-indigo-400">Aja</span></h1>
                    <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest -mt-1">Enterprise Suite</p>
                </div>
            </div>
            <nav class="space-y-1">
                <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3 px-3">Management</p>
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 text-slate-400 hover:bg-white/5 hover:text-white p-3 rounded-xl transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    <span class="text-sm font-semibold">Active Assessments</span>
                </a>
                <a href="{{ route('admin.quizzes.index') }}" class="flex items-center gap-3 bg-white/5 border border-white/10 text-white p-3 rounded-xl transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 014-4h6m2 0a2 2 0 00-2-2H7a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2"></path></svg>
                    <span class="text-sm font-semibold">Quiz Builder</span>
                </a>
                <a href="{{ route('admin.employees.index') }}" class="flex items-center gap-3 text-slate-400 hover:bg-white/5 hover:text-white p-3 rounded-xl transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    <span class="text-sm font-medium">Employee Insights</span>
                </a>
            </nav>
            <div class="absolute bottom-6 left-6 right-6">
                <div class="bg-indigo-600/10 p-4 rounded-2xl border border-indigo-500/20">
                    <p class="text-[9px] text-indigo-400 font-black uppercase tracking-widest mb-1">Review</p>
                    <p class="text-xs font-bold text-white mb-3">Curate Draft</p>
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                        <span class="text-[9px] text-indigo-300 font-bold uppercase">Editable</span>
                    </div>
                </div>
            </div>
        </aside>

        <main class="flex-1 p-10 max-w-5xl mx-auto">
            <header class="mb-10 flex flex-col gap-6">
                <div class="flex items-start justify-between gap-6">
                    <div>
                        <div class="flex items-center gap-2 text-slate-400 text-xs font-bold">
                            <a class="hover:text-indigo-600" href="{{ route('admin.quizzes.index') }}">Assessments</a>
                            <span>/</span>
                            <a class="hover:text-indigo-600" href="{{ route('admin.quizzes.ai-create') }}">AI Generator</a>
                            <span>/</span>
                            <span class="text-slate-600">Review</span>
                        </div>
                        <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight mt-2">Review Draft Soal</h2>
                        <p class="text-slate-500 mt-2 font-medium text-sm">Edit pertanyaan & opsi, pilih jawaban benar, lalu deploy.</p>
                    </div>
                    <div class="flex items-center gap-3 h-fit">
                        <span class="bg-indigo-600 text-white px-3 py-1 rounded-lg text-[10px] font-bold uppercase">{{ $difficulty }}</span>
                        @if(isset($language))
                            <span class="bg-slate-200 text-slate-700 px-3 py-1 rounded-lg text-[10px] font-bold uppercase">{{ $language === 'en' ? 'EN' : 'ID' }}</span>
                        @endif
                        <span class="bg-slate-200 text-slate-700 px-3 py-1 rounded-lg text-[10px] font-bold uppercase">{{ $time_limit }} MIN</span>
                        <span class="bg-slate-900 text-white px-3 py-1 rounded-lg text-[10px] font-bold uppercase">PASS {{ $passing_score }}</span>
                        @if(isset($source_text) && isset($question_count))
                            <form action="{{ route('admin.quizzes.ai-generate') }}" method="POST">
                                @csrf
                                <input type="hidden" name="title" value="{{ $title }}">
                                <textarea name="content_text" class="hidden">{{ $source_text }}</textarea>
                                <input type="hidden" name="question_count" value="{{ $question_count }}">
                                <input type="hidden" name="difficulty" value="{{ $difficulty }}">
                                <input type="hidden" name="language" value="{{ $language ?? 'id' }}">
                                <input type="hidden" name="time_limit" value="{{ $time_limit }}">
                                <input type="hidden" name="passing_score" value="{{ $passing_score }}">
                                <input type="hidden" name="qc" value="{{ !empty($qc_enabled) ? '1' : '0' }}">
                                <input type="hidden" name="regen_token" value="{{ uniqid('regen_', true) }}">
                                <button type="submit" class="bg-white border border-slate-200 text-slate-700 px-6 py-3 rounded-xl font-bold text-sm hover:bg-gray-50 transition-all flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8 8 0 104.582 9m0 0H9"></path></svg>
                                    <span>Buat Soal Lagi</span>
                                </button>
                            </form>
                        @endif
                        <a href="{{ route('admin.quizzes.ai-create') }}" class="bg-white border border-slate-200 text-slate-700 px-6 py-3 rounded-xl font-bold text-sm hover:bg-gray-50 transition-all flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                            <span>Ubah Materi</span>
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-3 max-w-xl">
                    <div class="flex items-center gap-3 card-enterprise rounded-2xl p-4 opacity-90">
                        <div class="w-9 h-9 rounded-xl step-off flex items-center justify-center font-black text-sm">1</div>
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Tahap</p>
                            <p class="text-sm font-bold text-slate-900">Unggah</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 card-enterprise rounded-2xl p-4">
                        <div class="w-9 h-9 rounded-xl step-on flex items-center justify-center font-black text-sm">2</div>
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Tahap</p>
                            <p class="text-sm font-bold text-slate-900">Tinjau</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 card-enterprise rounded-2xl p-4 opacity-70">
                        <div class="w-9 h-9 rounded-xl step-off flex items-center justify-center font-black text-sm">3</div>
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Tahap</p>
                            <p class="text-sm font-bold text-slate-900">Terbitkan</p>
                        </div>
                    </div>
                </div>

                <div class="card-enterprise rounded-2xl p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-600 flex items-center justify-center text-white shadow-lg shadow-indigo-600/20">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </div>
                        <div>
                            <p class="text-sm font-black text-slate-900">{{ $title }}</p>
                            <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest">Total: <span id="question-count">{{ count($questions) }}</span> soal</p>
                        </div>
                    </div>
                    <span class="bg-slate-100 border border-slate-200 text-slate-700 px-3 py-1 rounded-lg text-[10px] font-bold uppercase">Pilih 1 jawaban benar per soal</span>
                </div>

                @if(isset($qc) && is_array($qc) && count($qc) > 0)
                    <div class="card-enterprise rounded-2xl p-6 border-rose-200 bg-rose-50">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-[10px] font-black text-rose-600 uppercase tracking-widest mb-1">QC Findings</p>
                                <p class="text-sm font-black text-slate-900">Ada {{ count($qc) }} soal yang perlu dicek.</p>
                            </div>
                            <span class="bg-rose-600 text-white px-3 py-1 rounded-lg text-[10px] font-black uppercase">Review</span>
                        </div>
                        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-3">
                            @foreach($qc as $item)
                                <div class="bg-white border border-rose-200 rounded-2xl p-4">
                                    <p class="text-xs font-black text-slate-900 mb-2">Soal #{{ (int) $item['index'] + 1 }}</p>
                                    <ul class="list-disc pl-5 text-xs font-semibold text-slate-600 space-y-1">
                                        @foreach($item['issues'] as $issue)
                                            <li>{{ $issue }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </header>

            <form action="{{ route('admin.quizzes.ai-store') }}" method="POST" class="space-y-8">
                @csrf
                <input type="hidden" name="title" value="{{ $title }}">
                <input type="hidden" name="time_limit" value="{{ $time_limit }}">
                <input type="hidden" name="passing_score" value="{{ $passing_score }}">

                <div class="space-y-6" id="questions-root">
                    @foreach($questions as $index => $question)
                    <div class="card-enterprise rounded-2xl p-8 question-block relative group" id="q-{{ $index }}">
                        <button type="button" onclick="this.closest('.question-block').remove()" 
                                class="absolute -top-3 -right-3 bg-white border border-slate-200 text-slate-400 hover:text-rose-600 w-10 h-10 rounded-full flex items-center justify-center shadow-lg transition-all opacity-0 group-hover:opacity-100 remove-question">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>

                        <div class="mb-8">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Question #{{ $index + 1 }}</label>
                            <textarea name="questions[{{ $index }}][text]" required rows="2"
                                      class="w-full bg-transparent text-xl font-black text-slate-900 border-none focus:ring-0 p-0 resize-none">{{ $question['text'] }}</textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($question['options'] as $oIndex => $option)
                            <div class="relative flex items-center gap-4 p-4 rounded-2xl border border-slate-100 hover:border-slate-200 transition-all {{ $option['is_correct'] ? 'correct-option border-emerald-200' : 'bg-white' }}">
                                <input type="radio" name="questions[{{ $index }}][correct]" value="{{ $oIndex }}" 
                                       {{ $option['is_correct'] ? 'checked' : '' }}
                                       class="w-5 h-5 text-emerald-600 focus:ring-emerald-500 border-slate-300">
                                <input type="text" name="questions[{{ $index }}][options][{{ $oIndex }}][text]" value="{{ $option['text'] }}" required
                                       class="flex-1 bg-transparent border-none focus:ring-0 text-sm font-semibold text-slate-700">
                                <input type="hidden" name="questions[{{ $index }}][options][{{ $oIndex }}][is_correct]" value="{{ $option['is_correct'] ? '1' : '0' }}" class="is-correct-hidden">
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="sticky bottom-6">
                    <div class="card-enterprise rounded-2xl p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-indigo-600 flex items-center justify-center text-white shadow-lg shadow-indigo-600/20">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <div>
                                <p class="text-sm font-black text-slate-900">Siap Deploy</p>
                                <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest">Tersisa <span id="question-count-bottom">{{ count($questions) }}</span> soal</p>
                            </div>
                        </div>
                        <button type="submit" id="deploy-button" class="btn-primary text-white px-10 py-4 rounded-2xl font-black text-sm shadow-xl shadow-indigo-600/20 flex items-center gap-3">
                            <span>Deploy</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </button>
                    </div>
                </div>
            </form>
        </main>
    </div>

    <script>
        const syncQuestionCount = () => {
            const count = document.querySelectorAll('.question-block').length;
            const top = document.getElementById('question-count');
            const bottom = document.getElementById('question-count-bottom');
            const btn = document.getElementById('deploy-button');

            if (top) top.textContent = String(count);
            if (bottom) bottom.textContent = String(count);

            if (btn) {
                btn.disabled = count === 0;
                btn.classList.toggle('opacity-50', count === 0);
                btn.classList.toggle('cursor-not-allowed', count === 0);
            }
        };

        document.addEventListener('click', function (e) {
            const target = e.target;
            if (!(target instanceof Element)) return;
            if (target.closest('.remove-question')) {
                setTimeout(syncQuestionCount, 0);
            }
        });

        document.addEventListener('change', function (e) {
            if (e.target.type === 'radio') {
                const block = e.target.closest('.question-block');
                const hiddenInputs = block.querySelectorAll('.is-correct-hidden');
                hiddenInputs.forEach((input, idx) => {
                    input.value = (idx == e.target.value) ? '1' : '0';
                });
                
                block.querySelectorAll('.correct-option').forEach(el => {
                    el.classList.remove('correct-option', 'border-emerald-200');
                    el.classList.add('bg-white');
                });
                const container = e.target.closest('div');
                container.classList.add('correct-option', 'border-emerald-200');
                container.classList.remove('bg-white');
            }
        });

        syncQuestionCount();
    </script>
</body>
</html>
