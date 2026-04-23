<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // AI Configuration
            [
                'key' => 'gemini_api_key',
                'value' => env('GEMINI_API_KEY'),
                'group' => 'ai',
            ],
            [
                'key' => 'gemini_model',
                'value' => env('GEMINI_MODEL', 'gemini-1.5-flash'),
                'group' => 'ai',
            ],
            
            // System Configuration
            [
                'key' => 'default_passing_score',
                'value' => '70',
                'group' => 'system',
            ],
            [
                'key' => 'platform_name',
                'value' => 'PahamAja Dashboard',
                'group' => 'system',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
