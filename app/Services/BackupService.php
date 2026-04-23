<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class BackupService
{
    public function generateBackup()
    {
        $databaseName = config('database.connections.pgsql.database');
        $timestamp = now()->format('Y-m-d_H-i-s');
        $backupFileName = "PahamAja_Backup_{$timestamp}.zip";
        $sqlFileName = "database_dump_{$timestamp}.sql";
        $tempPath = storage_path('app/temp_backup');

        if (!file_exists($tempPath)) {
            mkdir($tempPath, 0755, true);
        }

        // 1. Generate SQL Dump (Custom Implementation for Postgres)
        $sqlContent = "-- PahamAja Database Backup\n";
        $sqlContent .= "-- Generated at: " . now()->toDateTimeString() . "\n\n";

        $tables = $this->getTables();

        foreach ($tables as $table) {
            $sqlContent .= $this->getTableDump($table);
        }

        file_put_contents("{$tempPath}/{$sqlFileName}", $sqlContent);

        // 2. Create ZIP
        $zipPath = storage_path("app/backups/{$backupFileName}");
        if (!file_exists(storage_path('app/backups'))) {
            mkdir(storage_path('app/backups'), 0755, true);
        }

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            // Add SQL
            $zip->addFile("{$tempPath}/{$sqlFileName}", $sqlFileName);

            // Add Avatars
            $avatarPath = storage_path('app/public/avatars');
            if (file_exists($avatarPath)) {
                $files = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($avatarPath),
                    \RecursiveIteratorIterator::LEAVES_ONLY
                );

                foreach ($files as $name => $file) {
                    if (!$file->isDir()) {
                        $filePath = $file->getRealPath();
                        $relativePath = 'avatars/' . substr($filePath, strlen($avatarPath) + 1);
                        $zip->addFile($filePath, $relativePath);
                    }
                }
            }

            $zip->close();
        }

        // Cleanup temp
        unlink("{$tempPath}/{$sqlFileName}");
        rmdir($tempPath);

        return [
            'path' => $zipPath,
            'name' => $backupFileName
        ];
    }

    private function getTables()
    {
        $tables = DB::select("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'");
        return array_map(fn($t) => $t->table_name, $tables);
    }

    private function getTableDump($table)
    {
        $output = "-- Table: {$table}\n";
        $output .= "TRUNCATE TABLE \"{$table}\" RESTART IDENTITY CASCADE;\n\n";

        $rows = DB::table($table)->get();

        foreach ($rows as $row) {
            $rowArray = (array) $row;
            $columns = array_keys($rowArray);
            $values = array_values($rowArray);

            $escapedValues = array_map(function ($value) {
                if (is_null($value)) return 'NULL';
                if (is_bool($value)) return $value ? 'true' : 'false';
                if (is_numeric($value)) return $value;
                return "'" . str_replace("'", "''", $value) . "'";
            }, $values);

            $columnNames = implode('", "', $columns);
            $valueList = implode(', ', $escapedValues);

            $output .= "INSERT INTO \"{$table}\" (\"{$columnNames}\") VALUES ({$valueList});\n";
        }

        $output .= "\n";
        return $output;
    }
}
