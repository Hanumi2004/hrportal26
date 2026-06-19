<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id'    => Project::inRandomOrder()->value('id') ?? Project::factory(),
            'created_by'    => User::inRandomOrder()->first()->id,
            'task_name'     => fake()->sentence(4),
            'task_desc'     => fake()->optional()->paragraph(),
            'task_status'   => fake()->randomElement(['to-do', 'in-progress', 'in-review', 'to-review', 'completed']),
            'notes'         => fake()->optional()->sentence(),
            'due_date'      => fake()->optional()->dateTimeBetween('now', '+2 months')?->format('Y-m-d'),
        ];
    }
}
