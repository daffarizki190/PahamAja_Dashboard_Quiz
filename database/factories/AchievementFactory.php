<?php

namespace Database\Factories;

use App\Models\Achievement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Achievement>
 */
class AchievementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->sentence(2),
            'description' => fake()->sentence(5),
            'icon' => fake()->randomElement(['🏆', '⭐', '🎯', '🚀', '💎', '🔥']),
            'condition' => fake()->randomElement(['quiz_completed', 'perfect_score', 'streak_5', 'first_attempt']),
            'threshold' => fake()->numberBetween(1, 10),
        ];
    }
}
