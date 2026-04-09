<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PahamAja Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Outfit', sans-serif; 
            background: linear-gradient(135deg, #f4f7fb 0%, #e0e7ff 100%); 
        }
        .animate-fade-in {
            animation: fadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.98) translateY(20px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 10px 40px -10px rgba(79, 70, 229, 0.15);
        }
        .btn-primary {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            box-shadow: 0 4px 14px 0 rgba(99, 102, 241, 0.39);
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #4338ca 0%, #6d28d9 100%);
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.23);
            transform: translateY(-2px) scale(1.02);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-6">
    <div class="w-full max-w-md animate-fade-in opacity-0">
        <div class="glass-card rounded-3xl p-10">
            <div class="flex items-center gap-3 mb-8">
                <div class="bg-indigo-600 w-11 h-11 rounded-2xl flex items-center justify-center font-black text-xl italic text-white shadow-lg shadow-indigo-600/20">P</div>
                <div>
                    <h1 class="text-xl font-extrabold tracking-tight text-slate-900">Paham<span class="text-indigo-600">Aja</span></h1>
                    <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest -mt-0.5">Portal Akses</p>
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

                <button type="submit" class="w-full btn-primary text-white px-6 py-4 rounded-2xl font-black text-sm active:scale-[0.99]">
                    Masuk →
                </button>
            </form>

            <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest mt-8 text-center">
                Selamat datang, silakan masuk untuk melanjutkan
            </p>
        </div>
    </div>
<script src="{{ asset('js/prevent-double-submit.js') }}"></script>
</body>
</html>
