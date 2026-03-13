<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PahamAja Enterprise - Corporate Intelligence</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Outfit', sans-serif;
            background: #ffffff;
            color: #0f172a;
        }
        .sidebar {
            background: #0f172a;
            border-right: 1px solid #1e293b;
        }
        .card-enterprise {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
            transition: all 0.2s ease;
        }
        .card-enterprise:hover {
            border-color: #cbd5e1;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        }
        .btn-primary {
            background: #4f46e5;
            transition: all 0.2s ease;
        }
        .btn-primary:hover {
            background: #4338ca;
            transform: translateY(-1px);
        }
        .status-badge {
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
    </style>
</head>
<body class="min-h-screen bg-gray-50/50">
    <div class="flex">
        <!-- Enterprise Sidebar -->
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
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 bg-white/5 border border-white/10 text-white p-3 rounded-xl transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    <span class="text-sm font-semibold">Active Assessments</span>
                </a>
                <a href="{{ route('admin.employees.index') }}" class="flex items-center gap-3 text-slate-400 hover:bg-white/5 hover:text-white p-3 rounded-xl transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    <span class="text-sm font-medium">Employee Master Data</span>
                </a>
                <a href="{{ route('admin.employees.index') }}" class="flex items-center gap-3 text-slate-400 hover:bg-white/5 hover:text-white p-3 rounded-xl transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    <span class="text-sm font-medium">Growth Reports</span>
                </a>
            </nav>

            <div class="absolute bottom-6 left-6 right-6">
                <div class="bg-indigo-600/10 p-4 rounded-2xl border border-indigo-500/20">
                    <p class="text-[9px] text-indigo-400 font-black uppercase tracking-widest mb-1">Corporate System</p>
                    <p class="text-xs font-bold text-white mb-3">Enterprise Version 2.0</p>
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                        <span class="text-[9px] text-indigo-300 font-bold uppercase">System Operational</span>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-10 max-w-[1600px] mx-auto">
            <header class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-6">
                <div>
                    <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Enterprise Assessment Suite</h2>
                    <p class="text-slate-500 mt-1 font-medium text-sm">Monitor employee technical competence and knowledge alignment.</p>
                </div>
                
                <div class="flex items-center gap-4">
                    <button class="bg-white border border-slate-200 text-slate-700 px-6 py-3 rounded-xl font-bold text-sm hover:bg-gray-50 transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span>Download Summary</span>
                    </button>
                    <a href="{{ route('admin.quizzes.create') }}" 
                       class="btn-primary text-white px-8 py-3 rounded-xl font-bold text-sm shadow-lg shadow-indigo-600/20 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        <span>New Deployment</span>
                    </a>
                </div>
            </header>

            @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-6 py-4 rounded-xl mb-8 flex items-center justify-between animate-fade-in">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="font-bold text-sm">{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-emerald-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
                <!-- Stats Overview -->
                <div class="bg-white border border-slate-200 p-8 rounded-2xl">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Fleet Coverage</p>
                    <h3 class="text-3xl font-black text-slate-900 mb-6">{{ $quizzes->count() }} Deployments</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center text-sm font-bold">
                            <span class="text-slate-500">Active Integrity Check</span>
                            <span class="text-indigo-600">ON</span>
                        </div>
                        <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full bg-indigo-500" style="width: 100%"></div>
                        </div>
                    </div>
                </div>
                <div class="bg-indigo-600 p-8 rounded-2xl text-white shadow-xl shadow-indigo-600/10">
                    <p class="text-[10px] font-black text-indigo-200 uppercase tracking-widest mb-1">Human Capital</p>
                    <h3 class="text-3xl font-black mb-6">{{ \App\Models\Employee::count() }} Registered</h3>
                    <p class="text-sm font-medium text-indigo-100 italic leading-relaxed">System using AI normalization to match incoming participant data.</p>
                </div>
                <div class="bg-white border border-slate-200 p-8 rounded-2xl">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Average Integrity</p>
                    <h3 class="text-3xl font-black text-slate-900 mb-6">94.2%</h3>
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 bg-emerald-500 rounded-full"></div>
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-tight">Optimal Compliance</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @forelse($quizzes as $quiz)
                <div class="card-enterprise rounded-2xl overflow-hidden flex flex-col">
                    <div class="p-8 flex-1">
                        <div class="flex justify-between items-start mb-6">
                            <div class="status-badge bg-indigo-50 text-indigo-600 px-3 py-1 rounded-lg">Internal Training</div>
                            <form action="{{ route('admin.quizzes.destroy', $quiz->id) }}" method="POST" onsubmit="return confirm('Secure delete this deployment?')">
                                @csrf
                                @method('DELETE')
                                <button class="text-slate-300 hover:text-rose-600 transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>
                        
                        <h3 class="text-xl font-bold text-slate-900 mb-2 truncate group-hover:text-indigo-600 transition-all line-clamp-2 h-14 uppercase tracking-tight">{{ $quiz->title }}</h3>
                        
                        <div class="flex items-center gap-2 text-slate-500 font-bold text-xs mb-8">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>Updated {{ $quiz->updated_at?->diffForHumans() ?? 'Just now' }}</span>
                        </div>

                        <div class="grid grid-cols-2 gap-4 border-t border-slate-100 pt-6">
                            <div>
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Participants</p>
                                <p class="text-lg font-black text-slate-900">{{ $quiz->participants()->count() }}</p>
                            </div>
                            <div>
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Quality Gate</p>
                                <p class="text-lg font-black text-indigo-600">{{ $quiz->passing_score }}%</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-2 grid grid-cols-2 gap-2 bg-slate-50/50 border-t border-slate-100">
                        <a href="{{ route('admin.quizzes.show', $quiz->slug) }}" 
                           class="bg-white border border-slate-200 text-slate-700 py-3 rounded-xl font-bold text-xs text-center hover:bg-gray-50 transition-all">
                            Configure
                        </a>
                        <a href="{{ route('admin.quiz.dashboard', $quiz->slug) }}" 
                           class="bg-indigo-600 text-white py-3 rounded-xl font-bold text-xs text-center hover:bg-indigo-700 transition-all shadow-md shadow-indigo-600/10">
                            Analytics
                        </a>
                    </div>
                </div>
                @empty
                <div class="col-span-full py-32 flex flex-col items-center justify-center text-center">
                    <div class="bg-gray-100 w-24 h-24 rounded-3xl flex items-center justify-center mb-6 text-slate-300">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <h3 class="text-2xl font-black text-slate-900 tracking-tight">Deploy First Assessment</h3>
                    <p class="text-slate-500 mt-2 mb-10 max-w-sm font-medium">No professional assessments found in the system. Use the button above to begin.</p>
                </div>
                @endforelse
            </div>
        </main>
    </div>
</body>
</html>
