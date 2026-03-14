<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Directory - PahamAja Enterprise</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; background: #f8fafc; }
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
                    <span class="text-sm font-semibold">Employee Insights</span>
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-10 max-w-7xl mx-auto">
            <header class="flex justify-between items-center mb-10">
                <div class="flex items-center gap-4">
                    <a href="{{ route('admin.dashboard') }}" class="bg-white p-3 rounded-xl border border-slate-200 text-slate-400 hover:text-indigo-600 transition-all shadow-sm group">
                        <svg class="w-5 h-5 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    </a>
                    <div>
                        <h2 class="text-3xl font-black text-slate-900 tracking-tight">Employee Insights</h2>
                        <p class="text-slate-500 font-medium">Directory + ringkasan growth dalam satu halaman.</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.employees.export') }}" class="bg-white border border-slate-200 text-slate-700 px-6 py-3 rounded-xl font-bold text-sm hover:bg-gray-50 transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span>Export Excel</span>
                    </a>
                    <button onclick="openRegisterModal()" class="bg-indigo-600 text-white px-6 py-3 rounded-xl font-bold text-sm shadow-lg shadow-indigo-600/20 hover:bg-indigo-700 transition-all">
                        Register Employee
                    </button>
                </div>
            </header>

            @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-6 py-4 rounded-xl mb-8 flex items-center justify-between animate-fade-in font-bold text-sm">
                {{ session('success') }}
            </div>
            @endif

            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Name & Position</th>
                            <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Employee ID / NIM</th>
                            <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Avg Performance</th>
                            <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($employees as $employee)
                        <tr class="hover:bg-slate-50/50 transition-all border-l-4 border-l-transparent hover:border-l-indigo-600">
                            <td class="px-8 py-6">
                                <p class="text-sm font-black text-slate-800 tracking-tight">{{ $employee->name }}</p>
                                <p class="text-xs text-slate-400 font-bold uppercase tracking-widest leading-none">{{ $employee->department }} • {{ $employee->position }}</p>
                            </td>
                            <td class="px-8 py-6">
                                <span class="bg-slate-100 text-slate-600 px-3 py-1.5 rounded-lg text-xs font-black">{{ $employee->nim }}</span>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-3">
                                    @php($avg = $statsByNim[$employee->nim]['avg'] ?? 0)
                                    <span class="text-sm font-black text-slate-700">{{ number_format($avg, 1) }}%</span>
                                    <div class="w-24 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                        <div class="h-full bg-emerald-500" style="width: {{ $avg }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6 text-right flex items-center justify-end gap-2">
                                <button onclick="openEditModal({{ json_encode($employee) }})" class="text-indigo-600 bg-indigo-50 hover:bg-indigo-100 p-2 rounded-lg transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <form action="{{ route('admin.employees.destroy', $employee->id) }}" method="POST" onsubmit="return confirm('Hapus karyawan ini dari database?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600 bg-rose-50 hover:bg-rose-100 p-2 rounded-lg transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                                <a href="{{ route('admin.employees.show', $employee->id) }}" class="text-slate-600 bg-slate-100 hover:bg-slate-200 p-2 rounded-lg transition-all ml-2" title="View Growth Report">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-8 py-20 text-center text-slate-400 font-bold italic">No employees registered.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-10 bg-white border border-slate-200 rounded-2xl overflow-hidden">
                <div class="px-8 py-6 border-b border-slate-200 flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Ringkasan</p>
                        <h3 class="text-xl font-black text-slate-900 tracking-tight">Perkembangan Nilai</h3>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50 border-b border-slate-200">
                            <tr class="text-[10px] text-slate-400 font-black uppercase tracking-[0.2em]">
                                <th class="px-8 py-5">Karyawan</th>
                                <th class="px-8 py-5">Jumlah Kuis</th>
                                <th class="px-8 py-5">Rata-rata</th>
                                <th class="px-8 py-5">Terakhir</th>
                                <th class="px-8 py-5">Perubahan</th>
                                <th class="px-8 py-5 text-right">Detail</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($growthRows as $row)
                                <tr class="hover:bg-indigo-50/30 transition-all">
                                    <td class="px-8 py-5">
                                        <p class="text-slate-900 font-black">{{ $row['name'] }}</p>
                                        <p class="text-slate-400 text-xs font-bold">{{ $row['nim'] }} • {{ $row['department'] }}</p>
                                    </td>
                                    <td class="px-8 py-5">
                                        <span class="bg-slate-100 text-slate-700 px-3 py-1 rounded-lg text-xs font-black">{{ $row['attempts'] }}</span>
                                    </td>
                                    <td class="px-8 py-5">
                                        <span class="bg-slate-900 text-white px-3 py-1 rounded-lg text-xs font-black">{{ number_format($row['avg'], 1) }}%</span>
                                    </td>
                                    <td class="px-8 py-5">
                                        @if(!is_null($row['last']))
                                            <span class="text-slate-800 font-black">{{ number_format($row['last'], 0) }}%</span>
                                        @else
                                            <span class="text-slate-300 font-bold">-</span>
                                        @endif
                                    </td>
                                    <td class="px-8 py-5">
                                        @if(!is_null($row['delta']))
                                            @if($row['delta'] > 0)
                                                <span class="inline-flex items-center gap-2 bg-emerald-50 text-emerald-700 px-3 py-1 rounded-lg text-xs font-black">+{{ number_format($row['delta'], 1) }}</span>
                                            @elseif($row['delta'] < 0)
                                                <span class="inline-flex items-center gap-2 bg-rose-50 text-rose-700 px-3 py-1 rounded-lg text-xs font-black">{{ number_format($row['delta'], 1) }}</span>
                                            @else
                                                <span class="inline-flex items-center gap-2 bg-slate-100 text-slate-700 px-3 py-1 rounded-lg text-xs font-black">{{ number_format($row['delta'], 1) }}</span>
                                            @endif
                                        @else
                                            <span class="text-slate-300 font-bold">-</span>
                                        @endif
                                    </td>
                                    <td class="px-8 py-5 text-right">
                                        <a href="{{ route('admin.employees.show', $row['id']) }}" class="inline-flex items-center gap-2 bg-indigo-600 text-white px-4 py-2 rounded-xl text-xs font-black hover:bg-indigo-700 transition-all">
                                            <span>Detail</span>
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-8 py-20 text-center text-slate-400 font-bold italic">Belum ada data.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Edit Employee Modal -->
    <div id="editModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden animate-slide-up">
            <div class="p-8 border-b border-slate-100 flex justify-between items-center">
                <h3 class="text-xl font-black text-slate-800 tracking-tight">Edit Employee</h3>
                <button onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600 transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <form id="editForm" method="POST" class="p-8 space-y-6">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Full Name</label>
                    <input type="text" name="name" id="edit_name" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all font-bold">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Department</label>
                        <input type="text" name="department" id="edit_dept" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all font-bold">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Position</label>
                        <input type="text" name="position" id="edit_pos" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all font-bold">
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Status</label>
                    <select name="status" id="edit_status" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all font-bold">
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>
                <button type="submit" class="w-full bg-indigo-600 text-white py-4 rounded-2xl font-black text-xs uppercase tracking-widest shadow-lg shadow-indigo-600/20 hover:bg-indigo-700 transition-all">
                    Save Changes
                </button>
            </form>
        </div>
    </div>

    <!-- Register Employee Modal -->
    <div id="registerModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden animate-slide-up">
            <div class="p-8 border-b border-slate-100 flex justify-between items-center">
                <h3 class="text-xl font-black text-slate-800 tracking-tight">New Employee</h3>
                <button onclick="closeRegisterModal()" class="text-slate-400 hover:text-slate-600 transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <form action="{{ route('admin.employees.store') }}" method="POST" class="p-8 space-y-6">
                @csrf
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Full Name</label>
                    <input type="text" name="name" required placeholder="e.g. John Doe" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all font-bold">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Employee ID (NIM)</label>
                    <input type="text" name="nim" required placeholder="e.g. CP-2024-001" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all font-bold">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Department</label>
                        <input type="text" name="department" required placeholder="e.g. IT" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all font-bold">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Position</label>
                        <input type="text" name="position" required placeholder="e.g. Staff" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all font-bold">
                    </div>
                </div>
                <button type="submit" class="w-full bg-emerald-600 text-white py-4 rounded-2xl font-black text-xs uppercase tracking-widest shadow-lg shadow-emerald-600/20 hover:bg-emerald-700 transition-all">
                    Register Member
                </button>
            </form>
        </div>
    </div>

    <script>
        function openRegisterModal() {
            const modal = document.getElementById('registerModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closeRegisterModal() {
            const modal = document.getElementById('registerModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = 'auto';
        }

        function openEditModal(employee) {
            // ... (keep existing openEditModal)
            const modal = document.getElementById('editModal');
            const form = document.getElementById('editForm');
            
            // Set values
            document.getElementById('edit_name').value = employee.name;
            document.getElementById('edit_dept').value = employee.department;
            document.getElementById('edit_pos').value = employee.position;
            document.getElementById('edit_status').value = employee.status || 'Active';
            
            // Set dynamic action URL
            form.action = `/admin/employees/${employee.id}`;
            
            // Show modal
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closeEditModal() {
            const modal = document.getElementById('editModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = 'auto';
        }

        // Close on backdrop click
        window.onclick = function(event) {
            const modal = document.getElementById('editModal');
            if (event.target == modal) {
                closeEditModal();
            }
        }
    </script>
</body>
</html>
