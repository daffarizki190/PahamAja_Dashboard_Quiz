<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $employee->name }} - Growth Analysis</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; background: #ffffff; }
        .sidebar { background: #0f172a; border-right: 1px solid #1e293b; }
    </style>
</head>
<body class="min-h-screen">
    <div class="flex">
        <!-- Sidebar -->
        <aside class="w-64 sidebar min-h-screen sticky top-0 text-white p-6 hidden md:block">
            <div class="flex items-center gap-3 mb-10">
                <div class="bg-indigo-600 w-10 h-10 rounded-xl flex items-center justify-center font-black text-xl italic shadow-lg shadow-indigo-900/40">P</div>
                <div>
                    <h1 class="text-xl font-bold tracking-tight">Paham<span class="text-indigo-400">Aja</span></h1>
                    <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest -mt-1">Enterprise Suite</p>
                </div>
            </div>
            <nav class="space-y-1">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 text-slate-400 hover:bg-white/5 hover:text-white p-3 rounded-xl transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    <span class="text-sm font-medium">Active Assessments</span>
                </a>
                <a href="{{ route('admin.employees.index') }}" class="flex items-center gap-3 bg-white/5 border border-white/10 text-white p-3 rounded-xl transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    <span class="text-sm font-semibold">Employee Master Data</span>
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-10 max-w-6xl mx-auto">
            <a href="{{ route('admin.employees.index') }}" class="flex items-center gap-2 text-slate-400 font-bold text-xs uppercase tracking-widest hover:text-indigo-600 transition-all mb-8">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                <span>Back to Directory</span>
            </a>

            <header class="flex flex-col md:flex-row justify-between items-start md:items-end mb-12 gap-8">
                <div>
                    <p class="text-[10px] font-black text-indigo-600 uppercase tracking-[0.3em] mb-2 leading-none">Security Cleared • Corporate Analyst</p>
                    <h2 class="text-5xl font-black text-slate-900 tracking-tight leading-none">{{ $employee->name }}</h2>
                    <p class="text-slate-500 mt-4 font-medium flex items-center gap-3">
                        <span class="bg-indigo-50 text-indigo-700 px-3 py-1 rounded-lg text-xs font-black">{{ $employee->nim }}</span>
                        <span>{{ $employee->department }}</span>
                        <span class="w-1.5 h-1.5 bg-slate-200 rounded-full"></span>
                        <span class="text-slate-400">{{ $employee->position }}</span>
                    </p>
                </div>
                <div class="bg-slate-900 text-white p-6 rounded-3xl shadow-xl shadow-slate-200">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Average Integrity Score</p>
                    <p class="text-3xl font-black">{{ number_format($employee->average_score, 1) }}%</p>
                </div>
            </header>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                <!-- Assessment History -->
                <div class="bg-white border border-slate-200 rounded-3xl overflow-hidden self-start">
                    <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/50">
                        <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest">Assessment History</h3>
                    </div>
                    <div class="divide-y divide-slate-100">
                        @forelse($participations as $participation)
                        <div class="px-8 py-6 hover:bg-slate-50 transition-all cursor-default group">
                            <div class="flex justify-between items-start mb-2">
                                <h4 class="text-base font-black text-slate-800 tracking-tight group-hover:text-indigo-600 transition-all uppercase">{{ $participation->quiz->title }}</h4>
                                <span class="text-lg font-black text-slate-900">{{ $participation->score }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <p class="text-xs text-slate-400 font-bold">{{ $participation->updated_at->format('M d, Y') }}</p>
                                <div class="w-32 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-indigo-500" style="width: {{ $participation->score }}%"></div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="px-8 py-20 text-center text-slate-400 font-bold italic">No completed assessments recorded.</div>
                        @endforelse
                    </div>
                </div>

                <!-- Growth Chart -->
                <div class="bg-white border border-slate-200 p-8 rounded-3xl flex flex-col min-h-[400px]">
                    <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest mb-10">Performance Growth Radar</h3>
                    <div class="flex-1 w-full relative">
                        <canvas id="growthChart"></canvas>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('growthChart').getContext('2d');
            const data = @json($chartData);
            
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Score',
                        data: data.scores,
                        borderColor: '#4f46e5',
                        backgroundColor: (context) => {
                            const chart = context.chart;
                            const {ctx, chartArea} = chart;
                            if (!chartArea) return null;
                            const gradient = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
                            gradient.addColorStop(0, 'rgba(79, 70, 229, 0)');
                            gradient.addColorStop(1, 'rgba(79, 70, 229, 0.1)');
                            return gradient;
                        },
                        borderWidth: 4,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#4f46e5',
                        pointBorderWidth: 2,
                        pointRadius: 6,
                        pointHoverRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#0f172a',
                            padding: 12,
                            titleFont: { family: 'Outfit', size: 12, weight: 'bold' },
                            bodyFont: { family: 'Outfit', size: 12 },
                            displayColors: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            grid: { color: '#f1f5f9', drawBorder: false },
                            ticks: { 
                                font: { family: 'Outfit', weight: 'bold', size: 10 },
                                color: '#94a3b8',
                                padding: 10
                            }
                        },
                        x: {
                            grid: { display: false, drawBorder: false },
                            ticks: { 
                                font: { family: 'Outfit', weight: 'bold', size: 10 },
                                color: '#94a3b8',
                                padding: 10
                            }
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>
