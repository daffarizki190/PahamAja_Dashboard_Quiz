<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - PahamAja</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f7f7f5; color: #0f172a; }
        .font-serif { font-family: 'Fraunces', serif; }
        .sidebar {
            background: linear-gradient(180deg, #0b1220 0%, #0f172a 100%);
            border-right: 1px solid #1e293b;
        }
        .card-enterprise {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 2px 0 rgb(15 23 42 / 0.06), 0 12px 30px -20px rgb(15 23 42 / 0.18);
            transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
        }
        .card-enterprise:hover {
            border-color: #cbd5e1;
            box-shadow: 0 2px 6px 0 rgb(15 23 42 / 0.08), 0 18px 40px -22px rgb(15 23 42 / 0.22);
        }
        .btn-primary {
            background: #4f46e5;
            transition: background 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease;
        }
        .btn-primary:hover {
            background: #4338ca;
            transform: translateY(-1px);
        }
        .btn-ghost {
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.10);
        }
        .btn-ghost:hover {
            background: rgba(255, 255, 255, 0.10);
        }
        .chip { background: #f8fafc; border: 1px solid #e2e8f0; }
        .kpi { background: linear-gradient(180deg, #ffffff 0%, #fbfbfb 100%); }
        summary::-webkit-details-marker { display: none; }
    </style>
</head>
<body class="min-h-screen">
    <div class="flex">
        <!-- Enterprise Sidebar -->
        <aside class="w-64 sidebar min-h-screen sticky top-0 text-white p-6 hidden md:block z-50">
            <div class="flex items-center gap-3 mb-10">
                <div class="bg-indigo-600 w-10 h-10 rounded-2xl flex items-center justify-center font-black text-xl italic shadow-lg shadow-indigo-900/40">P</div>
                <div>
                    <h1 class="text-xl font-bold tracking-tight">Paham<span class="text-indigo-300">Aja</span></h1>
                    <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest -mt-1">Enterprise Suite</p>
                </div>
            </div>

            <nav class="space-y-1">
                <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3 px-3">Management</p>
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 bg-white/5 border border-white/10 text-white p-3 rounded-2xl transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    <span class="text-sm font-semibold">Active Assessments</span>
                </a>
                <a href="{{ route('admin.employees.index') }}" class="flex items-center gap-3 text-slate-300 hover:bg-white/5 hover:text-white p-3 rounded-2xl transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    <span class="text-sm font-medium">Employee Insights</span>
                </a>
            </nav>

            <div class="absolute bottom-6 left-6 right-6">
                <div class="bg-white/5 p-4 rounded-2xl border border-white/10">
                    <p class="text-[9px] text-indigo-400 font-black uppercase tracking-widest mb-1">Corporate System</p>
                    <p class="text-xs font-bold text-white mb-3">Admin Panel</p>
                    <form action="{{ route('admin.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full btn-ghost text-white py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-10 max-w-[1600px] mx-auto">
            <header class="mb-8">
                <div class="flex flex-col xl:flex-row xl:items-end justify-between gap-6">
                    <div>
                        <div class="flex items-center gap-2 text-slate-400 text-xs font-bold">
                            <span class="chip px-2.5 py-1 rounded-xl text-[10px] font-black uppercase tracking-widest">Admin</span>
                            <span>/</span>
                            <span class="text-slate-600">Dashboard</span>
                        </div>
                        <h2 class="text-3xl md:text-5xl font-serif font-bold text-slate-900 tracking-tight mt-3">Dashboard</h2>
                        <p class="text-slate-500 mt-3 font-medium text-sm max-w-2xl">Kelola kuis dan laporan.</p>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <a href="{{ route('admin.quizzes.ai-create') }}" class="bg-white border border-indigo-200 text-indigo-700 px-6 py-3 rounded-2xl font-semibold text-sm hover:bg-indigo-50 transition-all flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            <span>AI Generator</span>
                        </a>
                        <a href="{{ route('admin.quizzes.create') }}" class="btn-primary text-white px-7 py-3 rounded-2xl font-semibold text-sm shadow-lg shadow-indigo-600/20 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            <span>Buat Kuis</span>
                        </a>
                        <form action="{{ route('admin.logout') }}" method="POST" class="md:hidden">
                            @csrf
                            <button type="submit" class="bg-slate-900 text-white px-6 py-3 rounded-2xl font-semibold text-sm">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-8">
                    <div class="card-enterprise kpi rounded-2xl p-6">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Kuis</p>
                        <div class="mt-2 flex items-end justify-between gap-4">
                            <p class="text-3xl font-serif font-bold text-slate-900">{{ $stats['quizzes'] }}</p>
                            <span class="chip px-3 py-1 rounded-xl text-[10px] font-black uppercase tracking-widest text-slate-700">Deployments</span>
                        </div>
                    </div>
                    <div class="card-enterprise kpi rounded-2xl p-6">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Participants</p>
                        <div class="mt-2 flex items-end justify-between gap-4">
                            <p class="text-3xl font-serif font-bold text-slate-900">{{ $stats['participants'] }}</p>
                            <span class="chip px-3 py-1 rounded-xl text-[10px] font-black uppercase tracking-widest text-slate-700">Entries</span>
                        </div>
                    </div>
                    <div class="card-enterprise kpi rounded-2xl p-6">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Questions</p>
                        <div class="mt-2 flex items-end justify-between gap-4">
                            <p class="text-3xl font-serif font-bold text-slate-900">{{ $stats['questions'] }}</p>
                            <span class="chip px-3 py-1 rounded-xl text-[10px] font-black uppercase tracking-widest text-slate-700">Bank</span>
                        </div>
                    </div>
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

            <div class="card-enterprise rounded-2xl overflow-hidden">
                <div class="p-6 border-b border-slate-200 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-slate-900 text-white flex items-center justify-center shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5h8a2 2 0 012 2v11a1 1 0 01-1 1H8a2 2 0 01-2-2V6a1 1 0 011-1h2z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9h7M9 13h7M9 17h7"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-black text-slate-900 tracking-tight">Daftar Kuis</p>
                            <p class="text-xs font-semibold text-slate-500">Analytics, export, dan manajemen kuis</p>
                        </div>
                    </div>
                    <div class="w-full lg:w-[420px]">
                        <input id="quizSearch" type="text" placeholder="Cari judul kuis..." class="w-full bg-white border border-slate-200 px-4 py-3 rounded-2xl font-medium text-sm focus:outline-none focus:ring-4 focus:ring-indigo-600/10 focus:border-indigo-600">
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50 border-b border-slate-200">
                            <tr class="text-[10px] text-slate-400 font-black uppercase tracking-[0.2em]">
                                <th class="px-8 py-5">Kuis</th>
                                <th class="px-8 py-5">Durasi</th>
                                <th class="px-8 py-5">Passing</th>
                                <th class="px-8 py-5">Peserta</th>
                                <th class="px-8 py-5">Update</th>
                                <th class="px-8 py-5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100" id="quizRows">
                            @forelse($quizzes as $quiz)
                                <tr class="hover:bg-indigo-50/30 transition-all" data-title="{{ strtolower($quiz->title) }}">
                                    <td class="px-8 py-5">
                                        <p class="text-slate-900 font-semibold tracking-tight">{{ $quiz->title }}</p>
                                        <p class="text-slate-400 text-xs font-semibold">{{ $quiz->slug }}</p>
                                    </td>
                                    <td class="px-8 py-5">
                                        <span class="bg-slate-100 text-slate-700 px-3 py-1 rounded-lg text-xs font-black">{{ $quiz->time_limit }}m</span>
                                    </td>
                                    <td class="px-8 py-5">
                                        <span class="bg-slate-900 text-white px-3 py-1 rounded-lg text-xs font-black">{{ $quiz->passing_score }}%</span>
                                    </td>
                                    <td class="px-8 py-5">
                                        <span class="text-slate-800 font-black">{{ $quiz->participants_count }}</span>
                                    </td>
                                    <td class="px-8 py-5">
                                        <span class="text-slate-500 text-sm font-semibold">{{ $quiz->updated_at?->diffForHumans() ?? '-' }}</span>
                                    </td>
                                    <td class="px-8 py-5">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('admin.quiz.dashboard', $quiz->slug) }}" class="btn-primary text-white px-4 py-2 rounded-xl text-xs font-semibold transition-all">
                                                Analytics
                                            </a>
                                            <details class="relative">
                                                <summary class="list-none cursor-pointer bg-white border border-slate-200 text-slate-700 px-4 py-2 rounded-xl text-xs font-semibold hover:bg-gray-50 transition-all flex items-center gap-2">
                                                    <span>Actions</span>
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                                </summary>
                                                <div class="absolute right-0 mt-2 w-44 bg-white border border-slate-200 rounded-2xl shadow-xl p-2 z-10">
                                                    <a href="{{ route('admin.quizzes.show', $quiz->slug) }}" class="block px-3 py-2 rounded-xl text-sm font-semibold text-slate-700 hover:bg-slate-50">Manage</a>
                                                    <a href="{{ route('admin.quiz.export', $quiz->slug) }}" class="block px-3 py-2 rounded-xl text-sm font-semibold text-slate-700 hover:bg-slate-50">Export</a>
                                                    <form action="{{ route('admin.quizzes.destroy', $quiz->id) }}" method="POST" onsubmit="return confirm('Hapus kuis ini?')" class="px-1">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="w-full text-left px-2 py-2 rounded-xl text-sm font-semibold text-rose-700 hover:bg-rose-50">Delete</button>
                                                    </form>
                                                </div>
                                            </details>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-8 py-20 text-center text-slate-400 font-bold italic">Belum ada kuis.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <script>
                const search = document.getElementById('quizSearch');
                const rows = Array.from(document.querySelectorAll('#quizRows tr[data-title]'));

                const applySearch = () => {
                    const q = (search.value || '').toLowerCase().trim();
                    rows.forEach((row) => {
                        const title = row.getAttribute('data-title') || '';
                        row.classList.toggle('hidden', q !== '' && !title.includes(q));
                    });
                };

                if (search) {
                    search.addEventListener('input', applySearch);
                }
            </script>
        </main>
    </div>
</body>
</html>
