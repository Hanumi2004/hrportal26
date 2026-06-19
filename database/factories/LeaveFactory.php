<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Leave;
use App\Models\Employee;
use App\Models\LeaveEntitlement;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Leave>
 */
class LeaveFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'employee_id'        => Employee::inRandomOrder()->first()->employee_id,
            'leave_entitlement_id' => LeaveEntitlement::inRandomOrder()->first()?->id ?? 1,
            'leave_length'       => fake()->randomElement(['full_day', 'AM', 'PM']),
            'leave_reason'       => fake()->optional()->sentence(),
            'start_date'         => $start = fake()->dateTimeBetween('now', '+1 month')->format('Y-m-d'),
            'end_date'           => fake()->dateTimeBetween($start, '+1 month')->format('Y-m-d'),
            'days'               => fake()->numberBetween(1, 10),
            'attachment'         => null,
            'approved_by'        => fake()->optional()->randomElement(Employee::pluck('employee_id')->toArray()),
            'approval_level'     => fake()->numberBetween(0, 3),
            'approved_at'        => fake()->optional()->dateTimeBetween('now', '+1 month'),
            'leave_status'       => fake()->randomElement(['pending', 'approved', 'rejected']),
            'reject_reason'      => fake()->optional()->sentence(),
        ];
    }
}
