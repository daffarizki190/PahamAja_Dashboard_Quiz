<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0f172a">
    <meta name="description" content="Analytics dashboard untuk kuis {{ $quiz->title }} - PahamAja">
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/icon-192.png">
    <title>{{ $quiz->title }} - Professional Insights</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f8fafc;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.03);
            transition: all 0.3s ease;
        }
        .glass-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px -5px rgba(0, 0, 0, 0.05);
        }
        .stat-icon {
            background: linear-gradient(135deg, rgba(79, 70, 229, 0.1) 0%, rgba(124, 58, 237, 0.1) 100%);
        }
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
        .delay-200 { animation-delay: 200ms; }
        .delay-300 { animation-delay: 300ms; }
        .delay-400 { animation-delay: 400ms; }
        .delay-500 { animation-delay: 500ms; }
    </style>
</head>
<body class="bg-slate-50">

<div class="container mx-auto px-6 py-10 max-w-[1500px]">
    
    <!-- Premium Header -->
    <header class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-12 gap-8 animate-slide-up">
        <div class="flex items-center gap-6">
            <a href="{{ route('admin.dashboard') }}" 
               class="bg-white p-3.5 rounded-2xl border border-slate-200 text-slate-400 hover:text-indigo-600 hover:border-indigo-100 hover:bg-slate-50 transition-all shadow-sm group">
                <svg class="w-6 h-6 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="w-2 h-2 bg-indigo-500 rounded-full animate-pulse"></span>
                    <p class="text-xs font-black text-indigo-600 uppercase tracking-[0.3em]">PahamAja Analytics</p>
                </div>
                <h1 class="text-4xl font-extrabold text-slate-800 tracking-tight">{{ $quiz->title }}</h1>
            </div>
        </div>
        
        <div class="flex flex-wrap lg:flex-nowrap items-center gap-4">
            <a href="{{ route('admin.quizzes.show', $quiz->slug) }}"
               class="bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 px-6 md:px-8 py-4 rounded-[1.5rem] font-bold shadow-sm transition-all flex items-center justify-center gap-3 active:scale-95 text-sm w-full sm:w-auto">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <span>Manage Quiz</span>
            </a>
            <a href="{{ route('admin.quiz.export', $quiz->slug) }}" 
               class="bg-slate-900 hover:bg-indigo-600 text-white px-6 md:px-8 py-4 rounded-[1.5rem] font-bold shadow-xl shadow-slate-200 transition-all flex items-center justify-center gap-3 active:scale-95 text-sm w-full sm:w-auto">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <span>Export Excel</span>
            </a>
            <a href="{{ route('admin.quiz.export-pdf', $quiz->slug) }}" 
               class="bg-rose-600 hover:bg-rose-700 text-white px-6 md:px-8 py-4 rounded-[1.5rem] font-bold shadow-xl shadow-rose-100 transition-all flex items-center justify-center gap-3 active:scale-95 text-sm w-full sm:w-auto">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                <span>Export PDF</span>
            </a>
            <button
                type="button"
                id="btnAiInsight"
                onclick="(function(e){try{if(window.pahamajaOpenAiInsight){return window.pahamajaOpenAiInsight(e);}var m=document.getElementById('aiInsightModal');if(m){m.classList.remove('hidden');m.style.display='';m.setAttribute('aria-hidden','false');document.body.classList.add('overflow-hidden');}var err=document.getElementById('aiInsightModalError');if(err){err.textContent='Script AI Insight belum siap. Refresh halaman lalu coba lagi.';err.classList.remove('hidden');}var loading=document.getElementById('aiInsightModalLoading');if(loading){loading.classList.add('hidden');}}catch(_){}})(event)"
                class="bg-violet-600 hover:bg-violet-700 text-white px-6 md:px-8 py-4 rounded-[1.5rem] font-bold shadow-xl shadow-violet-100 transition-all flex items-center justify-center gap-3 active:scale-95 text-sm w-full sm:w-auto cursor-pointer relative z-10"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                <span>✨ AI Insight</span>
            </button>
            @if(app()->environment('local'))
                <button
                    type="button"
                    id="btnRestoreData"
                    class="bg-amber-500 hover:bg-amber-600 text-white px-6 md:px-8 py-4 rounded-[1.5rem] font-bold shadow-xl shadow-amber-100 transition-all flex items-center justify-center gap-3 active:scale-95 text-sm w-full sm:w-auto"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8 8 0 104.582 9m0 0H9"></path></svg>
                    <span>Pemulihan Data</span>
                </button>
            @endif
            <form action="{{ route('admin.logout') }}" method="POST" class="w-full sm:w-auto">
                @csrf
                <button type="submit" class="bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 px-6 py-4 rounded-[1.5rem] font-bold shadow-sm transition-all flex items-center justify-center gap-3 active:scale-95 text-sm w-full">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1"></path></svg>
                    <span>Logout</span>
                </button>
            </form>
            <div class="bg-indigo-50 border border-indigo-100/50 px-6 py-4 rounded-[1.5rem] flex items-center justify-center gap-3 w-full sm:w-auto">
                <span class="relative flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-indigo-500"></span>
                </span>
                <span class="text-indigo-700 font-extrabold text-sm whitespace-nowrap transition-opacity duration-300" id="statLiveActivity">{{ $participants->count() }} Live Activity</span>
            </div>
        </div>
    </header>

    <div id="aiInsightModal" class="hidden fixed inset-0 z-[9999]" style="display:none" aria-hidden="true">
        <div id="aiInsightBackdrop" class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm"></div>
        <div class="relative h-full w-full overflow-y-auto p-6 md:p-10">
            <div class="max-w-4xl mx-auto">
                <div class="glass-card p-8 md:p-10 rounded-[2rem] border-l-4 border-violet-500">
                    <div class="flex items-start gap-4 mb-6">
                        <div class="w-12 h-12 rounded-2xl bg-violet-100 text-violet-600 flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-xl font-black text-slate-800 tracking-tight">✨ AI Insight</h3>
                            <p class="text-sm text-slate-500 font-semibold mt-1">Analisis detail dari Gemini AI (format diagnosa + rekomendasi)</p>
                        </div>
                        <button type="button" id="btnCloseAiInsightModal" class="text-slate-400 hover:text-slate-600 p-2 rounded-xl hover:bg-slate-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div id="aiInsightModalLoading" class="flex items-center gap-3 text-slate-500">
                        <svg class="animate-spin w-5 h-5 text-violet-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        <span class="text-sm font-medium">Menganalisis data kuis (detail)...</span>
                    </div>
                    <div id="aiInsightModalMeta" class="hidden mt-6"></div>
                    <div id="aiInsightTabs" class="hidden mt-6">
                        <div class="inline-flex bg-slate-100 p-1 rounded-2xl border border-slate-200">
                            <button type="button" data-ai-tab="analysis" class="ai-tab px-4 py-2 rounded-xl text-xs font-black transition-all">Analisis</button>
                            <button type="button" data-ai-tab="reco" class="ai-tab px-4 py-2 rounded-xl text-xs font-black transition-all">Rekomendasi</button>
                            <button type="button" data-ai-tab="questions" class="ai-tab px-4 py-2 rounded-xl text-xs font-black transition-all">Soal</button>
                            <button type="button" data-ai-tab="raw" class="ai-tab px-4 py-2 rounded-xl text-xs font-black transition-all">Raw</button>
                        </div>
                    </div>
                    <div id="aiInsightViews" class="hidden mt-6 space-y-4">
                        <div id="aiInsightViewAnalysis" class="space-y-4"></div>
                        <div id="aiInsightViewReco" class="space-y-4 hidden"></div>
                        <div id="aiInsightViewQuestions" class="space-y-4 hidden"></div>
                        <div id="aiInsightViewRaw" class="space-y-4 hidden"></div>
                    </div>
                    <div id="aiInsightModalText" class="text-slate-800 leading-relaxed text-sm font-medium hidden"></div>
                    <p id="aiInsightModalError" class="text-rose-600 text-sm font-medium hidden"></p>
                </div>
            </div>
        </div>
    </div>

    @if(app()->environment('local'))
        <div id="restoreDataModal" class="hidden fixed inset-0 z-[9999]" style="display:none" aria-hidden="true">
            <div id="restoreDataBackdrop" class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm"></div>
            <div class="relative h-full w-full overflow-y-auto p-6 md:p-10">
                <div class="max-w-3xl mx-auto">
                    <div class="glass-card p-8 md:p-10 rounded-[2rem] border-l-4 border-amber-500">
                        <div class="flex items-start gap-4 mb-6">
                            <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86l-8.2 14.2A2 2 0 003.82 21h16.36a2 2 0 001.73-2.94l-8.2-14.2a2 2 0 00-3.42 0z"></path></svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-xl font-black text-slate-800 tracking-tight">Pemulihan Data</h3>
                                <p class="text-sm text-slate-500 font-semibold mt-1">Kembalikan data aplikasi ke kondisi awal (seed ulang).</p>
                            </div>
                            <button type="button" id="btnCloseRestoreDataModal" class="text-slate-400 hover:text-slate-600 p-2 rounded-xl hover:bg-slate-100">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>

                        <div class="bg-amber-50 border border-amber-100 rounded-2xl p-5">
                            <p class="text-sm font-bold text-amber-900 leading-relaxed">
                                Aksi ini dapat menghapus data yang ada dan menjalankan seeding ulang.
                            </p>
                            <p class="text-xs font-semibold text-amber-800 mt-2">
                                Disarankan hanya untuk environment local.
                            </p>
                        </div>

                        <div class="mt-6 flex flex-col sm:flex-row gap-3">
                            <button type="button" id="btnRestoreSeedOnly" class="bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 px-6 py-3.5 rounded-2xl font-black shadow-sm transition-all active:scale-95 text-xs flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                <span>Seed Ulang (Tanpa Hapus)</span>
                            </button>
                            <button type="button" id="btnRestoreClearSeed" class="bg-amber-600 hover:bg-amber-700 text-white px-6 py-3.5 rounded-2xl font-black shadow-xl shadow-amber-100 transition-all active:scale-95 text-xs flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                <span>Hapus + Seed Ulang</span>
                            </button>
                        </div>

                        <div id="restoreDataStatus" class="mt-6 hidden"></div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Professional Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
        <div class="glass-card p-8 rounded-[2rem] animate-slide-up opacity-0 delay-100">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center bg-indigo-50 text-indigo-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                </div>
                <p class="text-slate-400 font-bold text-xs uppercase tracking-widest">Rata-rata Nilai</p>
            </div>
            <h3 class="text-3xl font-black text-slate-800 tracking-tight transition-opacity duration-300" id="statAvgScore">{{ number_format($avgScore, 1) }}%</h3>
        </div>
        <div class="glass-card p-8 rounded-[2rem] animate-slide-up opacity-0 delay-200">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center bg-violet-50 text-violet-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <p class="text-slate-400 font-bold text-xs uppercase tracking-widest">Sedang Mengerjakan</p>
            </div>
            <h3 class="text-3xl font-black text-slate-800 tracking-tight transition-opacity duration-300" id="statInProgress">{{ $inProgressCount }}</h3>
        </div>
        <div class="glass-card p-8 rounded-[2rem] animate-slide-up opacity-0 delay-300">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center bg-blue-50 text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.158-2.046-.452-2.992z"></path></svg>
                </div>
                <p class="text-slate-400 font-bold text-xs uppercase tracking-widest">Nilai Kelulusan</p>
            </div>
            <h3 class="text-3xl font-black text-slate-800 tracking-tight">{{ $quiz->passing_score }}</h3>
        </div>
        <div class="glass-card p-8 rounded-[2rem] animate-slide-up opacity-0 delay-400">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center bg-emerald-50 text-emerald-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                </div>
                <p class="text-slate-400 font-bold text-xs uppercase tracking-widest">Sudah Selesai</p>
            </div>
            <h3 class="text-3xl font-black text-slate-800 tracking-tight transition-opacity duration-300" id="statCompleted">{{ $participants->whereNotNull('score')->count() }}</h3>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
        <!-- Premium Share -->
        <div class="glass-card p-8 rounded-[2.5rem] flex flex-col items-center justify-center text-center animate-slide-up" style="animation-delay: 0.5s">
            <p class="text-slate-400 text-sm font-medium mb-10 leading-relaxed max-w-[200px]">Bagikan kuis via link atau scan QR Code.</p>
            
            <div class="relative mb-10 group">
                <div class="absolute -inset-4 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-[2.5rem] blur-xl opacity-10 group-hover:opacity-20 transition-all duration-500"></div>
                <div class="relative bg-white p-6 rounded-[2rem] shadow-xl border border-slate-100" id="qrWrap">
                    {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(180)
                        ->color(15, 23, 42)
                        ->margin(1)
                        ->generate(route('quiz.join', $quiz->slug)) !!}
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-center gap-3 -mt-4 mb-8">
                <button type="button" onclick="copyQr()" class="bg-white border border-slate-200 text-slate-700 px-5 py-2.5 rounded-2xl text-xs font-black hover:bg-gray-50 transition-all active:scale-95">
                    Copy QR
                </button>
                <button type="button" onclick="downloadQr()" class="bg-white border border-slate-200 text-slate-700 px-5 py-2.5 rounded-2xl text-xs font-black hover:bg-gray-50 transition-all active:scale-95">
                    Download QR
                </button>
            </div>
            
            <p class="text-slate-400 text-sm font-medium mb-10 leading-relaxed max-w-[200px]">Admit participants via direct entry point or QR.</p>
            
            <div class="w-full flex bg-slate-100/50 p-2 rounded-[1.5rem] border border-slate-200">
                <input type="text" id="quizLink" value="{{ url('/quiz/'.$quiz->slug) }}" 
                       class="flex-1 bg-transparent px-4 py-2 text-xs font-bold text-slate-500 focus:outline-none" readonly>
                <button type="button" onclick="copyLink()"
                        class="bg-white hover:bg-indigo-600 hover:text-white text-indigo-600 px-6 py-2.5 rounded-[1.2rem] text-xs font-black transition-all shadow-sm active:scale-95">
                    Copy
                </button>
            </div>
        </div>

        <!-- Professional Chart -->
        <div class="glass-card p-10 rounded-[2.5rem] lg:col-span-2 flex flex-col animate-slide-up" style="animation-delay: 0.6s">
            <div class="flex justify-between items-center mb-10">
                <h2 class="text-2xl font-black text-slate-800 tracking-tight">Sebaran Nilai</h2>
                <div class="flex gap-4">
                    <div class="flex items-center gap-2"><div class="w-3 h-3 rounded bg-emerald-500"></div><span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">High</span></div>
                    <div class="flex items-center gap-2"><div class="w-3 h-3 rounded bg-amber-500"></div><span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Mid</span></div>
                    <div class="flex items-center gap-2"><div class="w-3 h-3 rounded bg-rose-500"></div><span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Low</span></div>
                </div>
            </div>
            <div class="flex justify-center items-center flex-1 py-4">
                <div id="scoreChart" class="w-full max-w-[320px] min-h-[320px] flex items-center justify-center"></div>
            </div>
        </div>
    </div>

    <div class="glass-card rounded-[2.5rem] overflow-hidden mb-12 animate-slide-up" style="animation-delay: 0.65s">
        <div class="px-10 py-8 border-b border-slate-100 flex justify-between items-center bg-white/50">
            <h2 class="text-2xl font-black text-slate-800 tracking-tight">Analisis Soal</h2>
            <span class="bg-slate-100 text-slate-500 text-[10px] font-black px-3 py-1.5 rounded-lg uppercase tracking-widest border border-slate-200">Per Soal</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50/50 text-slate-400 text-[10px] font-black uppercase tracking-[0.2em]">
                    <tr>
                        <th class="px-10 py-6">Soal</th>
                        <th class="px-10 py-6">Dijawab</th>
                        <th class="px-10 py-6">Tingkat Benar</th>
                        <th class="px-10 py-6">Jawaban Terbanyak</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($questionAnalytics as $index => $row)
                        <tr class="hover:bg-indigo-50/30 transition-all duration-300">
                            <td class="px-10 py-6">
                                <p class="text-slate-800 font-black tracking-tight">#{{ $index + 1 }}</p>
                                <p class="text-slate-500 text-sm font-semibold mt-1 max-w-2xl leading-snug">{{ $row['text'] }}</p>
                            </td>
                            <td class="px-10 py-6">
                                <span class="bg-slate-900 text-white px-4 py-2 rounded-2xl text-xs font-black">{{ $row['answered'] }}/{{ $row['participants'] }}</span>
                            </td>
                            <td class="px-10 py-6">
                                @if(!is_null($row['correct_rate']))
                                    <div class="inline-flex items-center gap-3">
                                        <span class="text-slate-800 font-black">{{ $row['correct_rate'] }}%</span>
                                        <div class="w-28 h-1.5 bg-slate-200 rounded-full overflow-hidden">
                                            <div class="h-full bg-indigo-600 progress-bar" data-width="{{ $row['correct_rate'] }}"></div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-slate-300 italic text-xs font-bold">Belum ada data</span>
                                @endif
                            </td>
                            <td class="px-10 py-6">
                                @if($row['top_option'])
                                    <span class="text-slate-700 text-sm font-semibold">{{ $row['top_option'] }}</span>
                                @else
                                    <span class="text-slate-300 italic text-xs font-bold">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-10 py-20 text-center">
                                <p class="text-slate-400 font-bold text-sm tracking-tight italic">Belum ada data soal.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Leaderboard -->
    <div class="glass-card rounded-[2.5rem] overflow-hidden animate-slide-up" style="animation-delay: 0.7s">
        <div class="px-10 py-8 border-b border-slate-100 flex justify-between items-center bg-white/50">
            <h2 class="text-2xl font-black text-slate-800 tracking-tight">Catatan & Peringkat Tertinggi</h2>
            <div class="flex gap-2">
                <span class="bg-slate-100 text-slate-500 text-[10px] font-black px-3 py-1.5 rounded-lg uppercase tracking-widest border border-slate-200">Data Terkini</span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left block lg:table">
                <thead class="bg-slate-50/50 text-slate-400 text-[10px] font-black uppercase tracking-[0.2em] hidden lg:table-header-group">
                    <tr>
                        <th class="px-10 py-6">Peringkat</th>
                        <th class="px-10 py-6">Identitas</th>
                        <th class="px-10 py-6">Sesi</th>
                        <th class="px-10 py-6">Nilai</th>
                        <th class="px-10 py-6">Status & Durasi</th>
                        <th class="px-10 py-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 transition-opacity duration-300 block lg:table-row-group" id="liveLeaderboard">
                    @forelse($participants as $index => $participant)
                    <tr class="block lg:table-row bg-white hover:bg-indigo-50/30 transition-all duration-300 p-5 lg:p-0 my-4 lg:my-0 border border-slate-100 lg:border-none rounded-2xl lg:rounded-none shadow-sm lg:shadow-none group">
                        <td class="block lg:table-cell lg:px-10 py-2 lg:py-6 border-b border-slate-50 lg:border-none">
                            <div class="flex items-center justify-between lg:justify-start">
                                <span class="lg:hidden text-[10px] text-slate-400 font-black uppercase tracking-widest">Peringkat</span>
                                @if($index == 0 && !is_null($participant->score)) <div class="w-10 h-10 bg-amber-100 text-amber-600 rounded-2xl flex items-center justify-center font-black">1</div>
                                @elseif($index == 1 && !is_null($participant->score)) <div class="w-10 h-10 bg-slate-100 text-slate-500 rounded-2xl flex items-center justify-center font-black">2</div>
                                @elseif($index == 2 && !is_null($participant->score)) <div class="w-10 h-10 bg-orange-50 text-orange-600 rounded-2xl flex items-center justify-center font-black">3</div>
                                @else <span class="text-slate-400 font-bold text-sm lg:ml-4 italic">#{{ $index + 1 }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="block lg:table-cell lg:px-10 py-2 lg:py-6 border-b border-slate-50 lg:border-none">
                            <div class="flex lg:block items-center justify-between">
                                <span class="lg:hidden text-[10px] text-slate-400 font-black uppercase tracking-widest">Identitas</span>
                                <div class="text-right lg:text-left">
                                    <p class="text-slate-800 font-black tracking-tight">{{ $participant->name }}</p>
                                    <p class="text-slate-400 text-xs font-bold">{{ $participant->nim }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="block lg:table-cell lg:px-10 py-2 lg:py-6 border-b border-slate-50 lg:border-none">
                            <div class="flex items-center justify-between lg:justify-start">
                                <span class="lg:hidden text-[10px] text-slate-400 font-black uppercase tracking-widest">Sesi</span>
                                <span class="text-xs font-bold text-slate-600">{{ $participant->quizSession ? $participant->quizSession->name : '-' }}</span>
                            </div>
                        </td>
                        <td class="block lg:table-cell lg:px-10 py-2 lg:py-6 border-b border-slate-50 lg:border-none">
                            <div class="flex items-center justify-between lg:justify-start">
                                <span class="lg:hidden text-[10px] text-slate-400 font-black uppercase tracking-widest">Nilai</span>
                                @if(!is_null($participant->score))
                                    <div class="inline-flex items-center gap-2 bg-slate-900 text-white px-4 py-2 rounded-2xl">
                                        <span class="text-xs font-black">{{ $participant->score }}</span>
                                        <div class="w-20 h-1 bg-white/10 rounded-full overflow-hidden">
                                            <div class="h-full bg-indigo-500 progress-bar" data-width="{{ $participant->score }}"></div>
                                        </div>
                                    </div>
                                @else
                                    <span class="px-4 py-2 bg-amber-50 text-amber-600 rounded-2xl text-[10px] font-black uppercase tracking-widest animate-pulse">Mengerjakan</span>
                                @endif
                            </div>
                        </td>
                        <td class="block lg:table-cell lg:px-10 py-2 lg:py-6 border-b border-slate-50 lg:border-none">
                            <div class="flex items-center justify-between lg:justify-start">
                                <span class="lg:hidden text-[10px] text-slate-400 font-black uppercase tracking-widest">Status</span>
                                <div class="flex flex-col">
                                    @if(!is_null($participant->score))
                                        <span class="text-[10px] font-black uppercase tracking-[0.2em] {{ $participant->score >= $quiz->passing_score ? 'text-emerald-500' : 'text-rose-500' }}">
                                            {{ $participant->score >= $quiz->passing_score ? 'Lulus' : 'Tidak Lulus' }}
                                        </span>
                                        @if($participant->started_at && $participant->finished_at)
                                            <span class="text-[9px] font-bold text-slate-400 mt-1">🕒 {{ $participant->finished_at->diff($participant->started_at)->format('%im %ss') }}</span>
                                        @endif
                                    @else
                                        <span class="text-slate-300 italic text-[10px] font-bold">Sedang Sinkronisasi...</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="block lg:table-cell lg:px-10 pt-4 pb-2 lg:py-6 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.participant.answers', ['quiz' => $quiz->slug, 'participant' => $participant->id]) }}" class="text-slate-400 hover:text-indigo-600 transition-all p-2 hover:bg-indigo-50 rounded-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </a>
                                <form action="{{ route('admin.participant.destroy', ['quiz' => $quiz->slug, 'participant' => $participant->id]) }}" method="POST" onsubmit="return confirm('Hapus data peserta ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-400 hover:text-rose-600 transition-all p-2 hover:bg-rose-50 rounded-lg">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-10 py-20 text-center">
                            <div class="bg-slate-50 w-20 h-20 rounded-[1.5rem] flex items-center justify-center mx-auto mb-6 text-slate-200">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            </div>
                            <p class="text-slate-400 font-bold text-sm tracking-tight italic">Belum ada peserta.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    </div>
</div>

<script>
    const copyTextToClipboard = async (text) => {
        if (navigator.clipboard && window.isSecureContext) {
            await navigator.clipboard.writeText(text);
            return;
        }

        const textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.style.position = 'fixed';
        textarea.style.top = '0';
        textarea.style.left = '0';
        textarea.style.opacity = '0';
        document.body.appendChild(textarea);
        textarea.focus();
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
    };

    const getQrSvg = () => {
        const wrap = document.getElementById('qrWrap');
        if (!wrap) return null;
        return wrap.querySelector('svg');
    };

    const svgToPngBlob = async (svgEl, size = 512) => {
        const svgData = new XMLSerializer().serializeToString(svgEl);
        const svgBlob = new Blob([svgData], { type: 'image/svg+xml;charset=utf-8' });
        const url = URL.createObjectURL(svgBlob);

        const img = new Image();
        img.decoding = 'async';
        img.src = url;
        await img.decode();

        const canvas = document.createElement('canvas');
        canvas.width = size;
        canvas.height = size;
        const ctx = canvas.getContext('2d');
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, size, size);
        ctx.drawImage(img, 0, 0, size, size);

        URL.revokeObjectURL(url);

        return await new Promise((resolve) => canvas.toBlob(resolve, 'image/png'));
    };

    const copyLink = async () => {
        const input = document.getElementById('quizLink');
        if (!input) return;
        await copyTextToClipboard(input.value);
    };

    const downloadQr = async () => {
        const svg = getQrSvg();
        if (!svg) return;

        const blob = await svgToPngBlob(svg);
        if (!blob) return;

        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'qr-quiz.png';
        document.body.appendChild(a);
        a.click();
        a.remove();
        URL.revokeObjectURL(url);
    };

    const copyQr = async () => {
        const svg = getQrSvg();
        if (!svg) return;

        const blob = await svgToPngBlob(svg);
        if (!blob) return;

        if (navigator.clipboard && window.ClipboardItem) {
            await navigator.clipboard.write([new ClipboardItem({ 'image/png': blob })]);
            return;
        }

        await downloadQr();
    };

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.progress-bar[data-width]').forEach((el) => {
            const value = Number.parseFloat(el.dataset.width ?? '0');
            const clamped = Number.isFinite(value) ? Math.min(100, Math.max(0, value)) : 0;
            el.style.width = `${clamped}%`;
        });
    });
