<?php

namespace App\Http\Controllers;

use App\Services\BackupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    protected $backupService;

    public function __construct(BackupService $backupService)
    {
        $this->backupService = $backupService;
    }

    public function download()
    {
        try {
            $backup = $this->backupService->generateBackup();
            return response()->download($backup['path'], $backup['name'])->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membuat cadangan: ' . $e->getMessage());
        }
    }
}
