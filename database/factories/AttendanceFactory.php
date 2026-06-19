<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attendance>
 */
class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $date = fake()->dateTimeBetween('-1 month', 'now');

        $timeIn = Carbon::instance($date)->setTime(fake()->numberBetween(7, 10), fake()->numberBetween(0, 59));

        $workStart = fake()->randomElement(['8:30', '9:00']);

        $statusTimeIn = $timeIn->greaterThan(Carbon::instance($date)->setTime(...explode(':', $workStart))) ? 'Late' : 'On Time';

        // 70% chance employee punched out
        $timeOut = fake()->boolean(70)
            ? Carbon::instance($date)->setTime(fake()->numberBetween(16, 19), fake()->numberBetween(0, 59))
            : null;

        $workEnd = fake()->randomElement(['17:30', '18:00']);

        $statusTimeOut = $timeOut
            ? ($timeOut->greaterThan(Carbon::instance($date)->setTime(...explode(':', $workEnd))) ? 'On Time' : 'Early Leave')
            : null;

        // 30% chance employee has a time slip
        $hasTimeSlip = fake()->boolean(30);
        $timeSlipStart = $hasTimeSlip ? Carbon::instance($date)->setTime(fake()->numberBetween(8, 17), fake()->numberBetween(0, 59)) : null;
        $timeSlipEnd = $hasTimeSlip ? Carbon::instance($date)->setTime(fake()->numberBetween(8, 17), fake()->numberBetween(0, 59)) : null;

        return [
            // Randomly decide if the employee is present, absent, or on leave
            'employee_id'        => Employee::inRandomOrder()->value('employee_id'),
            'date'               => $timeIn->toDateString(),

            'time_in'            => $timeIn->toTimeString(),
            'time_in_lat'       => fake()->latitude(3.10, 3.30),
            'time_in_lng'       => fake()->longitude(101.60, 101.80),
            'location_in'       => fake()->randomElement(['On-site', 'Off-site']),

            'time_out'           => $timeOut?->toTimeString(),
            'time_out_lat'      => $timeOut ? fake()->latitude(3.10, 3.30) : null,
            'time_out_lng'      => $timeOut ? fake()->longitude(101.60, 101.80) : null,
            'location_out'      => $timeOut ? fake()->randomElement(['On-site', 'Off-site']) : null,

            'status_time_in'     => $statusTimeIn,
            'status_time_out'    => $statusTimeOut,

            'late_reason'        => $statusTimeIn === 'Late' ? fake()->sentence() : null,
            'early_leave_reason' => $statusTimeOut === 'Early Leave' ? fake()->sentence() : null,

            'time_slip_start'    => $timeSlipStart?->toTimeString(),
            'time_slip_end'      => $timeSlipEnd?->toTimeString(),
            'time_slip_reason'   => $hasTimeSlip ? fake()->sentence() : null,
            'time_slip_status'   => $hasTimeSlip ? fake()->randomElement(['pending', 'approved', 'rejected']) : null,
        ];
    }
}
