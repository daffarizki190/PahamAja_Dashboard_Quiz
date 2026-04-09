<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Kuis - {{ $quiz->title }}</title>
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Outfit:wght@600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background: #f8fafc; color: #0f172a; }
        .font-outfit { font-family: 'Outfit', sans-serif; }
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
        .glass-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
    </style>
</head>
<body class="bg-[#F2F2F7] flex items-center justify-center min-h-screen p-6">

    <div class="glass-card p-8 md:p-12 rounded-[2.5rem] shadow-2xl w-full max-w-lg text-center relative overflow-hidden animate-slide-up opacity-0">
        
        <!-- Apple-style Gradient Background Accessory -->
        <div class="absolute top-0 left-0 right-0 h-40 bg-gradient-to-br {{ $participant->score >= 70 ? 'from-green-400 to-emerald-600' : ($participant->score >= 50 ? 'from-orange-300 to-amber-500' : 'from-rose-400 to-red-600') }} opacity-90 z-0"></div>

        <div class="relative z-10 pt-4">
            @if(session('success'))
                <div class="inline-block bg-white/20 backdrop-blur-md text-white px-5 py-2 rounded-full text-xs font-black uppercase tracking-widest mb-8 border border-white/30">
                    {{ session('success') }}
                </div>
            @endif

            <h1 class="text-3xl font-black text-white mb-10 drop-shadow-lg tracking-tight">Kuis Selesai!</h1>
            
            <div class="bg-white rounded-[2rem] shadow-xl border border-gray-100 p-8 mx-auto -mt-4 relative mb-10 group">
                <p class="text-[#8E8E93] text-[10px] font-black uppercase tracking-[0.2em] mb-4">Skor Akhir Anda</p>
                
                <div class="relative inline-block">
                    <div class="text-[4rem] sm:text-[5rem] font-black leading-none tracking-tighter {{ $participant->score >= 70 ? 'text-emerald-500' : ($participant->score >= 50 ? 'text-amber-500' : 'text-rose-500') }} transition-transform group-hover:scale-105 duration-500 font-outfit">
                        {{ $participant->score ?? 0 }}<span class="text-xl sm:text-2xl text-[#C7C7CC] ml-1 font-extrabold font-sans">/100</span>
                    </div>
                </div>
                
                <div class="h-3 w-full bg-[#F2F2F7] rounded-full mt-10 mb-4 overflow-hidden p-0.5">
                    <div class="h-full {{ $participant->score >= 70 ? 'bg-emerald-500' : ($participant->score >= 50 ? 'bg-amber-500' : 'bg-rose-500') }} rounded-full transition-all duration-1000 ease-out shadow-sm" 
                         data-score="{{ $participant->score ?? 0 }}"></div>
                </div>

                <div class="mt-8 pt-6 border-t border-[#F2F2F7] grid grid-cols-2 gap-y-6 gap-x-4 text-left">
                    <div>
                        <p class="text-[9px] text-[#8E8E93] font-black uppercase tracking-wider mb-1">NAMA PESERTA</p>
                        <p class="font-bold text-[#1C1C1E] text-sm truncate uppercase">{{ $participant->name }}</p>
                    </div>
                    <div>
                        <p class="text-[9px] text-[#8E8E93] font-black uppercase tracking-wider mb-1">NOMOR INDUK</p>
                        <p class="font-bold text-[#1C1C1E] text-sm tracking-tight">{{ $participant->nim }}</p>
                    </div>
                    @if($participant->quizSession)
                    <div>
                        <p class="text-[9px] text-[#8E8E93] font-black uppercase tracking-wider mb-1">SESI PENGERJAAN</p>
                        <p class="font-bold text-indigo-600 text-[11px] leading-tight">{{ $participant->quizSession->name }}</p>
                    </div>
                    @endif
                    @if($participant->started_at && $participant->finished_at)
                    <div>
                        <p class="text-[9px] text-[#8E8E93] font-black uppercase tracking-wider mb-1">DURASI</p>
                        <p class="font-bold text-[#1C1C1E] text-[11px]">{{ $participant->finished_at->diff($participant->started_at)->format('%im %ss') }}</p>
                    </div>
                    @else
                    <div>
                        <p class="text-[9px] text-[#8E8E93] font-black uppercase tracking-wider mb-1">DURASI</p>
                        <p class="font-bold text-[#1C1C1E] text-[11px]">-</p>
                    </div>
                    @endif
                </div>
            </div>

            <div class="text-[#1C1C1E] mb-10 leading-relaxed text-[15px] px-2 font-medium">
                @if($participant->score !== null && $participant->score >= $quiz->passing_score)
                    <p class="text-emerald-600 font-black text-xs uppercase tracking-widest mb-2 flex items-center justify-center gap-1">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                        LULUS ASSESMEN
                    </p>
                    Luar biasa! Anda telah menyelesaikan <span class="text-indigo-600 font-bold">{{ $quiz->title }}</span> dengan hasil yang sangat memuaskan.
                @else
                    Terima kasih telah berpartisipasi dalam <span class="text-indigo-600 font-bold">{{ $quiz->title }}</span>. Hasil ujian Anda telah tersimpan di sistem.
                @endif
            </div>

            <!-- Detailed Answer Review (Only if Passed) -->
            @if(isset($reviewData) && $reviewData->count() > 0)
            <div class="bg-white rounded-[2rem] shadow-lg border border-gray-100 p-8 mx-auto mb-10 text-left">
                <p class="text-[#8E8E93] text-[10px] font-black uppercase tracking-[0.2em] mb-6">Review Jawaban</p>
                <div class="space-y-6">
                    @foreach($reviewData as $item)
                    <div class="pb-6 border-b border-[#F2F2F7] last:border-0 last:pb-0">
                        <p class="text-[13px] font-bold text-[#1C1C1E] mb-3 leading-snug">{{ $loop->iteration }}. {{ $item['question'] }}</p>
                        <div class="space-y-2">
                            <div class="flex items-center gap-2 p-3 rounded-xl border {{ $item['is_correct'] ? 'bg-emerald-50 border-emerald-100' : 'bg-rose-50 border-rose-100' }}">
                                <div class="w-2 h-2 rounded-full {{ $item['is_correct'] ? 'bg-emerald-500' : 'bg-rose-500' }}"></div>
                                <p class="text-[11px] font-medium {{ $item['is_correct'] ? 'text-emerald-700' : 'text-rose-700' }}">
                                    Jawaban Anda: <span class="font-bold">{{ $item['selected'] }}</span>
                                </p>
                            </div>
                            @if(!$item['is_correct'])
                            <div class="flex items-center gap-2 p-3 rounded-xl border bg-slate-50 border-slate-100 ml-4">
                                <div class="w-2 h-2 rounded-full bg-slate-400"></div>
                                <p class="text-[11px] font-medium text-slate-600">
                                    Jawaban Benar: <span class="font-bold">{{ $item['correct'] }}</span>
                                </p>
                            </div>
                            @endif
                            
                            @if(isset($item['explanation']) && $item['explanation'])
                            <div class="p-4 bg-indigo-50 border border-indigo-100 rounded-xl mt-3">
                                <p class="text-[10px] font-black text-indigo-400 uppercase tracking-widest mb-1">Penjelasan AI</p>
                                <p class="text-[11px] text-indigo-700 leading-relaxed italic">
                                    "{{ $item['explanation'] }}"
                                </p>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            @if(isset($attempts) && $attempts->count() > 1)
                <div class="bg-white rounded-[1.75rem] shadow-lg border border-gray-100 p-6 mx-auto mb-8 text-left">
                    <p class="text-[#8E8E93] text-[10px] font-black uppercase tracking-[0.2em] mb-4">Riwayat Nilai</p>
                    <div class="space-y-4">
                        @foreach($attempts as $a)
                            <div class="flex items-center justify-between group">
                                <div class="flex flex-col">
                                    <div class="text-sm font-bold text-[#1C1C1E]">
                                        {{ $loop->iteration == 1 ? 'Upaya Pertama' : 'Remedial ' . ($loop->iteration - 1) }}
                                    </div>
                                    <div class="text-[9px] font-bold text-slate-400">
                                        {{ $a->created_at->format('d M, H:i') }}
                                        @if($a->started_at && $a->finished_at)
                                            • {{ $a->finished_at->diff($a->started_at)->format('%im %ss') }}
                                        @endif
                                    </div>
                                </div>
                                <div class="text-sm font-black {{ $a->score >= $quiz->passing_score ? 'text-emerald-500' : 'text-[#1C1C1E]' }}">
                                    {{ $a->score }}<span class="text-[#8E8E93] font-extra-bold">/100</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="space-y-4">
                <a href="{{ route('quiz.join', $quiz->slug) }}" class="inline-block w-full bg-[#1C1C1E] hover:bg-black text-white font-black py-5 px-8 rounded-2xl transition-all duration-300 transform active:scale-[0.98] shadow-xl uppercase text-xs tracking-widest">
                    Finish & Back Home
                </a>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('[data-score]').forEach((el) => {
                if (!el.classList.contains('rounded-full')) return;
                const value = Number.parseFloat(el.dataset.score ?? '0');
                const clamped = Number.isFinite(value) ? Math.min(100, Math.max(0, value)) : 0;
                el.style.width = `${clamped}%`;
            });
        });
    </script>

    @if($participant->score !== null && $participant->score >= 70)
    <script>
        window.onload = function() {
            var duration = 4 * 1000;
            var animationEnd = Date.now() + duration;
            var defaults = { startVelocity: 45, spread: 360, ticks: 100, zIndex: 0 };

            function randomInRange(min, max) {
                return Math.random() * (max - min) + min;
            }

            var interval = setInterval(function() {
                var timeLeft = animationEnd - Date.now();

                if (timeLeft <= 0) {
                    return clearInterval(interval);
                }

                var particleCount = 60 * (timeLeft / duration);
                
                confetti(Object.assign({}, defaults, { particleCount, origin: { x: randomInRange(0.1, 0.3), y: Math.random() - 0.2 } }));
                confetti(Object.assign({}, defaults, { particleCount, origin: { x: randomInRange(0.7, 0.9), y: Math.random() - 0.2 } }));
            }, 250);
        }

        // Prevent back button
        (function() {
            history.pushState(null, null, location.href);
            window.onpopstate = function() {
                history.pushState(null, null, location.href);
            };
        })();
    </script>
    @endif

<script src="{{ asset('js/prevent-double-submit.js') }}"></script>
</body>
</html>
