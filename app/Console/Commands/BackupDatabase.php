<?php

namespace App\Console\Commands;

use App\Services\BackupService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:db';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a database and assets backup ZIP file';

    /**
     * Execute the console command.
     */
    public function handle(BackupService $backupService)
    {
        $this->info('Starting backup process...');
        
        try {
            $backup = $backupService->generateBackup();
            
            $this->info("Backup successfully created: {$backup['name']}");
            $this->info("Path: {$backup['path']}");
            
            Log::info("Automated backup successful: {$backup['name']}");
        } catch (\Exception $e) {
            $this->error("Backup failed: " . $e->getMessage());
            Log::error("Automated backup failed: " . $e->getMessage());
        }
    }
}
