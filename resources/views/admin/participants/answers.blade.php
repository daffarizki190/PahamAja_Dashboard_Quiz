<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jawaban Peserta - {{ $quiz->title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="min-h-screen bg-slate-50">
    <div class="max-w-6xl mx-auto p-4 sm:p-6 lg:p-8">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-6 mb-8">
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Jawaban Peserta</p>
                <h1 class="text-3xl font-black text-slate-900 tracking-tight">{{ $quiz->title }}</h1>
                <p class="text-slate-500 font-semibold mt-2">{{ $participant->name }} • {{ $participant->nim }}</p>
            </div>
            <div class="flex items-center gap-3 h-fit">
                <div class="bg-white border border-slate-200 rounded-2xl p-1 flex items-center gap-1">
                    <button type="button" id="btnTable" class="px-4 py-2 rounded-2xl text-xs font-black transition-all bg-slate-900 text-white">Tabel</button>
                    <button type="button" id="btnCard" class="px-4 py-2 rounded-2xl text-xs font-black transition-all text-slate-600 hover:bg-slate-50">Kartu</button>
                </div>
                <a href="{{ route('admin.quiz.dashboard', $quiz->slug) }}" class="bg-white border border-slate-200 text-slate-700 px-5 py-3 rounded-2xl font-bold text-sm hover:bg-gray-50 transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    <span>Kembali</span>
                </a>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
            <div class="p-6 border-b border-slate-200 flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Skor</p>
                    <p class="text-2xl font-black text-slate-900">{{ $participant->score ?? '-' }}</p>
                </div>
                <div class="text-right">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Percobaan</p>
                    <p class="text-sm font-black text-slate-900">{{ $participant->attempt ?? '-' }}</p>
                </div>
            </div>
            <div id="tableView" class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr class="text-[10px] text-slate-400 font-black uppercase tracking-[0.2em]">
                            <th class="px-6 py-4">No</th>
                            <th class="px-6 py-4">Pertanyaan</th>
                            <th class="px-6 py-4">Jawaban</th>
                            <th class="px-6 py-4">Kunci</th>
                            <th class="px-6 py-4 text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($rows as $i => $row)
                            <tr class="hover:bg-indigo-50/30 transition-all">
                                <td class="px-6 py-5">
                                    <span class="text-slate-500 font-black">{{ $i + 1 }}</span>
                                </td>
                                <td class="px-6 py-5">
                                    <p class="text-slate-900 font-semibold leading-snug break-words">{{ $row['question'] }}</p>
                                </td>
                                <td class="px-6 py-5">
                                    @if($row['selected'])
                                        <span class="text-slate-800 font-semibold break-words">{{ $row['selected'] }}</span>
                                    @else
                                        <span class="text-slate-300 font-bold">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-5">
                                    @if($row['correct'])
                                        <span class="text-slate-800 font-semibold break-words">{{ $row['correct'] }}</span>
                                    @else
                                        <span class="text-slate-300 font-bold">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-5 text-right">
                                    @if(is_null($row['is_correct']))
                                        <span class="text-slate-300 font-bold">-</span>
                                    @elseif($row['is_correct'])
                                        <span class="bg-emerald-50 text-emerald-700 px-3 py-1 rounded-xl text-xs font-black">Benar</span>
                                    @else
                                        <span class="bg-rose-50 text-rose-700 px-3 py-1 rounded-xl text-xs font-black">Salah</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div id="cardView" class="hidden p-6 space-y-4">
                @foreach($rows as $i => $row)
                    <div class="bg-white border border-slate-200 rounded-2xl p-6">
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Soal {{ $i + 1 }}</p>
                                <p class="text-slate-900 font-semibold leading-snug">{{ $row['question'] }}</p>
                            </div>
                            @if(is_null($row['is_correct']))
                                <span class="text-slate-300 font-black">-</span>
                            @elseif($row['is_correct'])
                                <span class="bg-emerald-50 text-emerald-700 px-3 py-1 rounded-xl text-xs font-black h-fit">Benar</span>
                            @else
                                <span class="bg-rose-50 text-rose-700 px-3 py-1 rounded-xl text-xs font-black h-fit">Salah</span>
                            @endif
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-5">
                            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Jawaban</p>
                                <p class="text-slate-800 font-semibold">{{ $row['selected'] ?: '-' }}</p>
                            </div>
                            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Kunci</p>
                                <p class="text-slate-800 font-semibold">{{ $row['correct'] ?: '-' }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <script>
        const btnTable = document.getElementById('btnTable');
        const btnCard = document.getElementById('btnCard');
        const tableView = document.getElementById('tableView');
        const cardView = document.getElementById('cardView');

        const setMode = (mode) => {
            const isTable = mode === 'table';
            tableView?.classList.toggle('hidden', !isTable);
            cardView?.classList.toggle('hidden', isTable);

            btnTable?.classList.toggle('bg-slate-900', isTable);
            btnTable?.classList.toggle('text-white', isTable);
            btnTable?.classList.toggle('text-slate-600', !isTable);
            btnTable?.classList.toggle('hover:bg-slate-50', !isTable);

            btnCard?.classList.toggle('bg-slate-900', !isTable);
            btnCard?.classList.toggle('text-white', !isTable);
            btnCard?.classList.toggle('text-slate-600', isTable);
            btnCard?.classList.toggle('hover:bg-slate-50', isTable);
        };

        btnTable?.addEventListener('click', () => setMode('table'));
        btnCard?.addEventListener('click', () => setMode('card'));
        setMode('table');
    </script>
</body>
</html>
