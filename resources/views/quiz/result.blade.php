<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Result - {{ $quiz->title }}</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen p-4">

    <div class="bg-white p-8 md:p-10 rounded-2xl shadow-xl w-full max-w-lg border border-gray-100 text-center relative overflow-hidden">
        
        <!-- Decorative Header Background -->
        <div class="absolute top-0 left-0 right-0 h-32 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-t-2xl z-0"></div>

        <div class="relative z-10 pt-6">
            @if(session('success'))
                <div class="inline-block bg-green-100 text-green-800 px-4 py-1.5 rounded-full text-sm font-semibold mb-6 shadow-sm border border-green-200">
                    {{ session('success') }}
                </div>
            @endif

            <h1 class="text-3xl font-bold text-white mb-8 drop-shadow-md">Quiz Completed!</h1>
            
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 mx-auto -mt-4 relative mb-6">
                <p class="text-gray-500 text-sm font-medium mb-1 uppercase tracking-wider">Your Score</p>
                
                <div class="text-6xl font-black {{ $participant->score >= 70 ? 'text-green-500' : ($participant->score >= 50 ? 'text-yellow-500' : 'text-red-500') }} my-4">
                    {{ $participant->score ?? 0 }}<span class="text-3xl text-gray-300">/100</span>
                </div>
                
                <div class="h-2 w-full bg-gray-100 rounded-full mt-6 mb-2 overflow-hidden">
                    <div class="h-full {{ $participant->score >= 70 ? 'bg-green-500' : ($participant->score >= 50 ? 'bg-yellow-500' : 'bg-red-500') }} rounded-full" 
                         style="width: {{ $participant->score ?? 0 }}%"></div>
                </div>

                <div class="mt-4 pt-4 border-t border-gray-100 grid grid-cols-2 gap-4 text-left">
                    <div>
                        <p class="text-xs text-gray-400 font-medium">NAME</p>
                        <p class="font-semibold text-gray-800 truncate">{{ $participant->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 font-medium">NIM</p>
                        <p class="font-semibold text-gray-800">{{ $participant->nim }}</p>
                    </div>
                </div>
            </div>

            <div class="text-gray-600 mb-8 mt-4 leading-relaxed text-sm px-4">
                @if($participant->score !== null && $participant->score >= 70)
                    Congratulations! Excellent work answering the questions for <span class="font-semibold text-indigo-700">{{ $quiz->title }}</span>.
                @else
                    Thank you for participating in <span class="font-semibold text-indigo-700">{{ $quiz->title }}</span>. Here is your final result.
                @endif
            </div>

            <div class="mt-4">
                <a href="{{ url('/') }}" class="inline-block bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2.5 px-6 rounded-lg transition-colors border border-gray-200">
                    Back to Home
                </a>
            </div>
        </div>
    </div>

    @if($participant->score !== null && $participant->score >= 70)
    <script>
        // Confetti effect for good scores
        window.onload = function() {
            var duration = 3 * 1000;
            var animationEnd = Date.now() + duration;
            var defaults = { startVelocity: 30, spread: 360, ticks: 60, zIndex: 0 };

            function randomInRange(min, max) {
                return Math.random() * (max - min) + min;
            }

            var interval = setInterval(function() {
                var timeLeft = animationEnd - Date.now();

                if (timeLeft <= 0) {
                    return clearInterval(interval);
                }

                var particleCount = 50 * (timeLeft / duration);
                
                confetti(Object.assign({}, defaults, { particleCount, origin: { x: randomInRange(0.1, 0.3), y: Math.random() - 0.2 } }));
                confetti(Object.assign({}, defaults, { particleCount, origin: { x: randomInRange(0.7, 0.9), y: Math.random() - 0.2 } }));
            }, 250);
        }
    </script>
    @endif

</body>
</html>
