<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Details - PahamAja</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen">
    <div class="flex">
        <!-- Sidebar -->
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
            </nav>
        </div>

        <!-- Main -->
        <main class="flex-1 p-8">
            <header class="mb-10 flex justify-between items-start">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <a href="{{ route('admin.dashboard') }}" class="text-slate-400 hover:text-indigo-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        </a>
                        <span class="text-slate-400">Quiz Management</span>
                    </div>
                    <h2 class="text-3xl font-bold text-slate-800">{{ $quiz->title }}</h2>
                    <p class="text-slate-500">Melihat daftar pertanyaan dan pengaturan kuis.</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('admin.quizzes.edit', $quiz->slug) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-xl font-bold transition-all shadow-lg shadow-indigo-100 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        Edit Kuis
                    </a>
                    <form action="{{ route('admin.quizzes.destroy', $quiz->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kuis ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-rose-50 hover:bg-rose-100 text-rose-600 px-6 py-3 rounded-xl font-bold transition-all flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            Hapus
                        </button>
                    </form>
                </div>
            </header>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left: Questions List -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
                        <h3 class="text-xl font-bold text-slate-800 mb-8 flex items-center gap-2">
                            <span class="bg-indigo-100 text-indigo-600 p-1.5 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </span>
                            Daftar Pertanyaan ({{ $quiz->questions->count() }})
                        </h3>

                        @foreach($quiz->questions as $index => $question)
                        <div class="mb-10 last:mb-0 pb-10 last:pb-0 border-b border-slate-100 last:border-0">
                            <div class="flex items-start gap-4 mb-4">
                                <span class="w-8 h-8 bg-slate-100 text-slate-600 rounded-lg flex items-center justify-center font-bold flex-shrink-0">
                                    {{ $index + 1 }}
                                </span>
                                <h4 class="text-lg font-semibold text-slate-800 pt-0.5">{{ $question->text }}</h4>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pl-12">
                                @foreach($question->options as $option)
                                <div class="flex items-center gap-3 p-4 rounded-xl border {{ $option->is_correct ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'bg-slate-50 border-slate-200 text-slate-600' }}">
                                    @if($option->is_correct)
                                    <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                    @else
                                    <div class="w-5 h-5 border-2 border-slate-300 rounded-full"></div>
                                    @endif
                                    <span class="font-medium">{{ $option->text }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Right: Stats & Settings Sidebar -->
                <div class="space-y-6">
                    <div class="bg-indigo-600 rounded-3xl p-8 text-white shadow-xl shadow-indigo-200 relative overflow-hidden">
                        <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>
                        <h4 class="text-indigo-100 text-sm font-bold uppercase tracking-wider mb-6">Informasi Kuis</h4>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center bg-white/10 p-4 rounded-2xl">
                                <span class="text-indigo-100">Waktu</span>
                                <span class="font-bold text-lg">{{ $quiz->time_limit }} Menit</span>
                            </div>
                            <div class="flex justify-between items-center bg-white/10 p-4 rounded-2xl">
                                <span class="text-indigo-100">Nilai Kelulusan</span>
                                <span class="font-bold text-lg">{{ $quiz->passing_score }}</span>
                            </div>
                            <div class="flex justify-between items-center bg-white/10 p-4 rounded-2xl">
                                <span class="text-indigo-100">Peserta</span>
                                <span class="font-bold text-lg">{{ $quiz->participants_count }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
                        <h4 class="text-slate-800 font-bold mb-4">Link Berbagi</h4>
                        <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 flex items-center justify-between gap-2 overflow-hidden">
                            <span class="text-xs text-slate-500 truncate">{{ route('quiz.join', $quiz->slug) }}</span>
                            <button class="text-indigo-600 font-bold text-sm">Copy</button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
