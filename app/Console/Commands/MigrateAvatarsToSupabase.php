<?php

namespace App\Console\Commands;

use App\Models\Employee;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MigrateAvatarsToSupabase extends Command
{
    protected $signature = 'avatars:migrate-to-supabase {--dry-run : Preview without uploading}';
    protected $description = 'Migrate all local avatar files to Supabase Storage and update DB records';

    public function handle(): int
    {
        $employees = Employee::whereNotNull('avatar')->get();

        if ($employees->isEmpty()) {
            $this->warn('No employees with avatars found.');
            return 0;
        }

        $this->info("Found {$employees->count()} employees with avatars.");

        $bar = $this->output->createProgressBar($employees->count());
        $bar->start();

        $migrated = 0;
        $skipped  = 0;
        $errors   = 0;

        foreach ($employees as $employee) {
            $localPath = $employee->avatar; // e.g. "avatars/HNcQ...jpg"

            // Already a remote URL — skip
            if (Str::startsWith($localPath, 'http')) {
                $skipped++;
                $bar->advance();
                continue;
            }

            $localDisk = Storage::disk('public');
            $remoteDisk = Storage::disk('supabase');

            if (!$localDisk->exists($localPath)) {
                $this->newLine();
                $this->warn("  Local file missing: {$localPath} — skipping");
                $skipped++;
                $bar->advance();
                continue;
            }

            // Derive the filename (strip leading "avatars/")
            $filename = basename($localPath);
            $remotePath = $filename; // bucket root = "avatars", so just filename

            if ($this->option('dry-run')) {
                $this->newLine();
                $this->line("  [DRY-RUN] Would upload: {$localPath} → supabase/{$filename}");
                $migrated++;
                $bar->advance();
                continue;
            }

            try {
                $contents = $localDisk->get($localPath);
                $mimeType = $localDisk->mimeType($localPath) ?: 'image/jpeg';

                $remoteDisk->put($remotePath, $contents, [
                    'visibility'  => 'public',
                    'ContentType' => $mimeType,
                ]);

                // Build the public URL for this file
                $publicUrl = rtrim(config('filesystems.disks.supabase.url'), '/') . '/' . $filename;

                $employee->update(['avatar' => $publicUrl]);

                $migrated++;
            } catch (\Throwable $e) {
                $this->newLine();
                $this->error("  Failed for {$employee->name}: " . $e->getMessage());
                $errors++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Done! Migrated: {$migrated} | Skipped: {$skipped} | Errors: {$errors}");

        return $errors > 0 ? 1 : 0;
    }
}
