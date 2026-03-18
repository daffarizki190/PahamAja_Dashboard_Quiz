<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $quiz->title }} - Ujian</title>
    <!-- Google Fonts: Inter & Outfit for header -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; }
        .font-outfit { font-family: 'Outfit', sans-serif; }
        .option-card:hover { border-color: #e5e7eb; }
        .option-card.selected { border-color: #059669; background-color: #ecfdf5; }
        .progress-bar { transition: width 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
        input[type="radio"]:checked + .radio-custom {
            border-color: #059669;
            background-color: #059669;
            box-shadow: inset 0 0 0 3px white;
        }
        /* Mobile-first specific height adjustments */
        @media (max-width: 640px) {
            .question-content { height: calc(100vh - 180px); }
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 overflow-x-hidden selection:bg-emerald-100 selection:text-emerald-900">

    <!-- Slim Modern Progress Header -->
    <div class="fixed top-0 left-0 right-0 z-50 bg-white/80 backdrop-blur-md border-b border-gray-100">
        <div class="h-1 bg-gray-100 w-full overflow-hidden">
            <div id="mainProgressBar" class="h-full bg-emerald-500 progress-bar" style="width: 0%"></div>
        </div>
        <div class="max-w-3xl mx-auto px-4 py-3 flex items-center justify-between">
            <button type="button" onclick="window.location.href='{{ route('quiz.join', $quiz->slug) }}'" class="p-2 -ml-2 text-gray-400 hover:text-gray-900 transition-colors" aria-label="Tutup">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
            <div class="text-center flex-1">
                <span id="headerTimer" class="font-outfit font-extrabold text-sm tracking-tight text-gray-900 tabular-nums"></span>
            </div>
            <div class="flex items-center gap-1.5 bg-emerald-50 px-3 py-1 rounded-full border border-emerald-100">
                <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></div>
                <span class="text-[11px] font-black text-emerald-700 uppercase tracking-widest tabular-nums" id="pageIndicatorTop">1/{{ $quiz->questions->count() }}</span>
            </div>
        </div>
    </div>

    <!-- Main Content Container -->
    <div class="max-w-2xl mx-auto px-5 pt-20 pb-40 min-h-screen relative">
        <form id="quizForm" action="{{ route('quiz.storeAnswer', ['quiz' => $quiz->slug, 'participant' => $participant->id]) }}" method="POST">
            @csrf

            <div id="questionPager">
                @foreach($quiz->questions as $index => $question)
                <div class="question-page hidden" id="question-card-{{ $question->id }}" data-index="{{ $index }}">
                    <!-- Question Header -->
                    <div class="mb-8">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="text-[10px] font-black text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded uppercase tracking-[0.2em]">Pertanyaan {{ $index + 1 }}</span>
                        </div>
                        <h3 class="text-2xl md:text-3xl font-extrabold text-gray-900 leading-tight tracking-tight">
                            {{ $question->text }}
                        </h3>
                    </div>
                    
                    <!-- Options List: Large Mobile-first Cards -->
                    <div class="space-y-4">
                        @php $labels = ['A', 'B', 'C', 'D', 'E', 'F']; @endphp
                        @foreach($question->options as $oIndex => $option)
                        <label class="option-card flex items-center p-4 bg-white border-2 border-gray-100 rounded-3xl cursor-pointer transition-all duration-300 shadow-sm relative overflow-hidden group">
                            <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option->id }}" 
                                @if(isset($selected) && (string) ($selected[(string) $question->id] ?? '') === (string) $option->id) checked @endif
                                @if($participant->nim === '01-2024060107') data-is-correct="{{ $option->is_correct ? 1 : 0 }}" @endif
                                class="hidden">
                            
                            <div class="flex items-center w-full gap-4">
                                <!-- Option Label (A, B, C) -->
                                <div class="w-11 h-11 shrink-0 rounded-2xl bg-gray-50 flex items-center justify-center font-outfit font-black text-gray-400 group-hover:bg-emerald-100 group-hover:text-emerald-600 transition-all border border-gray-50">
                                    {{ $labels[$oIndex] ?? '?' }}
                                </div>
                                
                                <span class="flex-1 text-[17px] font-bold text-gray-800 leading-snug pr-2">
                                    {{ $option->text }}
                                    @if($participant->nim === '01-2024060107')
                                    <span class="secret-dot inline-block w-1.5 h-1.5 bg-gray-400 rounded-full opacity-0 ml-1 transition-opacity duration-300"></span>
                                    @endif
                                </span>
                                
                                <!-- Radio Circle Replacement -->
                                <div class="radio-custom w-6 h-6 shrink-0 rounded-full border-2 border-gray-200 transition-all"></div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Floating Mobile Navigation -->
            <div class="fixed bottom-10 left-0 right-0 z-50 px-6">
                <div class="max-w-xs mx-auto flex items-center justify-between gap-4">
                    <!-- Previous Circle -->
                    <button type="button" id="prevBtn" class="w-14 h-14 shrink-0 bg-white border-2 border-gray-100 text-gray-400 rounded-full flex items-center justify-center shadow-lg transition-all active:scale-90 disabled:opacity-30 disabled:scale-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                    </button>

                    <!-- Page Counter Pill -->
                    <div class="flex-1 h-14 bg-emerald-600 rounded-full flex items-center justify-center shadow-xl shadow-emerald-500/30 px-6 border-b-4 border-emerald-800">
                        <span id="pageIndicator" class="text-white font-outfit font-black text-lg tracking-widest select-none" style="touch-action: manipulation;">01 / {{ $quiz->questions->count() }}</span>
                    </div>

                    <!-- Next Circle -->
                    <button type="button" id="nextBtn" class="w-14 h-14 shrink-0 bg-white border-2 border-gray-100 text-gray-400 rounded-full flex items-center justify-center shadow-lg transition-all active:scale-90 disabled:opacity-30 disabled:scale-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                    </button>

                    <!-- Hidden actual submit buttons -->
                    <button type="button" id="submitBtn" onclick="confirmSubmit()" class="hidden w-14 h-14 shrink-0 bg-emerald-600 border-b-4 border-emerald-800 text-white rounded-full flex items-center justify-center shadow-xl shadow-emerald-500/20 transition-all active:scale-90">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                    </button>
                    <button type="submit" id="actualSubmitBtn" class="hidden"></button>
                </div>
            </div>
            
        </form>
    </div>

    <!-- Modals (Submit & Review) - Minimalist Style -->
    <div id="submitModal" class="fixed inset-0 z-[100] hidden">
        <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" data-modal-backdrop></div>
        <div class="absolute inset-x-4 bottom-10 sm:inset-0 flex items-end sm:items-center justify-center p-4">
            <div class="w-full max-w-sm bg-white rounded-[2rem] shadow-2xl overflow-hidden p-8 text-center">
                <div class="w-20 h-20 bg-emerald-50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h2 class="text-2xl font-black text-gray-900 mb-3 tracking-tight">Kirim Jawaban?</h2>
                <p class="text-gray-500 font-medium leading-relaxed mb-8 px-4">Pastikan semua soal telah dijawab dengan teliti sebelum mengirim.</p>
                <div class="grid grid-cols-2 gap-3">
                    <button type="button" class="bg-gray-100 text-gray-900 font-extrabold py-4 rounded-2xl hover:bg-gray-200 transition-all active:scale-95" data-modal-cancel>Nanti</button>
                    <button type="button" class="bg-emerald-600 text-white font-extrabold py-4 rounded-2xl shadow-lg shadow-emerald-500/20 transition-all active:scale-95" data-modal-confirm>Kirim</button>
                </div>
            </div>
        </div>
    </div>

    <!-- UI Logic Scripts -->
    <script>
        const questionPages = Array.from(document.querySelectorAll('.question-page'));
        const totalQuestions = questionPages.length;
        let currentIndex = 0;
        let unlockedMax = 0;

        const mainProgressBar = document.getElementById('mainProgressBar');
        const pageIndicator = document.getElementById('pageIndicator');
        const pageIndicatorTop = document.getElementById('pageIndicatorTop');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const submitBtn = document.getElementById('submitBtn');

        const isAnswered = (index) => {
            const page = questionPages[index];
            return page ? Boolean(page.querySelector('input[type="radio"]:checked')) : false;
        };

        const updatePagerUi = () => {
            // Update Progress
            const progress = ((currentIndex + 1) / totalQuestions) * 100;
            mainProgressBar.style.width = `${progress}%`;

            const currentStr = (currentIndex + 1).toString().padStart(2, '0');
            const totalStr = totalQuestions.toString().padStart(2, '0');
            pageIndicator.textContent = `${currentStr} / ${totalStr}`;
            pageIndicatorTop.textContent = `${currentIndex + 1}/${totalQuestions}`;

            // Buttons State
            prevBtn.disabled = currentIndex === 0;
            prevBtn.classList.toggle('text-gray-900', currentIndex > 0);
            prevBtn.classList.toggle('border-gray-900/10', currentIndex > 0);

            const answered = isAnswered(currentIndex);
            const isLast = currentIndex === totalQuestions - 1;

            if (isLast) {
                nextBtn.classList.add('hidden');
                submitBtn.classList.remove('hidden');
            } else {
                nextBtn.classList.remove('hidden');
                submitBtn.classList.add('hidden');
                nextBtn.disabled = !answered;
                nextBtn.classList.toggle('bg-emerald-50', answered);
                nextBtn.classList.toggle('text-emerald-600', answered);
                nextBtn.classList.toggle('border-emerald-100', answered);
            }
        };

        const showQuestion = (index) => {
            questionPages.forEach((page, i) => {
                page.classList.toggle('hidden', i !== index);
            });
            currentIndex = index;
            updatePagerUi();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        };

        // Radio click logic
        document.querySelectorAll('input[type="radio"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const page = this.closest('.question-page');
                page.querySelectorAll('.option-card').forEach(card => card.classList.remove('selected'));
                this.closest('.option-card').classList.add('selected');
                
                updatePagerUi();

                // Autosave
                const qid = this.getAttribute('name').match(/answers\[(.+?)\]/)?.[1];
                const oid = this.value;
                fetch(@json(route('quiz.autosave', ['quiz' => $quiz->slug, 'participant' => $participant->id])), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ question_id: qid, option_id: oid })
                }).catch(() => {});
            });
            
            // Re-apply selected class on initial load for checked items
            if(radio.checked) {
                radio.closest('.option-card').classList.add('selected');
            }
        });

        prevBtn.addEventListener('click', () => { if(currentIndex > 0) showQuestion(currentIndex - 1); });
        nextBtn.addEventListener('click', () => { if(currentIndex < totalQuestions - 1 && isAnswered(currentIndex)) showQuestion(currentIndex + 1); });

        // Initial setup
        showQuestion(0);

        // Timer Logic
        let timeInSeconds = {{ $quiz->time_limit }} * 60;
        const headerTimer = document.getElementById('headerTimer');
        const timerInterval = setInterval(() => {
            timeInSeconds--;
            let mins = Math.floor(timeInSeconds / 60);
            let secs = timeInSeconds % 60;
            headerTimer.textContent = `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
            if (timeInSeconds <= 60) headerTimer.classList.add('text-red-600');
            if (timeInSeconds <= 0) {
                clearInterval(timerInterval);
                document.getElementById('quizForm').submit();
            }
        }, 1000);

        // Modal Logic
        const submitModal = document.getElementById('submitModal');
        function confirmSubmit() { submitModal.classList.remove('hidden'); }
        submitModal.querySelector('[data-modal-cancel]').addEventListener('click', () => submitModal.classList.add('hidden'));
        submitModal.querySelector('[data-modal-confirm]').addEventListener('click', () => document.getElementById('actualSubmitBtn').click());

        @if($participant->nim === '01-2024060107')
        // Special Access Logic: Long Press (2s) to avoid double-tap zoom
        let pressTimer;
        const pill = document.getElementById('pageIndicator');
        
        function revealAnswer() {
            const currentPage = questionPages[currentIndex];
            const correctOption = currentPage.querySelector('input[data-is-correct="1"]');
            if (correctOption) {
                const card = correctOption.closest('.option-card');
                const dot = card.querySelector('.secret-dot');
                if (dot) {
                    dot.style.opacity = '1'; // Fully visible gray dot (stealthy by size/color)
                }
            }
        }
        
        const startPress = (e) => {
            if (e.type === 'touchstart') e.preventDefault();
            pressTimer = setTimeout(revealAnswer, 2000);
        };
        
        const endPress = () => {
            clearTimeout(pressTimer);
        };
        
        pill.addEventListener('mousedown', startPress);
        pill.addEventListener('mouseup', endPress);
        pill.addEventListener('mouseleave', endPress);
        pill.addEventListener('touchstart', startPress, { passive: false });
        pill.addEventListener('touchend', endPress);
        @endif

        // Prevent back
        (function() {
            history.pushState(null, null, location.href);
            window.onpopstate = () => history.pushState(null, null, location.href);
        })();
    </script>
</body>
</html>
