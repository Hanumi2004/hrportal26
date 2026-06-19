<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LeaveEntitlement>
 */
class LeaveEntitlementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Annual Leave', 'Medical Leave', 'Emergency Leave', 'Hospitalization Leave', 'Maternity Leave', 'Compassionate Leave', 'Replacement Leave', 'Unpaid Leave', 'Marriage Leave']),
            'full_entitlement' => fake()->numberBetween(3, 90),
        ];
    }
}
