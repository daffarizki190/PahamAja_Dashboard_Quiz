<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
                <h1 class="text-base md:text-lg font-bold text-[#1C1C1E] truncate drop-shadow-sm">{{ $quiz->title }}</h1>
                <div class="flex items-center gap-2 mt-0.5">
                    <span class="text-[10px] font-black uppercase tracking-wider text-indigo-600 bg-indigo-50 px-1.5 py-0.5 rounded">PESERTA</span>
                    <p class="text-xs md:text-sm font-semibold text-[#8E8E93] truncate">{{ $participant->name }} ({{ $participant->nim }})</p>
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
        <div class="mb-6 flex items-center justify-between gap-3">
            <p id="progressText" class="text-sm font-black text-slate-700 tracking-tight"></p>
            <div class="flex items-center gap-3">
                <button type="button" id="reviewBtn" class="bg-white border border-slate-200 text-slate-700 font-black px-4 py-2 rounded-2xl text-xs hover:bg-slate-50 transition-all active:scale-[0.98]">
                    Ringkasan
                </button>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest hidden sm:block">Satu soal per halaman</p>
            </div>
        </div>
        
        <form id="quizForm" action="{{ route('quiz.storeAnswer', ['quiz' => $quiz->slug, 'participant' => $participant->id]) }}" method="POST">
            @csrf

            <div class="space-y-10" id="questionPager">
                @foreach($quiz->questions as $index => $question)
                <div class="group question-page hidden" id="question-card-{{ $question->id }}" data-index="{{ $index }}">
                    <!-- Question Header with Hierarchy -->
                    <div class="mb-5 flex items-start gap-3 md:gap-4">
                        <span class="shrink-0 w-7 h-7 md:w-8 md:h-8 bg-white border border-gray-200 rounded-xl flex items-center justify-center text-[11px] md:text-xs font-black text-indigo-600 shadow-sm group-hover:border-indigo-200 transition-colors">
                            {{ $index + 1 }}
                        </span>
                        <h3 class="text-lg sm:text-xl md:text-2xl font-bold text-[#1C1C1E] leading-tight pt-0.5 break-words">
                            {{ $question->text }}
                        </h3>
                    </div>
                    
                    <!-- Options List: Cleaner Layout -->
                    <div class="grid gap-3 md:pl-12">
                        @foreach($question->options as $option)
                        <label class="option-card flex items-start p-3 sm:p-4 bg-white border border-gray-100 rounded-[1.25rem] cursor-pointer transition-all duration-200 shadow-sm relative overflow-hidden group/opt">
                            <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option->id }}" @if(isset($selected) && (string) ($selected[(string) $question->id] ?? '') === (string) $option->id) checked @endif
                                class="w-5 h-5 mt-0.5 text-indigo-600 focus:ring-offset-0 focus:ring-0 border-[#D1D1D6] rounded-full transition-all">
                            <span class="ml-4 text-lg sm:text-[18px] font-medium text-[#1C1C1E] group-hover/opt:text-indigo-900 transition-colors break-words">{{ $option->text }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Footer Action Bar -->
            <div class="fixed bottom-0 left-0 right-0 glass-nav border-t border-[#D1D1D6]/30 p-4 md:p-6 z-40" style="padding-bottom: calc(1rem + env(safe-area-inset-bottom));">
                <div class="max-w-3xl mx-auto flex items-center gap-3">
                    <button type="button" id="prevBtn" class="flex-1 bg-white border border-slate-200 text-slate-700 font-black py-4 px-6 rounded-2xl hover:bg-slate-50 transition-all active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed">
                        Kembali
                    </button>
                    <button type="button" id="nextBtn" class="flex-1 bg-[#1C1C1E] hover:bg-black text-white font-black py-4 px-6 rounded-2xl shadow-xl transition-all duration-300 transform active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed">
                        Lanjutkan
                    </button>
                    <button type="button" id="submitBtn" onclick="confirmSubmit()" class="hidden flex-1 bg-[#1C1C1E] hover:bg-black text-white font-black py-4 px-6 rounded-2xl shadow-xl transition-all duration-300 transform active:scale-[0.98]">
                        Kirim Jawaban
                    </button>
                    <!-- Actual hidden submit -->
                    <button type="submit" id="actualSubmitBtn" class="hidden"></button>
                </div>
            </div>
            
        </form>
    </div>

    <div id="submitModal" class="fixed inset-0 z-50 hidden" aria-hidden="true">
        <div class="absolute inset-0 bg-black/30 backdrop-blur-sm" data-modal-backdrop></div>
        <div class="absolute inset-0 flex items-end sm:items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="submitModalTitle">
            <div class="w-full max-w-md bg-white rounded-[1.75rem] shadow-2xl border border-slate-200 overflow-hidden">
                <div class="p-6">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Konfirmasi</p>
                            <h2 id="submitModalTitle" class="text-xl font-black text-slate-900 tracking-tight">Kirim jawaban sekarang?</h2>
                            <p id="submitModalMessage" class="text-slate-600 font-semibold text-sm mt-3 leading-relaxed"></p>
                        </div>
                        <button type="button" class="shrink-0 w-10 h-10 rounded-2xl bg-slate-50 hover:bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-400 hover:text-slate-700 transition-all active:scale-95" data-modal-close aria-label="Tutup">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                </div>
                <div class="px-6 pb-6 flex flex-col-reverse sm:flex-row gap-3">
                    <button type="button" class="w-full sm:w-auto flex-1 bg-white border border-slate-200 text-slate-700 font-black py-3.5 px-6 rounded-2xl hover:bg-slate-50 transition-all active:scale-[0.98]" data-modal-cancel>
                        Batal
                    </button>
                    <button type="button" class="w-full sm:w-auto flex-1 bg-[#1C1C1E] hover:bg-black text-white font-black py-3.5 px-6 rounded-2xl shadow-lg transition-all active:scale-[0.98]" data-modal-confirm>
                        Kirim
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="reviewModal" class="fixed inset-0 z-50 hidden" aria-hidden="true">
        <div class="absolute inset-0 bg-black/30 backdrop-blur-sm" data-review-backdrop></div>
        <div class="absolute inset-0 flex items-end sm:items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="reviewModalTitle">
            <div class="w-full max-w-md bg-white rounded-[1.75rem] shadow-2xl border border-slate-200 overflow-hidden">
                <div class="p-6">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Ringkasan</p>
                            <h2 id="reviewModalTitle" class="text-xl font-black text-slate-900 tracking-tight">Status Jawaban</h2>
                            <p class="text-slate-600 font-semibold text-sm mt-3 leading-relaxed">
                                Terjawab: <span id="answeredCount" class="font-black text-slate-900"></span>/<span id="totalCount" class="font-black text-slate-900"></span>
                            </p>
                        </div>
                        <button type="button" class="shrink-0 w-10 h-10 rounded-2xl bg-slate-50 hover:bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-400 hover:text-slate-700 transition-all active:scale-95" data-review-close aria-label="Tutup">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                </div>
                <div class="px-6 pb-2">
                    <div id="reviewGrid" class="grid grid-cols-6 gap-2"></div>
                </div>
                <div class="px-6 pb-6 pt-4">
                    <button type="button" class="w-full bg-white border border-slate-200 text-slate-700 font-black py-3.5 px-6 rounded-2xl hover:bg-slate-50 transition-all active:scale-[0.98]" data-review-cancel>
                        Tutup
                    </button>
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

        const progressText = document.getElementById('progressText');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const submitBtn = document.getElementById('submitBtn');
        const reviewBtn = document.getElementById('reviewBtn');

        const isAnswered = (index) => {
            const page = questionPages[index];
            if (!page) return false;
            return Boolean(page.querySelector('input[type="radio"]:checked'));
        };

        const getAnsweredCount = () => {
            return document.querySelectorAll('input[type="radio"]:checked').length;
        };

        const getFirstUnansweredIndex = () => {
            for (let i = 0; i < totalQuestions; i++) {
                if (!isAnswered(i)) return i;
            }
            return totalQuestions - 1;
        };

        const scrollToTop = () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        };

        const updatePagerUi = () => {
            if (progressText) {
                progressText.textContent = `Soal ${currentIndex + 1} dari ${totalQuestions}`;
            }

            if (prevBtn) {
                prevBtn.disabled = currentIndex === 0;
            }

            const answered = isAnswered(currentIndex);
            if (answered) {
                unlockedMax = Math.max(unlockedMax, currentIndex + 1);
            }

            const isLast = currentIndex === totalQuestions - 1;
            if (nextBtn && submitBtn) {
                nextBtn.classList.toggle('hidden', isLast);
                submitBtn.classList.toggle('hidden', !isLast);
            }

            if (nextBtn) {
                nextBtn.disabled = !answered;
            }

            if (submitBtn) {
                submitBtn.disabled = !answered;
                submitBtn.classList.toggle('opacity-50', !answered);
                submitBtn.classList.toggle('cursor-not-allowed', !answered);
            }

            renderReviewGrid();
        };

        const showQuestion = (index) => {
            const clamped = Math.max(0, Math.min(totalQuestions - 1, index));
            const target = clamped > unlockedMax ? unlockedMax : clamped;
            questionPages.forEach((page, i) => {
                page.classList.toggle('hidden', i !== target);
            });
            currentIndex = target;
            updatePagerUi();
            scrollToTop();
        };

        const reviewModal = document.getElementById('reviewModal');
        const reviewGrid = document.getElementById('reviewGrid');
        const answeredCountEl = document.getElementById('answeredCount');
        const totalCountEl = document.getElementById('totalCount');
        const reviewClose = reviewModal?.querySelector('[data-review-close]');
        const reviewCancel = reviewModal?.querySelector('[data-review-cancel]');
        const reviewBackdrop = reviewModal?.querySelector('[data-review-backdrop]');

        const openReviewModal = () => {
            if (!reviewModal) return;
            reviewModal.classList.remove('hidden');
            reviewModal.setAttribute('aria-hidden', 'false');
            document.documentElement.classList.add('overflow-hidden');
            renderReviewGrid();
            reviewCancel?.focus();
        };

        const closeReviewModal = () => {
            if (!reviewModal) return;
            reviewModal.classList.add('hidden');
            reviewModal.setAttribute('aria-hidden', 'true');
            document.documentElement.classList.remove('overflow-hidden');
        };

        const renderReviewGrid = () => {
            if (!reviewGrid || !answeredCountEl || !totalCountEl) return;

            const answeredCount = getAnsweredCount();
            answeredCountEl.textContent = String(answeredCount);
            totalCountEl.textContent = String(totalQuestions);

            const items = [];
            for (let i = 0; i < totalQuestions; i++) {
                const answered = isAnswered(i);
                const isCurrent = i === currentIndex;
                const isLocked = i > unlockedMax;

                const base = 'w-full aspect-square rounded-2xl font-black text-sm transition-all active:scale-[0.98]';
                const state = isLocked
                    ? 'bg-slate-100 text-slate-300 border border-slate-200 cursor-not-allowed'
                    : answered
                        ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100'
                        : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-50';

                const currentRing = isCurrent ? ' ring-2 ring-indigo-500/30' : '';

                items.push(
                    `<button type="button" data-jump="${i}" ${isLocked ? 'disabled' : ''} class="${base} ${state}${currentRing}">${i + 1}</button>`
                );
            }

            reviewGrid.innerHTML = items.join('');

            reviewGrid.querySelectorAll('button[data-jump]').forEach((btn) => {
                btn.addEventListener('click', () => {
                    const idx = Number(btn.getAttribute('data-jump'));
                    closeReviewModal();
                    showQuestion(idx);
                });
            });
        };

        // Visual selection + autosave
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

                updatePagerUi();

                // Autosave to backend
                const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                const qid = groupName.match(/answers\[(.+?)\]/)?.[1];
                const oid = this.value;
                if (!qid || !oid) return;

                fetch(@json(route('quiz.autosave', ['quiz' => $quiz->slug, 'participant' => $participant->id])), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf || ''
                    },
                    body: JSON.stringify({ question_id: qid, option_id: oid })
                }).catch(() => {});
            });
        });

        prevBtn?.addEventListener('click', () => {
            showQuestion(currentIndex - 1);
        });

        nextBtn?.addEventListener('click', () => {
            if (!isAnswered(currentIndex)) return;
            showQuestion(currentIndex + 1);
        });

        reviewBtn?.addEventListener('click', openReviewModal);
        reviewClose?.addEventListener('click', closeReviewModal);
        reviewCancel?.addEventListener('click', closeReviewModal);
        reviewBackdrop?.addEventListener('click', closeReviewModal);

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && reviewModal && !reviewModal.classList.contains('hidden')) {
                closeReviewModal();
            }
        });

        unlockedMax = getFirstUnansweredIndex();
        showQuestion(getFirstUnansweredIndex());

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

            if (answeredCount < totalQuestions) {
                openReviewModal();
                return;
            }

            openSubmitModal('Semua soal sudah terjawab. Kirim jawaban sekarang?');
        }

        const submitModal = document.getElementById('submitModal');
        const submitModalMessage = document.getElementById('submitModalMessage');
        const modalConfirm = submitModal?.querySelector('[data-modal-confirm]');
        const modalCancel = submitModal?.querySelector('[data-modal-cancel]');
        const modalClose = submitModal?.querySelector('[data-modal-close]');
        const modalBackdrop = submitModal?.querySelector('[data-modal-backdrop]');

        const openSubmitModal = (message) => {
            if (!submitModal || !submitModalMessage) return;
            submitModalMessage.textContent = message;
            submitModal.classList.remove('hidden');
            submitModal.setAttribute('aria-hidden', 'false');
            document.documentElement.classList.add('overflow-hidden');
            modalConfirm?.focus();
        };

        const closeSubmitModal = () => {
            if (!submitModal) return;
            submitModal.classList.add('hidden');
            submitModal.setAttribute('aria-hidden', 'true');
            document.documentElement.classList.remove('overflow-hidden');
        };

        modalConfirm?.addEventListener('click', () => {
            closeSubmitModal();
            document.getElementById('actualSubmitBtn')?.click();
        });

        modalCancel?.addEventListener('click', closeSubmitModal);
        modalClose?.addEventListener('click', closeSubmitModal);
        modalBackdrop?.addEventListener('click', closeSubmitModal);

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && submitModal && !submitModal.classList.contains('hidden')) {
                closeSubmitModal();
            }
        });

        // Prevent back button
        (function() {
            history.pushState(null, null, location.href);
            window.onpopstate = function() {
                history.pushState(null, null, location.href);
            };
        })();
    </script>
</body>
</html>
