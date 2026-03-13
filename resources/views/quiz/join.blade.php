<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join Quiz - {{ $quiz->title }}</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen text-gray-800 p-4">

    <div class="bg-white p-8 rounded-2xl shadow-lg w-full max-w-md border border-gray-100">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-black text-indigo-700 tracking-tight mb-2">Paham<span class="text-indigo-400">Aja</span></h1>
            <p class="text-gray-500 text-xs font-bold uppercase tracking-widest">Enterprise Assessment</p>
        </div>

        <div class="bg-indigo-50 border border-indigo-100 rounded-lg p-4 mb-6 text-center">
            <h2 class="text-lg font-semibold text-indigo-900">{{ $quiz->title }}</h2>
            <div class="mt-2 text-sm text-indigo-700 flex justify-center items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Time Limit: {{ $quiz->time_limit }} Minutes
            </div>
        </div>

        @if(session('error'))
            <div class="bg-red-50 text-red-600 p-3 rounded-lg mb-4 text-sm font-medium text-center border border-red-200">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('quiz.join.process', $quiz->slug) }}" method="POST" class="space-y-5">
            @csrf
            
            <div>
                <label for="nim" class="block text-sm font-medium text-gray-700 mb-1">Student ID (NIM)</label>
                <input type="text" id="nim" name="nim" required value="{{ old('nim') }}"
                    class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all placeholder-gray-400"
                    placeholder="e.g. 10101001">
                @error('nim')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                <input type="text" id="name" name="name" required value="{{ old('name') }}"
                    class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all placeholder-gray-400"
                    placeholder="Enter your full name">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" 
                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-black py-4 px-4 rounded-xl shadow-lg shadow-indigo-100 transition duration-200 mt-4 focus:ring-4 focus:ring-indigo-200 uppercase text-xs tracking-widest">
                Start Assessment
            </button>
            
            <div class="text-center mt-6">
                <a href="{{ url('/') }}" class="text-slate-400 hover:text-indigo-600 text-xs font-bold uppercase tracking-widest transition-all">
                    ← Back to Entry
                </a>
            </div>
        </form>
    </div>

</body>
</html>