</script>

<script>
    // Initialize Sebaran Nilai Chart — ApexCharts Donut
    function initScoreChart() {
        const chartEl = document.querySelector("#scoreChart");
        if (!chartEl) return;

        try {
            const chartData = {!! json_encode($chartData) !!};
            if (!chartData || !chartData.scores) return;

            const scores = chartData.scores.map(s => parseInt(s) || 0);
            const total = scores.reduce((a, b) => a + b, 0);
            
            const options = {
                series: scores,
                chart: {
                    type: 'donut',
                    height: 320,
                    fontFamily: 'Plus Jakarta Sans, sans-serif',
                    animations: { enabled: true }
                },
                labels: ['0–50 (Low)', '51–75 (Mid)', '76–100 (High)'],
                colors: ['#f43f5e', '#f59e0b', '#10b981'],
                stroke: { show: false },
                dataLabels: { enabled: false },
                legend: {
                    position: 'bottom',
                    fontSize: '12px',
                    fontWeight: 600,
                    offsetY: 8,
                    markers: { radius: 12 },
                    formatter: function(val, opts) {
                        const v = opts.w.globals.series[opts.seriesIndex];
                        const pct = total > 0 ? Math.round((v / total) * 100) : 0;
                        return val + ": " + v + " (" + pct + "%)";
                    }
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '72%',
                            labels: {
                                show: true,
                                total: {
                                    show: true,
                                    label: 'PESERTA',
                                    fontSize: '10px',
                                    fontWeight: 800,
                                    color: '#94a3b8',
                                    formatter: () => total
                                },
                                value: {
                                    show: true,
                                    fontSize: '32px',
                                    fontWeight: 900,
                                    color: '#0f172a',
                                    offsetY: 2,
                                    formatter: (val) => val
                                }
                            }
                        }
                    }
                },
                tooltip: {
                    y: {
                        formatter: (val) => val + " peserta"
                    }
                }
            };

            const chart = new ApexCharts(chartEl, options);
            chart.render();
            window.scoreDonutChart = chart;
        } catch (err) {
            console.error('ApexCharts init error:', err);
            chartEl.innerHTML = '<p class="text-xs text-slate-400 font-bold italic">Gagal memuat grafik</p>';
        }
    }

    // Run on load
    if (document.readyState === 'loading') {
        window.addEventListener('DOMContentLoaded', initScoreChart);
    } else {
        initScoreChart();
    }
