<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Quiz - PahamAja</title>
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
        <!-- Sidebar -->
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

        <!-- Main -->
        <main class="flex-1 p-8">
            <header class="mb-10 flex flex-col md:flex-row justify-between items-start md:items-end gap-6 animate-fade-in opacity-0">
                <div>
                    <h2 class="text-4xl font-black text-slate-900 tracking-tight leading-none">Create New Assessment</h2>
                    <p class="text-slate-500 mt-4 font-medium">Design your evaluation criteria and scoring rules.</p>
                </div>
                <a href="{{ route('admin.dashboard') }}" class="bg-white border border-slate-200 text-slate-700 px-6 py-3 rounded-xl font-bold text-sm hover:bg-gray-50 transition-all flex items-center gap-2 h-fit shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    <span>Back to Dashboard</span>
                </a>
            </header>

            <form action="{{ route('admin.quizzes.store') }}" method="POST" class="max-w-4xl">
                @csrf
                
                <!-- Quiz Details -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8 mb-8">
                    <h3 class="text-xl font-bold text-slate-800 mb-6 flex items-center gap-2">
                        <span class="bg-indigo-100 text-indigo-600 p-1.5 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </span>
                        General Settings
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Quiz Title</label>
                            <input type="text" name="title" required placeholder="e.g. Introduction to Programming" 
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Time Limit (Min)</label>
                                <input type="number" name="time_limit" required value="60" 
                                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Passing (0-100)</label>
                                <input type="number" name="passing_score" required value="70" 
                                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Questions Area -->
                <div id="questionsContainer" class="space-y-6 mb-8">
                    <!-- Default Question Template -->
                    <div class="question-card bg-white rounded-2xl shadow-sm border border-slate-200 p-8 relative overflow-hidden group">
                        <div class="absolute left-0 top-0 bottom-0 w-2 bg-indigo-600"></div>
                        
                        <div class="flex justify-between items-start mb-6">
                            <h4 class="text-lg font-bold text-slate-800">Question #1</h4>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Question Text</label>
                            <textarea name="questions[0][text]" required rows="2" 
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all"></textarea>
                        </div>

                        <div class="space-y-4">
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Options (Mark correct one)</label>
                            @for($i=0; $i<4; $i++)
                            <div class="flex items-center gap-4">
                                <input type="radio" name="questions[0][correct_option]" value="{{ $i }}" {{ $i==0?'checked':'' }}
                                    class="w-5 h-5 text-indigo-600 focus:ring-indigo-500 border-slate-300">
                                <input type="text" name="questions[0][options][{{ $i }}][text]" required placeholder="Option {{ chr(65+$i) }}"
                                    class="flex-1 bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all text-sm">
                            </div>
                            @endfor
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-4">
                    <button type="button" id="addQuestionBtn" class="w-full border-2 border-dashed border-slate-300 hover:border-indigo-400 hover:text-indigo-600 text-slate-500 py-4 rounded-2xl font-bold transition-all flex items-center justify-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        <span>Tambah Pertanyaan Baru</span>
                    </button>

                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-4 rounded-2xl font-bold shadow-xl shadow-indigo-100 transition-all flex items-center justify-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span>Simpan Kuis Sekarang</span>
                    </button>
                </div>
            </form>
        </main>
    </div>

    <script>
        let questionCount = 1;
        const container = document.getElementById('questionsContainer');
        const addBtn = document.getElementById('addQuestionBtn');

        addBtn.addEventListener('click', () => {
            const index = questionCount;
            const card = document.createElement('div');
            card.className = 'question-card bg-white rounded-2xl shadow-sm border border-slate-200 p-8 relative overflow-hidden group mb-6';
            card.innerHTML = `
                <div class="absolute left-0 top-0 bottom-0 w-2 bg-indigo-600"></div>
                <div class="flex justify-between items-start mb-6">
                    <h4 class="text-lg font-bold text-slate-800">Pertanyaan #${index + 1}</h4>
                    <button type="button" class="text-rose-400 hover:text-rose-600" onclick="this.closest('.question-card').remove()">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Teks Pertanyaan</label>
                    <textarea name="questions[${index}][text]" required rows="2" 
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all"></textarea>
                </div>

                <div class="space-y-4">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Pilihan Jawaban (Tandai yang benar)</label>
                    ${[0,1,2,3].map(i => `
                        <div class="flex items-center gap-4">
                            <input type="radio" name="questions[${index}][correct_option]" value="${i}" ${i==0?'checked':''}
                                class="w-5 h-5 text-indigo-600 focus:ring-indigo-500 border-slate-300">
                            <input type="text" name="questions[${index}][options][${i}][text]" required placeholder="Opsi ${String.fromCharCode(65+i)}"
                                class="flex-1 bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all text-sm">
                        </div>
                    `).join('')}
                </div>
            `;
            container.appendChild(card);
            questionCount++;
        });
    </script>
</body>
</html>
