<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $quiz->title }} - Professional Insights</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            animation: slideUp 0.5s ease-out forwards;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="min-h-screen flex flex-col bg-slate-50">

<div class="flex-grow">
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
        
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.quiz.export', $quiz->slug) }}" 
               class="bg-slate-900 hover:bg-indigo-600 text-white px-8 py-4 rounded-[1.5rem] font-bold shadow-xl shadow-slate-200 transition-all flex items-center gap-3 active:scale-95 text-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <span>Export Analysis</span>
            </a>
            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button type="submit" class="bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 px-6 py-4 rounded-[1.5rem] font-bold shadow-sm transition-all flex items-center gap-3 active:scale-95 text-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1"></path></svg>
                    <span>Logout</span>
                </button>
            </form>
            <div class="bg-indigo-50 border border-indigo-100/50 px-6 py-4 rounded-[1.5rem] flex items-center gap-3">
                <span class="relative flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-indigo-500"></span>
                </span>
                <span class="text-indigo-700 font-extrabold text-sm">{{ $participants->count() }} Live Activity</span>
            </div>
        </div>
    </header>

    <!-- Professional Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
        <div class="glass-card p-8 rounded-[2rem] animate-slide-up" style="animation-delay: 0.1s">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center bg-indigo-50 text-indigo-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                </div>
                <p class="text-slate-400 font-bold text-xs uppercase tracking-widest">Performance</p>
            </div>
            <h3 class="text-3xl font-black text-slate-800 tracking-tight">{{ number_format($avgScore, 1) }}%</h3>
        </div>
        <div class="glass-card p-8 rounded-[2rem] animate-slide-up" style="animation-delay: 0.2s">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center bg-violet-50 text-violet-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <p class="text-slate-400 font-bold text-xs uppercase tracking-widest">Active Now</p>
            </div>
            <h3 class="text-3xl font-black text-slate-800 tracking-tight">{{ $inProgressCount }}</h3>
        </div>
        <div class="glass-card p-8 rounded-[2rem] animate-slide-up" style="animation-delay: 0.3s">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center bg-blue-50 text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.158-2.046-.452-2.992z"></path></svg>
                </div>
                <p class="text-slate-400 font-bold text-xs uppercase tracking-widest">Quality Gate</p>
            </div>
            <h3 class="text-3xl font-black text-slate-800 tracking-tight">{{ $quiz->passing_score }}</h3>
        </div>
        <div class="glass-card p-8 rounded-[2rem] animate-slide-up" style="animation-delay: 0.4s">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center bg-emerald-50 text-emerald-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                </div>
                <p class="text-slate-400 font-bold text-xs uppercase tracking-widest">Completed</p>
            </div>
            <h3 class="text-3xl font-black text-slate-800 tracking-tight">{{ $participants->whereNotNull('score')->count() }}</h3>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
        <!-- Premium Share -->
        <div class="glass-card p-8 rounded-[2.5rem] flex flex-col items-center justify-center text-center animate-slide-up" style="animation-delay: 0.5s">
            <p class="text-xs font-black text-indigo-500 uppercase tracking-[0.3em] mb-8 leading-none">Access Point</p>
            
            <div class="relative mb-10 group">
                <div class="absolute -inset-4 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-[2.5rem] blur-xl opacity-10 group-hover:opacity-20 transition-all duration-500"></div>
                <div class="relative bg-white p-6 rounded-[2rem] shadow-xl border border-slate-100">
                    {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(180)
                        ->color(15, 23, 42)
                        ->margin(1)
                        ->generate(route('quiz.join', $quiz->slug)) !!}
                </div>
            </div>
            
            <p class="text-slate-400 text-sm font-medium mb-10 leading-relaxed max-w-[200px]">Admit participants via direct entry point or QR.</p>
            
            <div class="w-full flex bg-slate-100/50 p-2 rounded-[1.5rem] border border-slate-200">
                <input type="text" id="quizLink" value="{{ url('/quiz/'.$quiz->slug) }}" 
                       class="flex-1 bg-transparent px-4 py-2 text-xs font-bold text-slate-500 focus:outline-none" readonly>
                <button onclick="copyLink()" 
                        class="bg-white hover:bg-indigo-600 hover:text-white text-indigo-600 px-6 py-2.5 rounded-[1.2rem] text-xs font-black transition-all shadow-sm active:scale-95">
                    Copy
                </button>
            </div>
        </div>

        <!-- Professional Chart -->
        <div class="glass-card p-10 rounded-[2.5rem] lg:col-span-2 flex flex-col animate-slide-up" style="animation-delay: 0.6s">
            <div class="flex justify-between items-center mb-10">
                <h2 class="text-2xl font-black text-slate-800 tracking-tight">Success Distribution</h2>
                <div class="flex gap-4">
                    <div class="flex items-center gap-2"><div class="w-3 h-3 rounded bg-emerald-500"></div><span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">High</span></div>
                    <div class="flex items-center gap-2"><div class="w-3 h-3 rounded bg-amber-500"></div><span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Mid</span></div>
                    <div class="flex items-center gap-2"><div class="w-3 h-3 rounded bg-rose-500"></div><span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Low</span></div>
                </div>
            </div>
            <div class="relative flex-1 w-full min-h-[300px]">
                <canvas id="scoreChart"></canvas>
            </div>
        </div>
    </div>

    <div class="glass-card rounded-[2.5rem] overflow-hidden mb-12 animate-slide-up" style="animation-delay: 0.65s">
        <div class="px-10 py-8 border-b border-slate-100 flex justify-between items-center bg-white/50">
            <h2 class="text-2xl font-black text-slate-800 tracking-tight">Item Analysis</h2>
            <span class="bg-slate-100 text-slate-500 text-[10px] font-black px-3 py-1.5 rounded-lg uppercase tracking-widest border border-slate-200">Per Question</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50/50 text-slate-400 text-[10px] font-black uppercase tracking-[0.2em]">
                    <tr>
                        <th class="px-10 py-6">Question</th>
                        <th class="px-10 py-6">Answered</th>
                        <th class="px-10 py-6">Correct Rate</th>
                        <th class="px-10 py-6">Top Choice</th>
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
                                            <div class="h-full bg-indigo-600" style="width: {{ $row['correct_rate'] }}%"></div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-slate-300 italic text-xs font-bold">No data</span>
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
                                <p class="text-slate-400 font-bold text-sm tracking-tight italic">No item data found.</p>
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
            <h2 class="text-2xl font-black text-slate-800 tracking-tight">System Logs & Top Rankings</h2>
            <div class="flex gap-2">
                <span class="bg-slate-100 text-slate-500 text-[10px] font-black px-3 py-1.5 rounded-lg uppercase tracking-widest border border-slate-200">Real-time Feed</span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50/50 text-slate-400 text-[10px] font-black uppercase tracking-[0.2em]">
                    <tr>
                        <th class="px-10 py-6">Rank</th>
                        <th class="px-10 py-6">Identity</th>
                        <th class="px-10 py-6">Performance</th>
                        <th class="px-10 py-6">Status</th>
                        <th class="px-10 py-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($participants as $index => $participant)
                    <tr class="hover:bg-indigo-50/30 transition-all duration-300 group">
                        <td class="px-10 py-6">
                            @if($index == 0 && !is_null($participant->score)) <div class="w-10 h-10 bg-amber-100 text-amber-600 rounded-2xl flex items-center justify-center font-black">1</div>
                            @elseif($index == 1 && !is_null($participant->score)) <div class="w-10 h-10 bg-slate-100 text-slate-500 rounded-2xl flex items-center justify-center font-black">2</div>
                            @elseif($index == 2 && !is_null($participant->score)) <div class="w-10 h-10 bg-orange-50 text-orange-600 rounded-2xl flex items-center justify-center font-black">3</div>
                            @else <span class="text-slate-400 font-bold text-sm ml-4 italic">#{{ $index + 1 }}</span>
                            @endif
                        </td>
                        <td class="px-10 py-6">
                            <p class="text-slate-800 font-black tracking-tight">{{ $participant->name }}</p>
                            <p class="text-slate-400 text-xs font-bold">{{ $participant->nim }}</p>
                        </td>
                        <td class="px-10 py-6">
                            @if(!is_null($participant->score))
                                <div class="inline-flex items-center gap-2 bg-slate-900 text-white px-4 py-2 rounded-2xl">
                                    <span class="text-xs font-black">{{ $participant->score }}</span>
                                    <div class="w-20 h-1 bg-white/10 rounded-full overflow-hidden">
                                        <div class="h-full bg-indigo-500" style="width: {{ $participant->score }}%"></div>
                                    </div>
                                </div>
                            @else
                                <span class="px-4 py-2 bg-amber-50 text-amber-600 rounded-2xl text-[10px] font-black uppercase tracking-widest animate-pulse">Running</span>
                            @endif
                        </td>
                        <td class="px-10 py-6">
                            @if(!is_null($participant->score))
                                <span class="text-[10px] font-black uppercase tracking-[0.2em] {{ $participant->score >= $quiz->passing_score ? 'text-emerald-500' : 'text-rose-500' }}">
                                    {{ $participant->score >= $quiz->passing_score ? 'Qualified' : 'Failed' }}
                                </span>
                            @else
                                <span class="text-slate-300 italic text-[10px] font-bold">Synchronizing...</span>
                            @endif
                        </td>
                        <td class="px-10 py-6 text-right">
                            <form action="{{ route('admin.participant.destroy', ['quiz' => $quiz->slug, 'participant' => $participant->id]) }}" method="POST" onsubmit="return confirm('Hapus data peserta ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-rose-400 hover:text-rose-600 transition-all p-2 hover:bg-rose-50 rounded-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-10 py-20 text-center">
                            <div class="bg-slate-50 w-20 h-20 rounded-[1.5rem] flex items-center justify-center mx-auto mb-6 text-slate-200">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            </div>
                            <p class="text-slate-400 font-bold text-sm tracking-tight italic">No participant telemetry found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    </div>
</div>

<footer class="mt-auto py-12 border-t border-slate-200 bg-white">
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

</body>
</html>
