<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Ujian - {{ $quiz->title }}</title>
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <style>
        body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; }
        .glass-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
    </style>
</head>
<body class="bg-[#F2F2F7] flex items-center justify-center min-h-screen p-6">

    <div class="glass-card p-10 md:p-12 rounded-[2.5rem] shadow-2xl w-full max-w-lg text-center relative overflow-hidden">
        
        <!-- Apple-style Gradient Background Accessory -->
        <div class="absolute top-0 left-0 right-0 h-40 bg-gradient-to-br {{ $participant->score >= 70 ? 'from-green-400 to-emerald-600' : ($participant->score >= 50 ? 'from-orange-300 to-amber-500' : 'from-rose-400 to-red-600') }} opacity-90 z-0"></div>

        <div class="relative z-10 pt-4">
            @if(session('success'))
                <div class="inline-block bg-white/20 backdrop-blur-md text-white px-5 py-2 rounded-full text-xs font-black uppercase tracking-widest mb-8 border border-white/30">
                    {{ session('success') }}
                </div>
            @endif

            <h1 class="text-3xl font-black text-white mb-10 drop-shadow-lg tracking-tight">Ujian Selesai!</h1>
            
            <div class="bg-white rounded-[2rem] shadow-xl border border-gray-100 p-8 mx-auto -mt-4 relative mb-10 group">
                <p class="text-[#8E8E93] text-[10px] font-black uppercase tracking-[0.2em] mb-4">Skor Akhir Anda</p>
                
                <div class="relative inline-block">
                    <div class="text-[5rem] font-black leading-none tracking-tighter {{ $participant->score >= 70 ? 'text-emerald-500' : ($participant->score >= 50 ? 'text-amber-500' : 'text-rose-500') }} transition-transform group-hover:scale-105 duration-500">
                        {{ $participant->score ?? 0 }}<span class="text-2xl text-[#C7C7CC] ml-1 font-extrabold">/100</span>
                    </div>
                </div>
                
                <div class="h-3 w-full bg-[#F2F2F7] rounded-full mt-10 mb-4 overflow-hidden p-0.5">
                    <div class="h-full {{ $participant->score >= 70 ? 'bg-emerald-500' : ($participant->score >= 50 ? 'bg-amber-500' : 'bg-rose-500') }} rounded-full transition-all duration-1000 ease-out shadow-sm" 
                         style="width: {{ $participant->score ?? 0 }}%"></div>
                </div>

                <div class="mt-8 pt-6 border-t border-[#F2F2F7] grid grid-cols-2 gap-6 text-left">
                    <div>
                        <p class="text-[9px] text-[#8E8E93] font-black uppercase tracking-wider mb-1">NAMA PESERTA</p>
                        <p class="font-bold text-[#1C1C1E] text-sm truncate uppercase">{{ $participant->name }}</p>
                    </div>
                    <div>
                        <p class="text-[9px] text-[#8E8E93] font-black uppercase tracking-wider mb-1">NOMOR INDUK</p>
                        <p class="font-bold text-[#1C1C1E] text-sm tracking-tight">{{ $participant->nim }}</p>
                    </div>
                </div>
            </div>

            <div class="text-[#1C1C1E] mb-10 leading-relaxed text-[15px] px-2 font-medium">
                @if($participant->score !== null && $participant->score >= 70)
                    Luar biasa! Anda telah menyelesaikan <span class="text-indigo-600 font-bold">{{ $quiz->title }}</span> dengan hasil yang sangat memuaskan.
                @else
                    Terima kasih telah berpartisipasi dalam <span class="text-indigo-600 font-bold">{{ $quiz->title }}</span>. Hasil ujian Anda telah tersimpan di sistem.
                @endif
            </div>

            @if(isset($attempts) && $attempts->count() > 1)
                <div class="bg-white rounded-[1.75rem] shadow-lg border border-gray-100 p-6 mx-auto mb-8 text-left">
                    <p class="text-[#8E8E93] text-[10px] font-black uppercase tracking-[0.2em] mb-4">Riwayat Nilai</p>
                    <div class="space-y-3">
                        @foreach($attempts as $a)
                            <div class="flex items-center justify-between">
                                <div class="text-sm font-bold text-[#1C1C1E]">
                                    @if($loop->first)
                                        Nilai Saya
                                    @else
                                        Remedial {{ $loop->iteration - 1 }}
                                    @endif
                                </div>
                                <div class="text-sm font-black text-[#1C1C1E]">
                                    {{ $a->score }}<span class="text-[#8E8E93] font-extrabold">/100</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="space-y-4">
                <a href="{{ route('quiz.join', $quiz->slug) }}" class="inline-block w-full bg-[#1C1C1E] hover:bg-black text-white font-bold py-4.5 px-8 rounded-2xl transition-all duration-300 transform active:scale-[0.98] shadow-xl">
                    Finish
                </a>
            </div>
        </div>
    </div>

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
    </script>
    @endif

</body>
</html>
