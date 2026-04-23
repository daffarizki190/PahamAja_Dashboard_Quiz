<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PahamAja — Platform Assessment Modern</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Tailwind for rapid landing page design -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                    colors: {
                        purple: {
                            50: '#f5f3ff',
                            100: '#ede9fe',
                            500: '#8b5cf6',
                            600: '#7c3aed',
                            700: '#6d28d9',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        body {
            background-color: #fbfbfe;
            color: #1e1b4b;
        }
        .hero-gradient {
            background: radial-gradient(circle at 10% 20%, rgba(124, 58, 237, 0.05) 0%, transparent 50%),
                        radial-gradient(circle at 90% 80%, rgba(124, 58, 237, 0.03) 0%, transparent 50%);
        }
        .glass-nav {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(124, 58, 237, 0.1);
        }
        .btn-premium {
            background: linear-gradient(135deg, #7c3aed 0%, #6366f1 100%);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .btn-premium:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(124, 58, 237, 0.2);
        }
        .card-premium {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 24px;
            transition: all 0.3s ease;
        }
        .card-premium:hover {
            border-color: #ddd6fe;
            box-shadow: 0 20px 40px rgba(124, 58, 237, 0.05);
        }
        .floating {
            animation: floating 3s ease-in-out infinite;
        }
        @keyframes floating {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
    </style>
</head>
<body class="font-sans antialiased hero-gradient min-h-screen">
    
    <!-- Navigation -->
    <nav class="glass-nav fixed top-0 w-full z-50">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-10 h-10 bg-purple-600 rounded-xl flex items-center justify-center text-white font-black italic shadow-lg shadow-purple-500/20">P</div>
                <span class="text-xl font-extrabold tracking-tight">Paham<span class="text-purple-600">Aja</span></span>
            </div>
            
            <div class="hidden md:flex items-center gap-8">
                <a href="#" class="text-sm font-semibold text-slate-600 hover:text-purple-600 transition-colors">Fitur</a>
                <a href="#" class="text-sm font-semibold text-slate-600 hover:text-purple-600 transition-colors">Tentang Kami</a>
                <a href="#" class="text-sm font-semibold text-slate-600 hover:text-purple-600 transition-colors">Kontak</a>
            </div>

            <div class="flex items-center gap-4">
                @auth
                    <a href="{{ url('/admin') }}" class="text-sm font-bold text-slate-700 hover:text-purple-600 px-4 py-2 opacity-80">Dashboard</a>
                @else
                    <a href="{{ route('admin.login') }}" class="text-sm font-bold text-slate-700 hover:text-purple-600 px-4 py-2">Masuk Admin</a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="pt-40 pb-20 px-6">
        <div class="max-w-7xl mx-auto grid lg:grid-cols-2 gap-16 items-center">
            <div>
                <span class="inline-block px-4 py-1.5 bg-purple-50 text-purple-600 text-[11px] font-black uppercase tracking-widest rounded-full mb-6 border border-purple-100">
                    <i class="fa-solid fa-sparkles mr-1.5"></i> Revolusi Assessment Karyawan
                </span>
                <h1 class="text-6xl md:text-7xl font-extrabold tracking-tight leading-[1.1] mb-8">
                    Ukur <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-indigo-500">Potensi</span> Team Anda Lebih Akurat.
                </h1>
                <p class="text-lg text-slate-500 leading-relaxed mb-10 max-w-lg font-medium">
                    Platform quiz dan assessment cerdas dengan integrasi AI untuk membantu perusahaan mengevaluasi kompetensi karyawan secara objektif dan menyenangkan.
                </p>
                <div class="flex flex-wrap gap-4">
                    <a href="#join-quiz" class="btn-premium text-white px-8 py-4 rounded-2xl font-bold text-lg flex items-center gap-3">
                        Mulai Assessment <i class="fa-solid fa-arrow-right text-sm"></i>
                    </a>
                    <div class="flex items-center gap-4 px-2">
                        <div class="flex -space-x-3">
                            <img src="https://i.pravatar.cc/150?u=1" class="w-10 h-10 rounded-full border-2 border-white" alt="">
                            <img src="https://i.pravatar.cc/150?u=2" class="w-10 h-10 rounded-full border-2 border-white" alt="">
                            <img src="https://i.pravatar.cc/150?u=3" class="w-10 h-10 rounded-full border-2 border-white" alt="">
                        </div>
                        <p class="text-xs font-bold text-slate-400 leading-tight">Digunakan oleh <br><span class="text-slate-800">500+ Karyawan Aktif</span></p>
                    </div>
                </div>
            </div>

            <div class="relative">
                <div class="absolute -top-20 -right-20 w-64 h-64 bg-purple-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-pulse"></div>
                <div class="absolute -bottom-20 -left-20 w-64 h-64 bg-indigo-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-pulse delay-700"></div>
                
                <div class="relative card-premium p-4 shadow-2xl floating">
                    <div class="bg-slate-900 rounded-2xl overflow-hidden aspect-video flex items-center justify-center p-8 relative">
                        <!-- Simulated AI UI -->
                        <div class="w-full space-y-4">
                            <div class="flex items-center gap-3">
                                <div class="w-3 h-3 rounded-full bg-red-400"></div>
                                <div class="w-3 h-3 rounded-full bg-amber-400"></div>
                                <div class="w-3 h-3 rounded-full bg-emerald-400"></div>
                            </div>
                            <div class="p-4 bg-white/5 rounded-xl border border-white/10">
                                <div class="h-2 w-1/2 bg-white/20 rounded mb-2"></div>
                                <div class="h-2 w-3/4 bg-white/10 rounded"></div>
                            </div>
                            <div class="p-4 bg-purple-500/20 rounded-xl border border-purple-500/30 ml-8">
                                <div class="h-2 w-2/3 bg-purple-300 rounded mb-2"></div>
                                <div class="h-2 w-full bg-purple-300/50 rounded"></div>
                            </div>
                        </div>
                        <div class="absolute inset-x-0 bottom-0 p-6 bg-gradient-to-t from-slate-900 via-slate-900/40 to-transparent">
                            <p class="text-white font-bold text-sm tracking-wide">PahamAja AI Interface v2.0</p>
                        </div>
                    </div>
                </div>
                
                <div class="absolute -bottom-10 -right-10 bg-white p-6 shadow-xl rounded-3xl border border-slate-100 hidden md:block">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600">
                            <i class="fa-solid fa-chart-line"></i>
                        </div>
                        <p class="text-xs font-black uppercase text-slate-400 tracking-wider">Avg. Team Score</p>
                    </div>
                    <p class="text-3xl font-black">88.5<span class="text-sm text-slate-400 ml-1">%</span></p>
                </div>
            </div>
        </div>
    </header>

    <!-- Features -->
    <section class="py-20 px-6">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-extrabold mb-4">Mengapa Menggunakan PahamAja?</h2>
                <div class="w-20 h-1 bg-purple-600 mx-auto rounded-full"></div>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="card-premium p-8">
                    <div class="w-14 h-14 bg-purple-50 rounded-2xl flex items-center justify-center text-purple-600 text-2xl mb-6">
                        <i class="fa-solid fa-robot"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">AI Question Generator</h3>
                    <p class="text-slate-500 text-sm leading-relaxed">Buat ratusan soal berkualitas dari file PDF atau teks hanya dalam hitungan detik dengan kekuatan AI Gemini.</p>
                </div>
                <div class="card-premium p-8">
                    <div class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600 text-2xl mb-6">
                        <i class="fa-solid fa-brain"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Instant Explanation</h3>
                    <p class="text-slate-500 text-sm leading-relaxed">Berikan pembelajaran yang bermakna dengan penjelasan otomatis untuk setiap jawaban yang benar atau salah.</p>
                </div>
                <div class="card-premium p-8">
                    <div class="w-14 h-14 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 text-2xl mb-6">
                        <i class="fa-solid fa-ranking-star"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Deep Insights</h3>
                    <p class="text-slate-500 text-sm leading-relaxed">Analisis performa mendalam untuk setiap karyawan dengan grafik pertumbuhan yang sangat informatif.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Participation Form -->
    <section id="join-quiz" class="py-20 px-6 bg-slate-900 border-y border-slate-800 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')]"></div>
        <div class="max-w-4xl mx-auto relative z-10 text-center">
            <h2 class="text-4xl font-extrabold text-white mb-6">Siap Menjalani Assessment?</h2>
            <p class="text-slate-400 mb-12 text-lg">Masukkan Kode Quiz yang diberikan oleh admin Anda untuk memulai.</p>
            
            <div class="bg-white/10 p-2 rounded-3xl border border-white/10 flex flex-col md:flex-row gap-3">
                <input type="text" id="quizCodeInput" placeholder="Contoh: QA-2024" 
                       class="flex-1 bg-transparent border-none px-8 py-4 text-white placeholder-slate-500 outline-none text-lg font-bold">
                <button type="button" onclick="joinQuiz()" class="bg-white text-slate-900 px-10 py-4 rounded-2xl font-bold text-lg hover:bg-purple-50 transition-all">
                    Gabung Quiz
                </button>
            </div>
            <script>
                function joinQuiz() {
                    const code = document.getElementById('quizCodeInput').value.trim();
                    if(code) {
                        window.location.href = '/quiz/' + code;
                    } else {
                        alert('Silakan masukkan kode kuis.');
                    }
                }
            </script>

            <p class="text-slate-500 mt-6 text-sm">Hanya berlaku untuk assessment yang berstatus AKTIF.</p>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-12 px-6 border-t border-slate-200 mt-20">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-8 text-slate-500 text-sm">
            <div class="flex items-center gap-2 grayscale brightness-50">
                <div class="w-8 h-8 bg-slate-400 rounded-lg flex items-center justify-center text-white font-black italic">P</div>
                <span class="text-lg font-extrabold tracking-tight">PahamAja</span>
            </div>
            <p>&copy; 2026 PahamAja Learning Management System. Seluruh hak cipta dilindungi.</p>
            <div class="flex gap-6">
                <a href="#" class="hover:text-purple-600 transition-colors">Privacy Policy</a>
                <a href="#" class="hover:text-purple-600 transition-colors">Terms of Service</a>
            </div>
        </div>
    </footer>

    <script src="{{ asset('js/prevent-double-submit.js') }}"></script>
</body>
</html>
