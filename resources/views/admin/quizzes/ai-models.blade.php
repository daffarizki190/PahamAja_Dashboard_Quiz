<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gemini Models - PahamAja</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; background: #f8fafc; color: #0f172a; }
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
<body class="min-h-screen bg-slate-50">
    <div class="max-w-6xl mx-auto p-10">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-8 mb-12 animate-fade-in opacity-0">
            <div class="flex-1">
                <p class="text-[10px] font-black text-indigo-600 uppercase tracking-[0.3em] mb-3 leading-none bg-indigo-50 inline-block px-3 py-1 rounded-full border border-indigo-100">Artificial Intelligence • Core</p>
                <h1 class="text-5xl font-black text-slate-900 tracking-tight leading-none">Gemini Models</h1>
                <p class="text-slate-500 font-bold mt-6 text-sm tracking-tight opacity-80">Available models for current API configuration.</p>
            </div>
            <a href="{{ route('admin.quizzes.ai-create') }}" class="bg-white border border-slate-200 text-slate-700 px-6 py-4 rounded-2xl font-black text-[11px] uppercase tracking-widest hover:bg-slate-50 transition-all flex items-center gap-2 h-fit shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                <span>Back</span>
            </a>
        </div>

        <div class="bg-white border border-slate-200 rounded-[2.5rem] shadow-2xl shadow-slate-200/50 overflow-hidden animate-slide-up opacity-0 delay-100">
            <div class="px-10 py-8 border-b border-slate-100 flex items-center justify-between bg-white/50">
                <p class="text-lg font-black text-slate-900 tracking-tight">{{ count($models) }} Models Detected</p>
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest bg-slate-100 px-3 py-1.5 rounded-lg border border-slate-200">API Telemetry</span>
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

