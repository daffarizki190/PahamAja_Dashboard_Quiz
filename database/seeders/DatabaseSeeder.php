<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database (SAFE CONSOLIDATED MODE).
     */
    public function run(): void
    {
        // 1. Run Master Data & Professional Restoration Seeders
        // All Quiz logic is now consolidated in ProfessionalQuizSeeder to avoid conflicts.
        $this->call([
            SettingSeeder::class,
            EmployeeSeeder::class,
            AchievementSeeder::class,
            ProfessionalQuizSeeder::class, // Manages Standar Pelayanan, Incident, and Complaint
            HistoryRestorationSeeder::class, // Restores exact historical scores
        ]);

        // DELETED all local quiz logic and random loops for absolute safety and data integrity.
    }
}
