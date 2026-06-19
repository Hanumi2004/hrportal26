<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Project;
use App\Models\Employee;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_name'      => fake()->sentence(4),
            'project_desc'      => fake()->optional()->paragraph(),
            'created_by'        => Employee::inRandomOrder()->value('employee_id'),
            'start_date'        => fake()->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
            'end_date'          => fake()->dateTimeBetween('now', '+2 months')->format('Y-m-d'),
            'project_status'    => fake()->randomElement(['not-started', 'in-progress', 'on-hold', 'completed']),
        ];
    }
}
