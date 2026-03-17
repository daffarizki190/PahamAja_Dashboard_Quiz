<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Soal - PahamAja</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen">
    <div class="flex">
        <div class="w-64 bg-indigo-900 min-h-screen text-white p-6 hidden md:block">
            <h1 class="text-2xl font-bold mb-10 flex items-center">
                <span class="bg-white text-indigo-900 rounded-lg p-1 mr-2">P</span>
                PahamAja
            </h1>
            <nav class="space-y-4">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 hover:bg-indigo-800 p-3 rounded-xl transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('admin.quizzes.index') }}" class="flex items-center space-x-3 hover:bg-indigo-800 p-3 rounded-xl transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 014-4h6m2 0a2 2 0 00-2-2H7a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2"></path></svg>
                    <span>Quiz Builder</span>
                </a>
            </nav>
        </div>

        <main class="flex-1 p-8">
            <header class="mb-10 flex justify-between items-center">
                <div>
                    <h2 class="text-3xl font-bold text-slate-800">Import Soal</h2>
                    <p class="text-slate-500">Upload soal dari file CSV atau JSON.</p>
                </div>
                <a href="{{ route('admin.quizzes.index') }}" class="text-slate-500 hover:text-slate-800 font-semibold flex items-center">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Kembali
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