</script>

<script>
// AI Insight Button Logic
    window.pahamajaOpenAiInsight = async function (e) {
        if (e && typeof e.preventDefault === 'function') e.preventDefault();
        if (e && typeof e.stopPropagation === 'function') e.stopPropagation();
        const modal = document.getElementById('aiInsightModal');
        const loading = document.getElementById('aiInsightModalLoading');
        const meta = document.getElementById('aiInsightModalMeta');
        const tabs = document.getElementById('aiInsightTabs');
        const views = document.getElementById('aiInsightViews');
        const viewAnalysis = document.getElementById('aiInsightViewAnalysis');
        const viewReco = document.getElementById('aiInsightViewReco');
        const viewQuestions = document.getElementById('aiInsightViewQuestions');
        const viewRaw = document.getElementById('aiInsightViewRaw');
        const error = document.getElementById('aiInsightModalError');

        if (!modal || !loading || !tabs || !views || !viewAnalysis || !viewReco || !viewQuestions || !viewRaw || !error) {
            return;
        }

        modal.classList.remove('hidden');
        modal.style.display = '';
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('overflow-hidden');
        const scrollContainer = modal.querySelector('.overflow-y-auto');
        if (scrollContainer) scrollContainer.scrollTop = 0;
        loading.classList.remove('hidden');
        if (meta) meta.classList.add('hidden');
        tabs.classList.add('hidden');
        views.classList.add('hidden');
        viewAnalysis.innerHTML = '';
        viewReco.innerHTML = '';
        viewQuestions.innerHTML = '';
        viewRaw.innerHTML = '';
        error.classList.add('hidden');

        const escapeHtml = (s) => String(s)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');

        const parseInsight = (raw) => {
            const cleaned = String(raw || '').replace(/\r\n/g, '\n');
            const stopIdx = cleaned.indexOf('AKHIR_INSIGHT');
            const textOnly = stopIdx >= 0 ? cleaned.slice(0, stopIdx).trim() : cleaned.trim();

            const headings = [
                'Ringkasan:',
                'Diagnosis Utama:',
                'Area Perhatian:',
                'Temuan Data:',
                'Rekomendasi Peserta:',
                'Rekomendasi Trainer/Materi:',
                'Rekomendasi Assessment/Soal:',
                'Rencana 7 Hari:',
            ];

            const idxs = headings
                .map(h => ({ h, i: textOnly.indexOf(h) }))
                .filter(x => x.i >= 0)
                .sort((a, b) => a.i - b.i);

            if (idxs.length === 0) {
                return { sections: [], raw: textOnly };
            }

            const sections = [];

            for (let k = 0; k < idxs.length; k++) {
                const start = idxs[k];
                const end = idxs[k + 1];
                const bodyStart = start.i + start.h.length;
                const bodyEnd = end ? end.i : textOnly.length;
                const body = textOnly.slice(bodyStart, bodyEnd).trim();
                sections.push({ title: start.h.replace(':', ''), body });
            }

            return { sections, raw: textOnly };
        };

        const renderSection = (title, body) => {
            const lines = String(body || '').split('\n').map(l => l.trim()).filter(Boolean);
            const bullets = lines.filter(l => l.startsWith('-'));
            const plain = bullets.length > 0 ? lines.filter(l => !l.startsWith('-')) : lines;

            const chips = title === 'Area Perhatian'
                ? String(body || '').split(';').map(s => s.trim()).filter(Boolean)
                : [];

            const titleHtml = `<h4 class="text-[11px] font-black uppercase tracking-widest text-slate-500 mb-3">${escapeHtml(title)}</h4>`;

            const chipsHtml = chips.length > 0
                ? `<div class="flex flex-wrap gap-2 mb-3">${chips.map(c => `<span class="bg-slate-100 border border-slate-200 text-slate-700 px-3 py-1 rounded-xl text-[11px] font-bold">${escapeHtml(c)}</span>`).join('')}</div>`
                : '';

            const plainHtml = plain.length > 0
                ? `<p class="text-slate-800 text-sm font-medium leading-relaxed whitespace-pre-line">${escapeHtml(plain.join('\n'))}</p>`
                : '';

            const bulletsHtml = bullets.length > 0
                ? `<ul class="list-disc pl-5 space-y-2 text-sm font-medium text-slate-800">${bullets.map(b => `<li>${escapeHtml(b.replace(/^-\s*/, ''))}</li>`).join('')}</ul>`
                : '';

            return `<section class="bg-white/70 border border-slate-200 rounded-2xl p-6">${titleHtml}${chipsHtml}${plainHtml}${bulletsHtml}</section>`;
        };

        const renderMeta = (m) => {
            if (!m || !m.participants || !m.quiz) return '';

            const p = m.participants;
            const q = m.quiz;
            const distribution = p.distribution || {};

            const chip = (label, value, tone) => {
                const classes = tone === 'violet'
                    ? 'bg-violet-50 border-violet-200 text-violet-700'
                    : tone === 'emerald'
                        ? 'bg-emerald-50 border-emerald-200 text-emerald-700'
                        : tone === 'rose'
                            ? 'bg-rose-50 border-rose-200 text-rose-700'
                            : 'bg-slate-100 border-slate-200 text-slate-700';

                return `<span class="${classes} border px-3 py-1 rounded-xl text-[11px] font-black uppercase tracking-widest">${escapeHtml(label)} ${escapeHtml(value)}</span>`;
            };

            const row = (label, value) => `<div class="flex items-start justify-between gap-4"><span class="text-xs font-bold text-slate-500">${escapeHtml(label)}</span><span class="text-xs font-black text-slate-800 text-right">${escapeHtml(value)}</span></div>`;

            return `
                <section class="bg-white/70 border border-slate-200 rounded-2xl p-6">
                    <h4 class="text-[11px] font-black uppercase tracking-widest text-slate-500 mb-3">Ringkasan Data</h4>
                    <div class="flex flex-wrap gap-2 mb-4">
                        ${chip('PASS', String(q.passing_score ?? '-'), 'slate')}
                        ${chip('COMPLETION', `${p.completion_rate ?? 0}%`, 'violet')}
                        ${chip('AVG', `${p.avg_score ?? 0}%`, 'slate')}
                        ${chip('LULUS', String(p.passed ?? 0), 'emerald')}
                        ${chip('TIDAK LULUS', String(p.failed ?? 0), 'rose')}
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div class="bg-white border border-slate-200 rounded-xl p-4 space-y-2">
                            ${row('Total peserta', String(p.total ?? 0))}
                            ${row('Selesai', String(p.finished ?? 0))}
                            ${row('Tertinggi', String(p.highest ?? 0))}
                            ${row('Terendah', String(p.lowest ?? 0))}
                        </div>
                        <div class="bg-white border border-slate-200 rounded-xl p-4 space-y-2">
                            ${row('Distribusi Low (0-50)', String(distribution.low ?? 0))}
                            ${row('Distribusi Mid (51-75)', String(distribution.mid ?? 0))}
                            ${row('Distribusi High (76-100)', String(distribution.high ?? 0))}
                        </div>
                    </div>
                </section>
            `;
        };

        const renderQuestions = (m) => {
            const hardest = (m && m.questions && m.questions.hardest) ? m.questions.hardest : [];
            const easiest = (m && m.questions && m.questions.easiest) ? m.questions.easiest : [];

            const chip = (label, value, tone) => {
                const classes = tone === 'emerald'
                    ? 'bg-emerald-50 border-emerald-200 text-emerald-700'
                    : tone === 'rose'
                        ? 'bg-rose-50 border-rose-200 text-rose-700'
                        : 'bg-slate-100 border-slate-200 text-slate-700';

                return `<span class="${classes} border px-3 py-1 rounded-xl text-[11px] font-black uppercase tracking-widest">${escapeHtml(label)} ${escapeHtml(value)}</span>`;
            };

            const item = (it) => `<div class="bg-white border border-slate-200 rounded-xl p-4">
                <p class="text-xs font-black text-slate-900 mb-2">${escapeHtml(it.text || '-')}</p>
                <div class="flex flex-wrap gap-2">
                    ${chip('BENAR', `${it.correct_rate ?? 0}%`, 'emerald')}
                    ${chip('DIJAWAB', `${it.answered ?? 0}`, 'slate')}
                </div>
            </div>`;

            return `
                <section class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-white/70 border border-slate-200 rounded-2xl p-6">
                        <h4 class="text-[11px] font-black uppercase tracking-widest text-rose-600 mb-3">3 Soal Tersulit</h4>
                        <div class="space-y-3">${hardest.map(item).join('')}</div>
                    </div>
                    <div class="bg-white/70 border border-slate-200 rounded-2xl p-6">
                        <h4 class="text-[11px] font-black uppercase tracking-widest text-emerald-600 mb-3">3 Soal Termudah</h4>
                        <div class="space-y-3">${easiest.map(item).join('')}</div>
                    </div>
                </section>
            `;
        };

        const setActiveTab = (name) => {
            const allTabs = Array.from(document.querySelectorAll('#aiInsightTabs .ai-tab'));
            allTabs.forEach(btn => {
                const isActive = btn.dataset.aiTab === name;
                btn.classList.toggle('bg-white', isActive);
                btn.classList.toggle('text-violet-700', isActive);
                btn.classList.toggle('shadow-sm', isActive);
                btn.classList.toggle('text-slate-500', !isActive);
            });

            viewAnalysis.classList.toggle('hidden', name !== 'analysis');
            viewReco.classList.toggle('hidden', name !== 'reco');
            viewQuestions.classList.toggle('hidden', name !== 'questions');
            viewRaw.classList.toggle('hidden', name !== 'raw');
        };

        const bindTabClicks = () => {
            document.querySelectorAll('#aiInsightTabs .ai-tab').forEach(btn => {
                if (btn.dataset.bound === '1') return;
                btn.dataset.bound = '1';
                btn.addEventListener('click', () => setActiveTab(btn.dataset.aiTab));
            });
        };

        try {
            const res = await fetch('{{ route('admin.quiz.ai-insights', $quiz->slug) }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            if (!res.ok) {
                if (res.status === 419) {
                    throw new Error('Sesi habis. Refresh halaman lalu coba lagi.');
                }

                const ct = res.headers.get('content-type') || '';
                if (ct.includes('application/json')) {
                    const j = await res.json();
                    throw new Error(j.error || ('Gagal memuat insight. (HTTP ' + res.status + ')'));
                }

                const t = await res.text();
                const snippet = String(t || '').replace(/\s+/g, ' ').trim().slice(0, 280);
                throw new Error('Gagal memuat insight. (HTTP ' + res.status + ') ' + snippet);
            }

            const ct = res.headers.get('content-type') || '';
            if (!ct.includes('application/json')) {
                throw new Error('Respons bukan JSON. Silakan refresh halaman lalu coba lagi.');
            }

            const data = await res.json();
            
            if (data.error) {
                error.textContent = data.error;
                error.classList.remove('hidden');
            } else {
                const parsed = parseInsight(data.insight);
                const metaHtml = renderMeta(data.meta);
                if (meta) {
                    meta.innerHTML = metaHtml;
                    if (metaHtml) meta.classList.remove('hidden');
                }

                const analysisTitles = new Set(['Ringkasan', 'Diagnosis Utama', 'Area Perhatian', 'Temuan Data']);
                const recoTitles = new Set(['Rekomendasi Peserta', 'Rekomendasi Trainer/Materi', 'Rekomendasi Assessment/Soal', 'Rencana 7 Hari']);

                const analysisSections = parsed.sections.filter(s => analysisTitles.has(s.title));
                const recoSections = parsed.sections.filter(s => recoTitles.has(s.title));
                const otherSections = parsed.sections.filter(s => !analysisTitles.has(s.title) && !recoTitles.has(s.title));

                const renderSectionsBlock = (sections) => {
                    if (sections.length === 0) return '';
                    return `<div class="space-y-4">${sections.map(s => renderSection(s.title, s.body)).join('')}</div>`;
                };

                viewAnalysis.innerHTML = renderSectionsBlock(analysisSections) || `<section class="bg-white/70 border border-slate-200 rounded-2xl p-6"><p class="text-slate-800 text-sm font-medium leading-relaxed">Insight belum memiliki bagian Analisis.</p></section>`;
                viewReco.innerHTML = renderSectionsBlock(recoSections) || `<section class="bg-white/70 border border-slate-200 rounded-2xl p-6"><p class="text-slate-800 text-sm font-medium leading-relaxed">Insight belum memiliki bagian Rekomendasi.</p></section>`;
                viewQuestions.innerHTML = renderQuestions(data.meta);

                const rawBlocks = [];
                if (otherSections.length > 0) {
                    rawBlocks.push(renderSectionsBlock(otherSections));
                }
                rawBlocks.push(`<section class="bg-white/70 border border-slate-200 rounded-2xl p-6"><h4 class="text-[11px] font-black uppercase tracking-widest text-slate-500 mb-3">Teks Lengkap</h4><p class="text-slate-800 text-sm font-medium leading-relaxed whitespace-pre-line">${escapeHtml(parsed.raw)}</p></section>`);
                viewRaw.innerHTML = rawBlocks.join('');

                tabs.classList.remove('hidden');
                views.classList.remove('hidden');
                bindTabClicks();
                setActiveTab('analysis');
                if (scrollContainer) scrollContainer.scrollTop = 0;
            }
        } catch (e) {
            error.textContent = e?.message || "Gagal memuat insight. Coba lagi.";
            error.classList.remove('hidden');
        } finally {
            loading.classList.add('hidden');
        }
    };

    const bindAiInsightButton = () => {
        const btnAiInsight = document.getElementById('btnAiInsight');
        if (!btnAiInsight) return;
        if (btnAiInsight.dataset.bound === '1') return;
        btnAiInsight.dataset.bound = '1';
        btnAiInsight.addEventListener('click', window.pahamajaOpenAiInsight);
    };

    const closeAiInsightModal = () => {
        const m = document.getElementById('aiInsightModal');
        if (m) {
            m.classList.add('hidden');
            m.style.display = 'none';
            m.setAttribute('aria-hidden', 'true');
        }
        document.body.classList.remove('overflow-hidden');
    };

    const closeBtn = document.getElementById('btnCloseAiInsightModal');
    if (closeBtn) closeBtn.addEventListener('click', closeAiInsightModal);

    const backdrop = document.getElementById('aiInsightBackdrop');
    if (backdrop) backdrop.addEventListener('click', closeAiInsightModal);

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeAiInsightModal();
        }
    });

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bindAiInsightButton);
    } else {
        bindAiInsightButton();
    }

    (function () {
        const isLocal = @json(app()->environment('local'));
        if (!isLocal) return;

        const modal = document.getElementById('restoreDataModal');
        const btnOpen = document.getElementById('btnRestoreData');
        const btnClose = document.getElementById('btnCloseRestoreDataModal');
        const backdrop = document.getElementById('restoreDataBackdrop');
        const btnSeedOnly = document.getElementById('btnRestoreSeedOnly');
        const btnClearSeed = document.getElementById('btnRestoreClearSeed');
        const status = document.getElementById('restoreDataStatus');
        const baseUrl = @json(route('admin.force-seed'));

        if (!modal || !btnOpen || !btnClose || !backdrop || !btnSeedOnly || !btnClearSeed || !status) return;

        const open = () => {
            modal.classList.remove('hidden');
            modal.style.display = '';
            modal.setAttribute('aria-hidden', 'false');
            document.body.classList.add('overflow-hidden');
            status.classList.add('hidden');
            status.innerHTML = '';
        };

        const close = () => {
            if (modal.classList.contains('hidden')) return;
            modal.classList.add('hidden');
            modal.style.display = 'none';
            modal.setAttribute('aria-hidden', 'true');
            const aiModal = document.getElementById('aiInsightModal');
            const aiOpen = aiModal && !aiModal.classList.contains('hidden') && aiModal.getAttribute('aria-hidden') !== 'true';
            if (!aiOpen) {
                document.body.classList.remove('overflow-hidden');
            }
        };

        const setBusy = (busy) => {
            [btnSeedOnly, btnClearSeed].forEach((b) => {
                b.disabled = !!busy;
                b.classList.toggle('opacity-60', !!busy);
                b.classList.toggle('cursor-not-allowed', !!busy);
            });
        };

        const run = async (clear) => {
            setBusy(true);
            status.classList.remove('hidden');
            status.innerHTML = `
                <div class="flex items-center gap-3 text-slate-500 bg-white/70 border border-slate-200 rounded-2xl p-4">
                    <svg class="animate-spin w-5 h-5 text-amber-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    <span class="text-sm font-semibold">Menjalankan pemulihan data...</span>
                </div>
            `;

            try {
                const url = new URL(baseUrl, window.location.origin);
                if (clear) url.searchParams.set('clear', '1');

                const res = await fetch(url.toString(), {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                });

                const payload = await res.json().catch(() => null);
                const ok = res.ok && payload && payload.status === 'success';

                status.innerHTML = ok
                    ? `
                        <div class="bg-emerald-50 border border-emerald-100 rounded-2xl p-5">
                            <p class="text-sm font-black text-emerald-800">Sukses</p>
                            <p class="text-sm font-semibold text-emerald-700 mt-1">${payload.message || 'Pemulihan data selesai.'}</p>
                            <div class="mt-4 flex flex-col sm:flex-row gap-3">
                                <button type="button" id="btnRestoreReload" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-2xl font-black text-xs active:scale-95">Refresh Dashboard</button>
                                <button type="button" id="btnRestoreCloseAfterSuccess" class="bg-white border border-emerald-200 text-emerald-700 hover:bg-emerald-50 px-6 py-3 rounded-2xl font-black text-xs active:scale-95">Tutup</button>
                            </div>
                        </div>
                    `
                    : `
                        <div class="bg-rose-50 border border-rose-100 rounded-2xl p-5">
                            <p class="text-sm font-black text-rose-700">Gagal</p>
                            <p class="text-sm font-semibold text-rose-600 mt-1">${(payload && (payload.message || payload.error)) ? (payload.message || payload.error) : 'Tidak bisa menjalankan pemulihan data.'}</p>
                            <p class="text-xs font-semibold text-rose-500 mt-2">Cek akses admin / environment local / log aplikasi.</p>
                        </div>
                    `;

                const reloadBtn = document.getElementById('btnRestoreReload');
                if (reloadBtn) reloadBtn.addEventListener('click', () => window.location.reload());
                const closeAfter = document.getElementById('btnRestoreCloseAfterSuccess');
                if (closeAfter) closeAfter.addEventListener('click', close);
            } catch (e) {
                status.innerHTML = `
                    <div class="bg-rose-50 border border-rose-100 rounded-2xl p-5">
                        <p class="text-sm font-black text-rose-700">Gagal</p>
                        <p class="text-sm font-semibold text-rose-600 mt-1">${e?.message || 'Terjadi error.'}</p>
                    </div>
                `;
            } finally {
                setBusy(false);
            }
        };

        btnOpen.addEventListener('click', open);
        btnClose.addEventListener('click', close);
        backdrop.addEventListener('click', close);

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') close();
        });

        btnSeedOnly.addEventListener('click', () => run(false));
        btnClearSeed.addEventListener('click', () => run(true));
    })();

    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js').catch(err => console.log('SW registration failed:', err));
        });
    }
