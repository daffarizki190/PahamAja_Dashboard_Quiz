<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk Kuis - {{ $quiz->title }}</title>
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; }
        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body class="bg-[#F2F2F7] flex items-center justify-center min-h-screen p-4">

    <div class="glass p-8 md:p-10 rounded-[2rem] shadow-2xl w-full max-w-sm">
        <div class="text-center mb-10">
            <div class="inline-block p-1 bg-indigo-50 rounded-2xl mb-4">
                <div class="w-16 h-16 bg-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-200">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <h1 class="text-2xl font-extrabold text-[#1C1C1E] tracking-tight">Paham<span class="text-indigo-600">Aja</span></h1>
            <p class="text-[#8E8E93] text-sm font-medium mt-1">Jika mengalami kendala, hubungi pengawas ujian.</p>
        </div>

        <div class="mb-8 p-5 bg-white rounded-2xl border border-gray-100 shadow-sm">
            <h2 class="text-[#1C1C1E] font-bold text-base text-center leading-snug">{{ $quiz->title }}</h2>
            <div class="mt-3 flex justify-center items-center gap-1.5 text-[#8E8E93] text-xs font-semibold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                DURASI: {{ $quiz->time_limit }} MENIT
            </div>
        </div>

        @if(session('error'))
            <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-6 text-sm font-semibold text-center border border-red-100 flex items-center justify-center gap-2">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('quiz.join.process', $quiz->slug) }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="space-y-2">
                <label for="nim" class="block text-xs font-bold text-[#8E8E93] uppercase tracking-wider px-1">Nomor Induk Karyawan (NIK)</label>
                <div class="relative group">
                    <input type="text" id="nim" name="nim" required autofocus
                        class="w-full px-5 py-4 bg-white border border-gray-200 rounded-2xl focus:ring-4 focus:ring-indigo-50 focus:border-indigo-500 outline-none transition-all placeholder-[#C7C7CC] text-[#1C1C1E] font-medium"
                        placeholder="Masukkan NIK Anda" value="{{ old('nim') }}">
                </div>
                @error('nim')
                    <p class="mt-1 text-xs text-red-600 font-medium px-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" 
                class="group w-full bg-[#1C1C1E] hover:bg-black text-white font-bold py-4.5 px-6 rounded-2xl transition-all duration-300 transform active:scale-[0.98] shadow-xl hover:shadow-indigo-100 mt-2">
                <span class="flex items-center justify-center gap-2">
                    Mulai Ujian
                    <svg class="w-5 h-5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </span>
            </button>
        </form>
    </div>

</body>
</html>
