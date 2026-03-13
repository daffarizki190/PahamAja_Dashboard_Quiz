<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Quiz - PahamAja</title>
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
            <header class="mb-10 flex justify-between items-center">
                <div>
                    <h2 class="text-3xl font-bold text-slate-800">Edit Kuis</h2>
                    <p class="text-slate-500">Sesuaikan pertanyaan dan pengaturan kuis.</p>
                </div>
                <a href="{{ route('admin.quizzes.show', $quiz->slug) }}" class="text-slate-500 hover:text-slate-800 font-semibold flex items-center">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Batal Edit
                </a>
            </header>

            <form action="{{ route('admin.quizzes.update', $quiz->slug) }}" method="POST" class="max-w-4xl">
                @csrf
                @method('PATCH')
                
                <!-- Quiz Details -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8 mb-8">
                    <h3 class="text-xl font-bold text-slate-800 mb-6 flex items-center gap-2">
                        <span class="bg-indigo-100 text-indigo-600 p-1.5 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </span>
                        Pengaturan Umum
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Judul Kuis</label>
                            <input type="text" name="title" required value="{{ $quiz->title }}" 
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Waktu (Menit)</label>
                                <input type="number" name="time_limit" required value="{{ $quiz->time_limit }}" 
                                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Passing (0-100)</label>
                                <input type="number" name="passing_score" required value="{{ $quiz->passing_score }}" 
                                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Questions Area -->
                <div id="questionsContainer" class="space-y-6 mb-8">
                    @foreach($quiz->questions as $qIndex => $question)
                    <div class="question-card bg-white rounded-2xl shadow-sm border border-slate-200 p-8 relative overflow-hidden group">
                        <div class="absolute left-0 top-0 bottom-0 w-2 bg-indigo-600"></div>
                        
                        <div class="flex justify-between items-start mb-6">
                            <h4 class="text-lg font-bold text-slate-800">Pertanyaan #{{ $qIndex + 1 }}</h4>
                            @if($qIndex > 0)
                            <button type="button" class="text-rose-400 hover:text-rose-600" onclick="this.closest('.question-card').remove()">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                            @endif
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Teks Pertanyaan</label>
                            <textarea name="questions[{{ $qIndex }}][text]" required rows="2" 
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">{{ $question->text }}</textarea>
                        </div>

                        <div class="space-y-4">
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Pilihan Jawaban (Tandai yang benar)</label>
                            @foreach($question->options as $oIndex => $option)
                            <div class="flex items-center gap-4">
                                <input type="radio" name="questions[{{ $qIndex }}][correct_option]" value="{{ $oIndex }}" {{ $option->is_correct ? 'checked' : '' }}
                                    class="w-5 h-5 text-indigo-600 focus:ring-indigo-500 border-slate-300">
                                <input type="text" name="questions[{{ $qIndex }}][options][{{ $oIndex }}][text]" required value="{{ $option->text }}"
                                    class="flex-1 bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all text-sm">
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="flex flex-col gap-4">
                    <button type="button" id="addQuestionBtn" class="w-full border-2 border-dashed border-slate-300 hover:border-indigo-400 hover:text-indigo-600 text-slate-500 py-4 rounded-2xl font-bold transition-all flex items-center justify-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        <span>Tambah Pertanyaan Baru</span>
                    </button>

                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-4 rounded-2xl font-bold shadow-xl shadow-indigo-100 transition-all flex items-center justify-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span>Update Kuis</span>
                    </button>
                </div>
            </form>
        </main>
    </div>

    <script>
        let questionCount = {{ $quiz->questions->count() }};
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
