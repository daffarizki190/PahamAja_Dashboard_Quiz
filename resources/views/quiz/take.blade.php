<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $quiz->title }} - Ujian</title>
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; }
        .glass-nav {
            background: rgba(251, 251, 253, 0.8);
            backdrop-filter: saturate(180%) blur(20px);
            -webkit-backdrop-filter: saturate(180%) blur(20px);
        }
        .option-card:hover { border-color: #6366f1; background-color: #f5f3ff; }
        .option-card.selected { border-color: #4f46e5; background-color: #eef2ff; box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.1); }
    </style>
</head>
<body class="bg-[#F2F2F7] text-[#1C1C1E] pb-40">

    <!-- Premium Sticky Header -->
    <div class="sticky top-0 z-50 glass-nav border-b border-[#D1D1D6]/30 px-4 md:px-6 py-4">
        <div class="max-w-4xl mx-auto flex justify-between items-center gap-3">
            <div class="flex-1 min-w-0 pr-2">
                <h1 class="text-sm md:text-base font-bold text-[#1C1C1E] truncate drop-shadow-sm">{{ $quiz->title }}</h1>
                <div class="flex items-center gap-2 mt-0.5">
                    <span class="text-[10px] font-black uppercase tracking-wider text-indigo-600 bg-indigo-50 px-1.5 py-0.5 rounded">PESERTA</span>
                    <p class="text-[11px] font-semibold text-[#8E8E93] truncate">{{ $participant->name }} ({{ $participant->nim }})</p>
                </div>
            </div>
            
            <!-- Apple-style Timer -->
            <div class="shrink-0">
                <div id="timerContainer" class="bg-white/50 border border-gray-200/50 rounded-2xl px-4 py-2 flex items-center gap-2.5 shadow-sm transition-all duration-500">
                    <div class="w-2 h-2 rounded-full bg-indigo-600 animate-pulse" id="timerDot"></div>
                    <span id="timerDisplay" class="font-mono font-extrabold text-[#1C1C1E] tracking-tighter text-base sm:text-lg leading-none">{{ $quiz->time_limit }}:00</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-3xl mx-auto px-4 md:px-6 pt-6 md:pt-10">
        
        <form id="quizForm" action="{{ route('quiz.storeAnswer', ['quiz' => $quiz->slug, 'participant' => $participant->id]) }}" method="POST">
            @csrf

            <div class="space-y-10">
                @foreach($quiz->questions as $index => $question)
                <div class="group" id="question-card-{{ $question->id }}">
                    <!-- Question Header with Hierarchy -->
                    <div class="mb-5 flex items-start gap-3 md:gap-4">
                        <span class="shrink-0 w-7 h-7 md:w-8 md:h-8 bg-white border border-gray-200 rounded-xl flex items-center justify-center text-[11px] md:text-xs font-black text-indigo-600 shadow-sm group-hover:border-indigo-200 transition-colors">
                            {{ $index + 1 }}
                        </span>
                        <h3 class="text-base sm:text-lg md:text-xl font-bold text-[#1C1C1E] leading-tight pt-0.5 break-words">
                            {{ $question->text }}
                        </h3>
                    </div>
                    
                    <!-- Options List: Cleaner Layout -->
                    <div class="grid gap-3 md:pl-12">
                        @foreach($question->options as $option)
                        <label class="option-card flex items-start p-3 sm:p-4 bg-white border border-gray-100 rounded-[1.25rem] cursor-pointer transition-all duration-200 shadow-sm relative overflow-hidden group/opt">
                            <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option->id }}" required
                                class="w-5 h-5 mt-0.5 text-indigo-600 focus:ring-offset-0 focus:ring-0 border-[#D1D1D6] rounded-full transition-all">
                            <span class="ml-4 text-sm sm:text-[15px] font-medium text-[#1C1C1E] group-hover/opt:text-indigo-900 transition-colors break-words">{{ $option->text }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Footer Action Bar -->
            <div class="fixed bottom-0 left-0 right-0 glass-nav border-t border-[#D1D1D6]/30 p-4 md:p-6 z-40" style="padding-bottom: calc(1rem + env(safe-area-inset-bottom));">
                <div class="max-w-3xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-4">
                    <p class="text-xs font-semibold text-[#8E8E93] uppercase tracking-widest hidden sm:block">Periksa kembali sebelum mengirim</p>
                    <button type="button" onclick="confirmSubmit()" 
                        class="w-full sm:w-auto bg-[#1C1C1E] hover:bg-black text-white font-bold py-4 px-10 rounded-2xl shadow-xl transition-all duration-300 transform active:scale-[0.98]">
                        Kirim Jawaban
                    </button>
                    <!-- Actual hidden submit -->
                    <button type="submit" id="actualSubmitBtn" class="hidden"></button>
                </div>
            </div>
            
        </form>
    </div>

    <!-- UI Logic Scripts -->
    <script>
        // Visual selection logic
        document.querySelectorAll('input[type="radio"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const groupName = this.getAttribute('name');
                const radiosInGroup = document.querySelectorAll(`input[name="${groupName}"]`);
                
                radiosInGroup.forEach(r => {
                    const card = r.closest('.option-card');
                    card.classList.remove('selected', 'border-indigo-500', 'bg-indigo-50/50', 'ring-1', 'ring-indigo-500/20');
                    card.classList.add('border-gray-100', 'bg-white');
                });
                
                if(this.checked) {
                    const card = this.closest('.option-card');
                    card.classList.remove('border-gray-100', 'bg-white');
                    card.classList.add('selected', 'border-indigo-500', 'bg-indigo-50/50', 'ring-1', 'ring-indigo-500/20');
                }
            });
        });

        // HIG-style Timer Logic
        let timeInMinutes = {{ $quiz->time_limit }};
        let timeInSeconds = timeInMinutes * 60;
        const timerDisplay = document.getElementById('timerDisplay');
        const timerContainer = document.getElementById('timerContainer');
        const timerDot = document.getElementById('timerDot');

        const timerInterval = setInterval(() => {
            timeInSeconds--;
            
            let minutes = Math.floor(timeInSeconds / 60);
            let seconds = timeInSeconds % 60;
            
            timerDisplay.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            
            // Critical warning state (HIG: Clear feedback)
            if (timeInSeconds <= 120 && timeInSeconds > 0) {
                timerContainer.classList.remove('bg-white/50', 'border-gray-200/50');
                timerContainer.classList.add('bg-red-50/80', 'border-red-200');
                timerDisplay.classList.add('text-red-600', 'animate-pulse');
                timerDot.classList.remove('bg-indigo-600');
                timerDot.classList.add('bg-red-600');
            }

            if (timeInSeconds <= 0) {
                clearInterval(timerInterval);
                document.getElementById('quizForm').submit();
            }
        }, 1000);

        function confirmSubmit() {
            const totalQuestions = {{ $quiz->questions->count() }};
            const answeredCount = document.querySelectorAll('input[type="radio"]:checked').length;
            
            let message = answeredCount < totalQuestions 
                ? `Anda baru menjawab ${answeredCount} dari ${totalQuestions} soal. Tetap kirim?`
                : 'Apakah Anda yakin ingin mengirim jawaban sekarang?';

            if(confirm(message)) {
                document.getElementById('actualSubmitBtn').click();
            }
        }
    </script>
</body>
</html>
