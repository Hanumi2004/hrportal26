<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Employee;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'employee_id'       => strtoupper(fake()->unique()->bothify('EMP###')),
            'full_name'         => fake()->name(),
            'email'             => fake()->unique()->safeEmail(),
            'phone_number'      => fake()->unique()->phoneNumber(),
            'address'           => fake()->address(),
            'ic_number'         => fake()->unique()->numerify('############'),
            'marital_status'    => fake()->randomElement(['Single', 'Married']),
            'gender'            => fake()->randomElement(['Male', 'Female']),
            'birthday'          => fake()->dateTimeBetween('-50 years', '-20 years'),
            'nationality'       => fake()->country(),
            'emergency_contact_name' => fake()->name(),
            'emergency_contact_number' => fake()->phoneNumber(),
            'emergency_contact_relationship' => fake()->randomElement(['Parent', 'Sibling', 'Spouse', 'Friend']),
            'highest_education_level' => fake()->randomElement(['High School', 'Diploma', 'Bachelor', 'Master', 'PhD']),
            'highest_education_institution' => fake()->company(),
            'graduation_year'   => fake()->year(),
        ];
    }

    public function configure()
    {
        return $this->afterMaking(function (Employee $employee) {
            if ($employee->user) {
                $employee->full_name = $employee->user->name;
            }
        });
    }
}