</script>

<footer class="py-12 border-t border-slate-200 bg-white">
    <div class="container mx-auto px-6 max-w-[1500px]">
        <div class="flex flex-col md:flex-row justify-between items-center gap-8">
            <div class="flex items-center gap-3">
                <div class="bg-slate-900 w-10 h-10 rounded-xl flex items-center justify-center font-black text-xl italic text-white shadow-lg">P</div>
                <div>
                    <h2 class="text-xl font-bold text-slate-900">PahamAja</h2>
                    <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">Corporate Analytics Suite</p>
                </div>
            </div>
            
            <div class="flex flex-wrap justify-center gap-10">
                <div class="flex flex-col gap-3">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Platform</p>
                    <a href="{{ route('admin.dashboard') }}" class="text-xs font-bold text-slate-600 hover:text-indigo-600 transition-colors">Assessment Center</a>
                    <a href="{{ route('admin.quizzes.index') }}" class="text-xs font-bold text-slate-600 hover:text-indigo-600 transition-colors">Quiz Builder</a>
                </div>
                <div class="flex flex-col gap-3">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Support</p>
                    <a href="#" class="text-xs font-bold text-slate-600 hover:text-indigo-600 transition-colors">Documentation</a>
                    <a href="#" class="text-xs font-bold text-slate-600 hover:text-indigo-600 transition-colors">Enterprise Help</a>
                </div>
            </div>
            
            <div class="text-right">
                <p class="text-xs font-black text-slate-900 mb-1">System Operational</p>
                <div class="flex items-center justify-end gap-2">
                    <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">All services normal</span>
                </div>
            </div>
        </div>
        
        <div class="mt-12 pt-8 border-t border-slate-100 flex flex-col md:flex-row justify-between items-center gap-4">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">&copy; 2026 PahamAja. Industrial Grade Assessment Engine.</p>
            <div class="flex gap-6">
                <a href="#" class="text-[10px] font-bold text-slate-400 hover:text-slate-600 uppercase tracking-widest">Terms</a>
                <a href="#" class="text-[10px] font-bold text-slate-400 hover:text-slate-600 uppercase tracking-widest">Privacy</a>
            </div>
        </div>
    </div>
