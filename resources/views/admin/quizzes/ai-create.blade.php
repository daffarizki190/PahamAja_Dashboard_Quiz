<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Quiz Generator - PahamAja</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; background: #ffffff; color: #0f172a; }
        .sidebar { background: #0f172a; border-right: 1px solid #1e293b; }
        .card-enterprise { background: #ffffff; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1); }
        .btn-primary { background: #4f46e5; transition: all 0.2s ease; }
        .btn-primary:hover { background: #4338ca; transform: translateY(-1px); }
        .field { background: #ffffff; border: 1px solid #e2e8f0; transition: all 0.2s ease; }
        .field:focus { border-color: #4f46e5; box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.12); outline: none; }
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
                <a href="{{ route('admin.employees.index') }}" class="flex items-center gap-3 text-slate-400 hover:bg-white/5 hover:text-white p-3 rounded-xl transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    <span class="text-sm font-medium">Employee Insights</span>
                </a>
            </nav>
            <div class="absolute bottom-6 left-6 right-6">
                <div class="bg-indigo-600/10 p-4 rounded-2xl border border-indigo-500/20">
                    <p class="text-[9px] text-indigo-400 font-black uppercase tracking-widest mb-1">AI Engine</p>
                    <p class="text-xs font-bold text-white mb-3">Gemini Generator</p>
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                        <span class="text-[9px] text-indigo-300 font-bold uppercase">Ready</span>
                    </div>
                </div>
            </div>
        </aside>

        <main class="flex-1 p-10 max-w-[1100px] mx-auto">
            <header class="mb-10 flex flex-col gap-6">
                <div class="flex items-start justify-between gap-6">
                    <div>
                        <div class="flex items-center gap-2 text-slate-400 text-xs font-bold">
                            <a class="hover:text-indigo-600" href="{{ route('admin.quizzes.index') }}">Assessments</a>
                            <span>/</span>
                            <span class="text-slate-600">AI Generator</span>
                        </div>
                        <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight mt-2">Generate Quiz dengan AI</h2>
                        <p class="text-slate-500 mt-2 font-medium text-sm">Upload dokumen, pilih parameter, lalu biarkan AI membuat draft soal.</p>
                    </div>
                    <a href="{{ route('admin.quizzes.index') }}" class="bg-white border border-slate-200 text-slate-700 px-6 py-3 rounded-xl font-bold text-sm hover:bg-gray-50 transition-all flex items-center gap-2 h-fit">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        <span>Kembali</span>
                    </a>
                </div>

                <div class="grid grid-cols-3 gap-3 max-w-xl">
                    <div class="flex items-center gap-3 card-enterprise rounded-2xl p-4">
                        <div class="w-9 h-9 rounded-xl step-on flex items-center justify-center font-black text-sm">1</div>
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Step</p>
                            <p class="text-sm font-bold text-slate-900">Upload</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 card-enterprise rounded-2xl p-4 opacity-80">
                        <div class="w-9 h-9 rounded-xl step-off flex items-center justify-center font-black text-sm">2</div>
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Step</p>
                            <p class="text-sm font-bold text-slate-900">Review</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 card-enterprise rounded-2xl p-4 opacity-70">
                        <div class="w-9 h-9 rounded-xl step-off flex items-center justify-center font-black text-sm">3</div>
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Step</p>
                            <p class="text-sm font-bold text-slate-900">Deploy</p>
                        </div>
                    </div>
                </div>
            </header>

            @if(session('error'))
            <div class="bg-rose-50 border border-rose-200 text-rose-800 px-6 py-4 rounded-2xl mb-6 flex items-center gap-3">
                <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span class="font-bold text-sm">{{ session('error') }}</span>
            </div>
            @endif

            @if ($errors->any())
                <div class="bg-rose-50 border border-rose-200 text-rose-800 px-6 py-4 rounded-2xl mb-6">
                    <p class="font-black text-sm mb-2">Validasi gagal</p>
                    <ul class="list-disc pl-5 text-sm font-semibold space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.quizzes.ai-generate') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2 space-y-6">
                        <div class="card-enterprise rounded-2xl p-8">
                            <div class="flex items-center justify-between mb-6">
                                <div>
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Quiz Setup</p>
                                    <h3 class="text-xl font-extrabold text-slate-900 tracking-tight">Informasi Kuis</h3>
                                </div>
                            </div>
                            <div class="space-y-6">
                                <div class="space-y-2">
                                    <label class="text-xs font-black text-slate-400 uppercase tracking-widest px-1">Judul Kuis</label>
                                    <input type="text" name="title" required value="{{ old('title') }}" placeholder="Contoh: Q1 Technical Competence Review" class="w-full field px-6 py-4 rounded-2xl font-bold text-slate-700">
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-2">
                                        <label class="text-xs font-black text-slate-400 uppercase tracking-widest px-1">Jumlah Soal</label>
                                        <select name="question_count" class="w-full field px-6 py-4 rounded-2xl font-bold text-slate-700 appearance-none">
                                            @foreach ([5, 10, 15, 20] as $count)
                                                <option value="{{ $count }}" {{ (int) old('question_count', 10) === $count ? 'selected' : '' }}>{{ $count }} Soal</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-xs font-black text-slate-400 uppercase tracking-widest px-1">Kesulitan</label>
                                        <select name="difficulty" class="w-full field px-6 py-4 rounded-2xl font-bold text-slate-700 appearance-none">
                                            <option value="Easy" {{ old('difficulty', 'Medium') === 'Easy' ? 'selected' : '' }}>Easy</option>
                                            <option value="Medium" {{ old('difficulty', 'Medium') === 'Medium' ? 'selected' : '' }}>Medium</option>
                                            <option value="Hard" {{ old('difficulty', 'Medium') === 'Hard' ? 'selected' : '' }}>Hard</option>
                                        </select>
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-xs font-black text-slate-400 uppercase tracking-widest px-1">Durasi (Menit)</label>
                                        <input type="number" name="time_limit" required value="{{ old('time_limit', 30) }}" min="1" class="w-full field px-6 py-4 rounded-2xl font-bold text-slate-700">
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-xs font-black text-slate-400 uppercase tracking-widest px-1">Passing Score</label>
                                        <input type="number" name="passing_score" required value="{{ old('passing_score', 70) }}" min="0" max="100" class="w-full field px-6 py-4 rounded-2xl font-bold text-slate-700">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-enterprise rounded-2xl p-8">
                            <div class="flex items-center justify-between mb-8">
                                <div>
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Source Material</p>
                                    <h3 class="text-xl font-extrabold text-slate-900 tracking-tight" id="source-title">Input Sumber Soal</h3>
                                </div>
                                <div class="bg-slate-100 p-1 rounded-xl flex">
                                    <button type="button" onclick="switchSource('file')" id="tab-file" class="px-4 py-2 rounded-lg text-xs font-bold transition-all bg-white text-indigo-600 shadow-sm">FILE</button>
                                    <button type="button" onclick="switchSource('text')" id="tab-text" class="px-4 py-2 rounded-lg text-xs font-bold transition-all text-slate-500 hover:text-slate-700">TEKS</button>
                                </div>
                            </div>

                            <!-- File Upload Section -->
                            <div id="section-file" class="block">
                                <div class="relative group">
                                    <input type="file" name="file" id="file" accept=".pdf,.docx,.pptx" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                    <div class="border-2 border-dashed border-slate-200 rounded-2xl p-10 text-center group-hover:border-indigo-400 transition-all bg-slate-50">
                                        <div class="bg-white w-14 h-14 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-sm group-hover:scale-110 transition-transform">
                                            <svg class="w-7 h-7 text-slate-400 group-hover:text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                        </div>
                                        <p class="text-sm font-extrabold text-slate-800 mb-1" id="file-name">Klik untuk upload atau drag & drop</p>
                                        <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest">PDF, DOCX, PPTX (MAX 10MB)</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Paste Text Section -->
                            <div id="section-text" class="hidden">
                                <div class="space-y-2">
                                    <label class="text-xs font-black text-slate-400 uppercase tracking-widest px-1">Tempel Teks Bahan Kuis</label>
                                    <textarea name="content_text" id="content_text" rows="8" placeholder="Masukkan atau tempel teks materi kuis di sini (minimal 100 karakter)..." class="w-full field px-6 py-4 rounded-2xl font-medium text-slate-700 placeholder:font-medium placeholder:text-slate-300"></textarea>
                                </div>
                                <div class="mt-4 flex items-center gap-2 text-slate-400 text-[10px] font-bold uppercase tracking-widest px-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Pastikan teks mencukupi untuk jumlah soal yang diminta.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="card-enterprise rounded-2xl p-8">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Output</p>
                            <h3 class="text-xl font-extrabold text-slate-900 tracking-tight mb-4">Draft yang Dihasilkan</h3>
                            <div class="space-y-3 text-sm font-semibold text-slate-600">
                                <div class="flex items-center justify-between">
                                    <span>Format</span>
                                    <span class="text-slate-900">MCQ JSON</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span>Review</span>
                                    <span class="text-slate-900">Editable</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span>Deploy</span>
                                    <span class="text-slate-900">Save ke Sistem</span>
                                </div>
                            </div>
                            <div class="mt-6 bg-indigo-600/10 border border-indigo-500/20 rounded-2xl p-4">
                                <p class="text-[10px] text-indigo-500 font-black uppercase tracking-widest mb-1">Tips</p>
                                <p class="text-sm font-bold text-slate-800">Gunakan dokumen dengan teks yang jelas agar hasil lebih akurat.</p>
                            </div>
                            <label class="mt-6 flex items-start gap-3 bg-slate-50 border border-slate-200 rounded-2xl p-4 cursor-pointer select-none">
                                <input type="checkbox" name="qc" value="1" {{ old('qc') ? 'checked' : '' }} class="mt-1 w-5 h-5 text-indigo-600 border-slate-300 rounded">
                                <div>
                                    <p class="text-sm font-black text-slate-900">Quality Check (Opsional)</p>
                                    <p class="text-xs font-semibold text-slate-500 mt-1">Jalankan pemeriksaan kualitas otomatis sebelum review.</p>
                                </div>
                            </label>
                        </div>

                        <button type="submit" class="btn-primary w-full text-white px-8 py-4 rounded-2xl font-black text-sm shadow-lg shadow-indigo-600/20 flex items-center justify-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                            <span>Generate Soal</span>
                        </button>
                    </div>
                </div>
            </form>

            <footer class="mt-16 py-8 border-t border-slate-200 flex justify-center opacity-60">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">&copy; 2026 PahamAja</p>
            </footer>
        </main>
    </div>

    <script>
        document.getElementById('file').addEventListener('change', function(e) {
            const fileName = e.target.files[0] ? e.target.files[0].name : 'Klik untuk upload atau drag & drop';
            document.getElementById('file-name').textContent = fileName;
        });

        function switchSource(type) {
            const sectionFile = document.getElementById('section-file');
            const sectionText = document.getElementById('section-text');
            const tabFile = document.getElementById('tab-file');
            const tabText = document.getElementById('tab-text');
            const fileInput = document.getElementById('file');
            const textInput = document.getElementById('content_text');

            if (type === 'file') {
                sectionFile.classList.remove('hidden');
                sectionFile.classList.add('block');
                sectionText.classList.remove('block');
                sectionText.classList.add('hidden');

                tabFile.classList.add('bg-white', 'text-indigo-600', 'shadow-sm');
                tabFile.classList.remove('text-slate-500');
                tabText.classList.remove('bg-white', 'text-indigo-600', 'shadow-sm');
                tabText.classList.add('text-slate-500');

                fileInput.required = true;
                textInput.required = false;
            } else {
                sectionFile.classList.remove('block');
                sectionFile.classList.add('hidden');
                sectionText.classList.remove('hidden');
                sectionText.classList.add('block');

                tabText.classList.add('bg-white', 'text-indigo-600', 'shadow-sm');
                tabText.classList.remove('text-slate-500');
                tabFile.classList.remove('bg-white', 'text-indigo-600', 'shadow-sm');
                tabFile.classList.add('text-slate-500');

                fileInput.required = false;
                textInput.required = true;
                textInput.focus();
            }
        }
    </script>
</body>
</html>
