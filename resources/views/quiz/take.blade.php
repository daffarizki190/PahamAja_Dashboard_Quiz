<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Take Quiz - {{ $quiz->title }}</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans text-gray-800 pb-20">

    <!-- Sticky Header -->
    <div class="sticky top-0 z-50 bg-white shadow-sm border-b border-gray-100 px-4 py-4 md:px-8 flex justify-between items-center">
        <div>
            <h1 class="text-lg font-bold text-gray-800 line-clamp-1 truncate max-w-[200px] md:max-w-md">{{ $quiz->title }}</h1>
            <p class="text-xs text-gray-500">Participant: <span class="font-semibold text-indigo-600">{{ $participant->name }}</span> ({{ $participant->nim }})</p>
        </div>
        
        <!-- Timer Widget -->
        <div class="bg-indigo-50 border border-indigo-100 rounded-full px-4 py-1.5 flex items-center gap-2">
            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span id="timerDisplay" class="font-mono font-bold text-indigo-700 tracking-wide">{{ $quiz->time_limit }}:00</span>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-6 max-w-3xl">
        
        <form id="quizForm" action="{{ route('quiz.storeAnswer', ['quiz' => $quiz->slug, 'participant' => $participant->id]) }}" method="POST">
            @csrf

            <div class="space-y-6">
                @foreach($quiz->questions as $index => $question)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden" id="question-card-{{ $question->id }}">
                    <!-- Question Header -->
                    <div class="bg-gray-50 px-5 py-3 border-b border-gray-100 flex items-start gap-3">
                        <span class="bg-indigo-100 text-indigo-800 text-xs font-bold px-2 py-1 rounded">Q{{ $index + 1 }}</span>
                        <h3 class="text-base font-medium text-gray-800 mt-0.5 leading-relaxed">{{ $question->text }}</h3>
                    </div>
                    
                    <!-- Options List -->
                    <div class="p-5 space-y-3">
                        @foreach($question->options as $option)
                        <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-indigo-50 focus-within:ring-2 focus-within:ring-indigo-500 focus-within:border-indigo-500 transition-colors option-label">
                            <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option->id }}" required
                                class="w-4 h-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                            <span class="ml-3 text-sm text-gray-700">{{ $option->text }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Submit Button Bar (Sticky at bottom for mobile friendly) -->
            <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 p-4 shrink-0 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)] z-40">
                <div class="container mx-auto max-w-3xl flex justify-between items-center">
                    <div class="text-sm text-gray-500 hidden sm:block">
                        Make sure to answer all questions before submitting.
                    </div>
                    <button type="button" onclick="confirmSubmit()" 
                        class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-8 rounded-lg shadow-md hover:shadow-lg transition duration-200">
                        Submit Answers
                    </button>
                    <!-- Actual hidden submit -->
                    <button type="submit" id="actualSubmitBtn" class="hidden"></button>
                </div>
            </div>
            
        </form>
    </div>

    <!-- UI Enhancements Scripts -->
    <script>
        // Visual indication for selected options
        document.querySelectorAll('input[type="radio"]').forEach(radio => {
            radio.addEventListener('change', function() {
                // Find all labels in the same question group
                const groupName = this.getAttribute('name');
                const radiosInGroup = document.querySelectorAll(`input[name="${groupName}"]`);
                
                // Reset styling for all options in group
                radiosInGroup.forEach(r => {
                    const label = r.closest('label');
                    label.classList.remove('bg-indigo-50', 'border-indigo-500', 'ring-1', 'ring-indigo-500');
                    label.classList.add('border-gray-200');
                });
                
                // Add active styling to selected
                if(this.checked) {
                    const label = this.closest('label');
                    label.classList.remove('border-gray-200');
                    label.classList.add('bg-indigo-50', 'border-indigo-500', 'ring-1', 'ring-indigo-500');
                }
            });
        });

        // Simple Timer Logic
        let timeInMinutes = {{ $quiz->time_limit }};
        let timeInSeconds = timeInMinutes * 60;
        const timerDisplay = document.getElementById('timerDisplay');

        const timerInterval = setInterval(() => {
            timeInSeconds--;
            
            let minutes = Math.floor(timeInSeconds / 60);
            let seconds = timeInSeconds % 60;
            
            // Format to MM:SS
            timerDisplay.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            
            // Warning color when under 2 minutes
            if (timeInSeconds <= 120 && timeInSeconds > 0) {
                timerDisplay.parentElement.classList.remove('bg-indigo-50', 'border-indigo-100');
                timerDisplay.parentElement.classList.add('bg-red-50', 'border-red-200');
                timerDisplay.classList.remove('text-indigo-700');
                timerDisplay.classList.add('text-red-700', 'animate-pulse');
            }

            // Time's up
            if (timeInSeconds <= 0) {
                clearInterval(timerInterval);
                alert("Time is up! Submitting your answers current progress.");
                document.getElementById('quizForm').submit();
            }
        }, 1000);

        function confirmSubmit() {
            // Check if all questions are answered
            const totalQuestions = {{ $quiz->questions->count() }};
            const answeredCount = document.querySelectorAll('input[type="radio"]:checked').length;
            
            if (answeredCount < totalQuestions) {
                if(confirm(`You have only answered ${answeredCount} out of ${totalQuestions} questions. Are you sure you want to submit?`)) {
                    document.getElementById('actualSubmitBtn').click();
                }
            } else {
                if(confirm('Are you sure you want to submit your final answers?')) {
                    document.getElementById('actualSubmitBtn').click();
                }
            }
        }

        // Anti-Cheat: Tab Switching Detection
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                console.warn('Violation detected: Tab switched/minimized');
                
                // Send violation log to backend using Fetch API
                fetch("{{ route('admin.quiz.cheat', ['quiz' => $quiz->slug, 'participant' => $participant->id]) }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if(data.status === 'success') {
                        console.log('Cheat attempt logged. Total:', data.attempts);
                        // Optional: Show warning to student
                        alert('Warning! Tab switching is detected and recorded. Please stay on this page.');
                    }
                })
                .catch(error => console.error('Error logging cheat attempt:', error));
            }
        });
    </script>
</body>
</html>
