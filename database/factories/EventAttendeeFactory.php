<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Event;
use App\Models\Employee;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EventAttendee>
 */
class EventAttendeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $responseStatus = $this->faker->randomElement([
            'pending',
            'confirmed',
            'declined',
        ]);

        return [
            'event_id' => Event::query()->inRandomOrder()->value('id')
                ?? Event::factory(),

            'employee_id' => Employee::query()->inRandomOrder()->value('employee_id')
                ?? Employee::factory(),

            'department_id' => Employee::query()
                ->whereNotNull('department_id') 
                ->inRandomOrder()
                ->value('department_id')
                ?? null,

            'response_status' => $responseStatus,

            'decline_reason' => $responseStatus === 'declined'
                ? $this->faker->sentence()
                : null,

            'responded_at' => in_array($responseStatus, ['confirmed', 'declined'])
                ? $this->faker->dateTimeBetween('-1 week', 'now')
                : null,

            'attendance_status' => $this->faker->randomElement([
                'pending',
                'attended',
                'absent',
            ]),

            'notes' => $this->faker->boolean(30)
                ? $this->faker->sentence()
                : null,
        ];
    }
}
