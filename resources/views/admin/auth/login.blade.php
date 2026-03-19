<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - PahamAja</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .animate-fade-in {
            animation: fadeIn 0.8s ease-out forwards;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.98); }
            to { opacity: 1; transform: scale(1); }
        }
    </style>
</head>
<body class="min-h-screen bg-gray-50 flex items-center justify-center p-6">
    <div class="w-full max-w-md animate-fade-in opacity-0">
        <div class="bg-white border border-slate-200 shadow-sm rounded-3xl p-10">
            <div class="flex items-center gap-3 mb-8">
                <div class="bg-indigo-600 w-11 h-11 rounded-2xl flex items-center justify-center font-black text-xl italic text-white shadow-lg shadow-indigo-600/20">P</div>
                <div>
                    <h1 class="text-xl font-extrabold tracking-tight text-slate-900">Paham<span class="text-indigo-600">Aja</span></h1>
                    <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest -mt-0.5">Admin Access</p>
                </div>
            </div>

            @if(session('error'))
                <div class="bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-2xl text-sm font-bold mb-6">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-2xl text-sm font-bold mb-6">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login.submit') }}" class="space-y-5">
                @csrf
                <div class="space-y-2">
                    <label class="text-xs font-black text-slate-400 uppercase tracking-widest px-1">Password</label>
                    <input type="password" name="password" required class="w-full bg-white border border-slate-200 px-5 py-4 rounded-2xl font-bold text-slate-700 focus:outline-none focus:ring-4 focus:ring-indigo-600/10 focus:border-indigo-600">
                </div>

                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-4 rounded-2xl font-black text-sm shadow-lg shadow-indigo-600/20 transition-all active:scale-[0.99]">
                    Masuk Admin
                </button>
            </form>

            <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest mt-8 text-center">
                Selamat datang, silakan masuk untuk melanjutkan
            </p>
        </div>
    </div>
</body>
</html>
