<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->groupBy('group');
        
        // Cari backup terakhir
        $latestBackup = null;
        $backupPath = storage_path('app/backups');
        if (file_exists($backupPath)) {
            $files = glob($backupPath . '/*.zip');
            if (!empty($files)) {
                usort($files, function($a, $b) {
                    return filemtime($b) - filemtime($a);
                });
                $latestBackup = \Carbon\Carbon::createFromTimestamp(filemtime($files[0]))->format('d M Y, H:i');
            }
        }

        return view('admin.settings.index', compact('settings', 'latestBackup'));
    }

    public function update(Request $request)
    {
        $data = $request->except('_token');
        
        // Basic validation
        $rules = [
            'gemini_api_key' => 'nullable|string',
            'gemini_model' => 'nullable|string',
            'default_passing_score' => 'nullable|integer|min:0|max:100',
        ];

        $request->validate($rules);
        
        foreach ($data as $key => $value) {
            // Determine group based on key prefix
            $group = 'system';
            if (str_contains($key, 'ai_') || str_contains($key, 'gemini_')) {
                $group = 'ai';
            }
            
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'group' => $group]
            );
        }

        return back()->with('success', 'Pengaturan berhasil diperbarui!');
    }
}

