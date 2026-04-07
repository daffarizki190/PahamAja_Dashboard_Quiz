<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AchievementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Achievement::updateOrCreate(
            ['name' => 'First Quiz'],
            [
                'description' => 'Completed your first quiz',
                'icon' => 'star',
                'condition' => 'quizzes_completed',
                'threshold' => 1,
            ]
        );

        \App\Models\Achievement::updateOrCreate(
            ['name' => 'Perfect Score'],
            [
                'description' => 'Scored 100% on a quiz',
                'icon' => 'trophy',
                'condition' => 'perfect_score',
                'threshold' => 1,
            ]
        );

        \App\Models\Achievement::updateOrCreate(
            ['name' => 'Quiz Master'],
            [
                'description' => 'Completed 5 quizzes',
                'icon' => 'crown',
                'condition' => 'quizzes_completed',
                'threshold' => 5,
            ]
        );

        \App\Models\Achievement::updateOrCreate(
            ['name' => 'Consistent Learner'],
            [
                'description' => 'Scored above 80% on 3 quizzes',
                'icon' => 'medal',
                'condition' => 'high_scores',
                'threshold' => 3,
            ]
        );
    }
}
