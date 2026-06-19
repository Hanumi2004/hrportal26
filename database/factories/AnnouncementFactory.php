<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Announcement>
 */
class AnnouncementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(6),
            'description' => $this->faker->paragraph(3),
            'category' => $this->faker->randomElement(['general', 'policy', 'system', 'other']),
            'created_by' => User::inRandomOrder()->value('id') ?? 1,
            'priority' => $this->faker->randomElement(['high', 'medium', 'low']),
            'expires_date' => $this->faker->optional()->dateTimeBetween('now', '+3 months'),
        ];
    }
}
