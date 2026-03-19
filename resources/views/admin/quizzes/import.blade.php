<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Soal - PahamAja</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; background: #f8fafc; color: #0f172a; }
        .sidebar { background: #0b1220; border-right: 1px solid #1e293b; }
        .animate-slide-up {
            animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        .animate-fade-in {
            animation: fadeIn 0.8s ease-out forwards;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .delay-100 { animation-delay: 100ms; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen">
    <div class="flex">
        <aside class="w-64 sidebar min-h-screen sticky top-0 text-white p-6 hidden md:block">
            <div class="flex items-center gap-3 mb-10">
                <div class="bg-indigo-600 w-10 h-10 rounded-2xl flex items-center justify-center font-black text-xl italic shadow-lg shadow-indigo-900/40">P</div>
                <div>
                    <h1 class="text-xl font-bold tracking-tight">Paham<span class="text-indigo-300">Aja</span></h1>
                    <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest -mt-1">Enterprise Suite</p>
                </div>
            </div>
            <nav class="space-y-1">
                <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3 px-3">Management</p>
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 text-slate-400 hover:bg-white/5 hover:text-white p-3 rounded-xl transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    <span class="text-sm font-medium">Active Assessments</span>
                </a>
                <a href="{{ route('admin.employees.index') }}" class="flex items-center gap-3 text-slate-400 hover:bg-white/5 hover:text-white p-3 rounded-xl transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    <span class="text-sm font-medium">Employee Insights</span>
                </a>
            </nav>
        </aside>

        <main class="flex-1 p-8">
            <header class="mb-10 flex flex-col md:flex-row justify-between items-start md:items-end gap-6 animate-fade-in opacity-0">
                <div>
                    <h2 class="text-4xl font-black text-slate-900 tracking-tight leading-none">Import Assessment</h2>
                    <p class="text-slate-500 mt-4 font-medium">Batch upload questions via CSV or JSON format.</p>
                </div>
                <a href="{{ route('admin.quizzes.index') }}" class="bg-white border border-slate-200 text-slate-700 px-6 py-3 rounded-xl font-bold text-sm hover:bg-gray-50 transition-all flex items-center gap-2 h-fit shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    <span>Kembali</span>
                </a>
            </header>

            @if(session('error'))
                <div class="bg-rose-50 border border-rose-200 text-rose-800 px-6 py-4 rounded-2xl mb-6">
                    <p class="font-black text-sm">{{ session('error') }}</p>
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

            <form action="{{ route('admin.quizzes.import.store') }}" method="POST" enctype="multipart/form-data" class="max-w-4xl">
                @csrf

                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8 mb-8">
                    <h3 class="text-xl font-bold text-slate-800 mb-6">Pengaturan Kuis</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Judul Kuis</label>
                            <input type="text" name="title" required value="{{ old('title') }}" placeholder="Contoh: Ujian Materi Bab 1"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Durasi (Menit)</label>
                                <input type="number" name="time_limit" required value="{{ old('time_limit', 60) }}" min="1"
                                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Passing (0-100)</label>
                                <input type="number" name="passing_score" required value="{{ old('passing_score', 70) }}" min="0" max="100"
                                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8 mb-8">
                    <h3 class="text-xl font-bold text-slate-800 mb-6">File Soal</h3>

                    <div class="space-y-3">
                        <input type="file" name="file" accept=".csv,.json" required
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                        <p class="text-xs text-slate-500 font-semibold">
                            Format CSV minimal: question, option_a, option_b, option_c, option_d, correct. Nilai correct bisa A/B/C/D atau 1/2/3/4.
                        </p>
                        <p class="text-xs text-slate-500 font-semibold">
                            Format JSON minimal: array berisi { "text": "...", "options": [ { "text": "...", "is_correct": true }, ... ] }.
                        </p>
                    </div>
                </div>

                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-4 rounded-2xl font-bold shadow-xl shadow-indigo-100 transition-all flex items-center justify-center space-x-2 w-full md:w-auto">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1M4 12l4 4m0 0l4-4m-4 4V4"></path></svg>
                    <span>Import & Buat Kuis</span>
                </button>
            </form>
        </main>
    </div>
</body>
</html>

