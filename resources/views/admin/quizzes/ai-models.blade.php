<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gemini Models - PahamAja</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="min-h-screen bg-slate-50">
    <div class="max-w-6xl mx-auto p-10">
        <div class="flex items-start justify-between gap-6 mb-8">
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">AI</p>
                <h1 class="text-3xl font-black text-slate-900 tracking-tight">Daftar Model Gemini</h1>
                <p class="text-slate-500 font-semibold mt-2">Model yang bisa dipakai oleh API key saat ini.</p>
            </div>
            <a href="{{ route('admin.quizzes.ai-create') }}" class="bg-white border border-slate-200 text-slate-700 px-6 py-3 rounded-2xl font-bold text-sm hover:bg-gray-50 transition-all flex items-center gap-2 h-fit">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                <span>Kembali</span>
            </a>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
            <div class="p-6 border-b border-slate-200 flex items-center justify-between">
                <p class="text-sm font-black text-slate-900">{{ count($models) }} model</p>
                <span class="text-xs font-semibold text-slate-500">Jika kosong, API key belum punya akses model text.</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr class="text-[10px] text-slate-400 font-black uppercase tracking-[0.2em]">
                            <th class="px-6 py-4">Name</th>
                            <th class="px-6 py-4">Display</th>
                            <th class="px-6 py-4">Methods</th>
                            <th class="px-6 py-4 text-right">Tokens</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($models as $m)
                            <tr class="hover:bg-indigo-50/30 transition-all">
                                <td class="px-6 py-5">
                                    <p class="text-slate-900 font-semibold">{{ $m['name'] }}</p>
                                </td>
                                <td class="px-6 py-5">
                                    <p class="text-slate-700 font-semibold">{{ $m['displayName'] }}</p>
                                </td>
                                <td class="px-6 py-5">
                                    <p class="text-slate-600 font-semibold text-sm">
                                        {{ is_array($m['supportedGenerationMethods']) ? implode(', ', $m['supportedGenerationMethods']) : '-' }}
                                    </p>
                                </td>
                                <td class="px-6 py-5 text-right">
                                    <p class="text-slate-600 font-semibold text-sm">
                                        {{ $m['inputTokenLimit'] ?? '-' }} / {{ $m['outputTokenLimit'] ?? '-' }}
                                    </p>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-14 text-center text-slate-400 font-bold italic">Tidak ada model yang terdeteksi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>

