<?php

use App\Models\Employee;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('employees:cleanup-non-centrepark {--dry-run}', function () {
    $query = Employee::query()
        ->whereRaw('LOWER(department) NOT LIKE ? AND LOWER(department) NOT LIKE ?', ['%centrep%', '%centre%park%']);

    $count = (int) $query->count();

    $samples = $query
        ->orderBy('created_at', 'desc')
        ->limit(20)
        ->get(['id', 'name', 'nim', 'department', 'position'])
        ->map(function (Employee $e) {
            return [
                'id' => (string) $e->id,
                'name' => (string) $e->name,
                'nim' => (string) $e->nim,
                'department' => (string) $e->department,
                'position' => (string) $e->position,
            ];
        })
        ->all();

    if ($count === 0) {
        $this->info('Tidak ada data karyawan non-PT CENTREPARK yang perlu dihapus.');

        return 0;
    }

    $this->warn("Ditemukan {$count} karyawan non-PT CENTREPARK. Contoh 20 data terakhir:");
    $this->table(['ID', 'Nama', 'NIM', 'Department', 'Position'], array_map(function ($row) {
        return [$row['id'], $row['name'], $row['nim'], $row['department'], $row['position']];
    }, $samples));

    if ((bool) $this->option('dry-run')) {
        $this->info('Dry run aktif: tidak ada data yang dihapus.');

        return 0;
    }

    $deleted = (int) Employee::query()
        ->whereRaw('LOWER(department) NOT LIKE ? AND LOWER(department) NOT LIKE ?', ['%centrep%', '%centre%park%'])
        ->delete();
    $this->info("Selesai: {$deleted} data karyawan non-PT CENTREPARK dihapus.");

    return 0;
})->purpose('Hapus data karyawan non-PT CENTREPARK');