</footer>

<script>
    function updateDashboard(data) {
        // Skeleton effect
        const targets = ['statAvgScore', 'statInProgress', 'statCompleted', 'statLiveActivity', 'liveLeaderboard'];
        targets.forEach(id => {
            const el = document.getElementById(id);
            if (el) el.classList.add('opacity-50', 'animate-pulse');
        });

        setTimeout(() => {
            if (document.getElementById('statAvgScore')) document.getElementById('statAvgScore').innerText = data.avgScore + '%';
            if (document.getElementById('statInProgress')) document.getElementById('statInProgress').innerText = data.inProgressCount;
            if (document.getElementById('statCompleted')) document.getElementById('statCompleted').innerText = data.completedCount;
            if (document.getElementById('statLiveActivity')) document.getElementById('statLiveActivity').innerText = data.liveActivity + ' Live Activity';

            // Rebuild Leaderboard
            const tbody = document.getElementById('liveLeaderboard');
            if (tbody && data.participants) {
                tbody.innerHTML = data.participants.map((p, index) => {
                    const rankBadge = index === 0 && p.score !== null ? `<div class="w-10 h-10 bg-amber-100 text-amber-600 rounded-2xl flex items-center justify-center font-black">1</div>`
                        : index === 1 && p.score !== null ? `<div class="w-10 h-10 bg-slate-100 text-slate-500 rounded-2xl flex items-center justify-center font-black">2</div>`
                        : index === 2 && p.score !== null ? `<div class="w-10 h-10 bg-orange-50 text-orange-600 rounded-2xl flex items-center justify-center font-black">3</div>`
                        : `<span class="text-slate-400 font-bold text-sm ml-4 italic">#${index + 1}</span>`;
                    
                    const scoreMarkup = p.score !== null 
                        ? `<div class="inline-flex items-center gap-2 bg-slate-900 text-white px-4 py-2 rounded-2xl">
                                <span class="text-xs font-black">${p.score}</span>
                                <div class="w-20 h-1 bg-white/10 rounded-full overflow-hidden">
                                    <div class="h-full bg-indigo-500" style="width: ${p.score}%"></div>
                                </div>
                           </div>`
                        : `<span class="px-4 py-2 bg-amber-50 text-amber-600 rounded-2xl text-[10px] font-black uppercase tracking-widest animate-pulse">Mengerjakan</span>`;
                    
                    const statusMarkup = p.score !== null
                        ? `<span class="text-[10px] font-black uppercase tracking-[0.2em] ${p.is_passing ? 'text-emerald-500' : 'text-rose-500'}">
                                ${p.is_passing ? 'Lulus' : 'Tidak Lulus'}
                           </span>`
                        : `<span class="text-slate-300 italic text-[10px] font-bold">Sedang Sinkronisasi...</span>`;

                    const ansUrl = `/admin/quiz/${'{{$quiz->slug}}'}/participant/${p.id}/answers`;
                    const delUrl = `/admin/quiz/${'{{$quiz->slug}}'}/participant/${p.id}`;
                    const csrf = '{{ csrf_token() }}';

                    return `
                    <tr class="block lg:table-row bg-white hover:bg-indigo-50/30 transition-all duration-300 p-5 lg:p-0 my-4 lg:my-0 border border-slate-100 lg:border-none rounded-2xl lg:rounded-none shadow-sm lg:shadow-none group">
                        <td class="block lg:table-cell lg:px-10 py-2 lg:py-6 border-b border-slate-50 lg:border-none">
                            <div class="flex items-center justify-between lg:justify-start">
                                <span class="lg:hidden text-[10px] text-slate-400 font-black uppercase tracking-widest">Rank</span>
                                ${rankBadge}
                            </div>
                        </td>
                        <td class="block lg:table-cell lg:px-10 py-2 lg:py-6 border-b border-slate-50 lg:border-none">
                            <div class="flex lg:block items-center justify-between">
                                <span class="lg:hidden text-[10px] text-slate-400 font-black uppercase tracking-widest">Identity</span>
                                <div class="text-right lg:text-left">
                                    <p class="text-slate-800 font-black tracking-tight">${p.name}</p>
                                    <p class="text-slate-400 text-xs font-bold">${p.nim}</p>
                                </div>
                            </div>
                        </td>
                        <td class="block lg:table-cell lg:px-10 py-2 lg:py-6 border-b border-slate-50 lg:border-none">
                            <div class="flex items-center justify-between lg:justify-start">
                                <span class="lg:hidden text-[10px] text-slate-400 font-black uppercase tracking-widest">Session</span>
                                <span class="text-xs font-bold text-slate-600">${p.session || '-'}</span>
                            </div>
                        </td>
                        <td class="block lg:table-cell lg:px-10 py-2 lg:py-6 border-b border-slate-50 lg:border-none">
                            <div class="flex items-center justify-between lg:justify-start">
                                <span class="lg:hidden text-[10px] text-slate-400 font-black uppercase tracking-widest">Score</span>
                                ${scoreMarkup}
                            </div>
                        </td>
                        <td class="block lg:table-cell lg:px-10 py-2 lg:py-6 border-b border-slate-50 lg:border-none">
                            <div class="flex items-center justify-between lg:justify-start">
                                <span class="lg:hidden text-[10px] text-slate-400 font-black uppercase tracking-widest">Status</span>
                                <div class="flex flex-col text-right lg:text-left">
                                    ${statusMarkup}
                                    ${p.duration ? `<span class="text-[9px] font-bold text-slate-400 mt-1">🕒 ${p.duration}</span>` : ''}
                                </div>
                            </div>
                        </td>
                        <td class="block lg:table-cell lg:px-10 pt-4 pb-2 lg:py-6 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="${ansUrl}" class="text-slate-400 hover:text-indigo-600 transition-all p-2 hover:bg-indigo-50 rounded-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </a>
                                <form action="${delUrl}" method="POST" onsubmit="return confirm('Hapus data peserta ini?')">
                                    <input type="hidden" name="_token" value="${csrf}">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="submit" class="text-rose-400 hover:text-rose-600 transition-all p-2 hover:bg-rose-50 rounded-lg">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>`;
                }).join('');
                if (data.participants.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="5" class="px-10 py-20 text-center"><p class="text-slate-400 font-bold text-sm tracking-tight italic">Belum ada peserta.</p></td></tr>`;
                }
            }

            // Remove Shimmer
            targets.forEach(id => {
                const el = document.getElementById(id);
                if (el) el.classList.remove('opacity-50', 'animate-pulse');
            });
        }, 500);
    }

    (function () {
        const url = @json(route('admin.quiz.dashboard', $quiz->slug));

        const poll = async () => {
            try {
                const res = await fetch(url, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                });

                if (!res.ok) return;

                const data = await res.json();
                if (!data) return;
                updateDashboard(data);
            } catch (_) {
            }
        };

        poll();
        setInterval(poll, 5000);
    })();
</script>

<script src="{{ asset('js/prevent-double-submit.js') }}"></script>
</body>
</html>
