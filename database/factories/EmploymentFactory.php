<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Employee;
use App\Models\Department;
use App\Models\EmploymentType;
use App\Models\EmploymentStatus;
use App\Models\CompanyBranch;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employment>
 */
class EmploymentFactory extends Factory
{   
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $statusId = EmploymentStatus::inRandomOrder()->first()?->id ?? 1;
        $statusName = EmploymentStatus::find($statusId)?->name ?? 'Active';

        return [
            'employee_id'            => Employee::inRandomOrder()->value('employee_id'),
            'department_id'          => Department::inRandomOrder()->first()?->id ?? 1,
            'employment_type_id'     => EmploymentType::inRandomOrder()->first()?->id ?? 1,
            'employment_status_id'   => $statusId,
            'company_branch_id'      => CompanyBranch::inRandomOrder()->first()?->id ?? 1,
            'report_to'              => null, // Will set this in seeder to avoid circular dependency
            'position'               => fake()->jobTitle(),

            'date_of_employment'     => fake()->dateTimeBetween('-5 years', 'now'),
            'probation_start'        => stripos($statusName, 'probation') !== false ? fake()->dateTimeBetween('-2 months', 'now') : null,
            'probation_end'          => stripos($statusName, 'probation') !== false ? fake()->dateTimeBetween('now', '+2 months') : null,
            'suspension_start'       => stripos($statusName, 'suspend') !== false ? fake()->dateTimeBetween('-1 month', 'now') : null,
            'suspension_end'         => stripos($statusName, 'suspend') !== false ? fake()->dateTimeBetween('now', '+1 month') : null,
            'resignation_date'       => stripos($statusName, 'resign') !== false ? fake()->dateTimeBetween('-6 months', 'now') : null,
            'last_working_day'       => (stripos($statusName, 'resign') !== false || stripos($statusName, 'terminat') !== false) ? fake()->dateTimeBetween('-6 months', 'now') : null,
            'termination_date'       => stripos($statusName, 'terminat') !== false ? fake()->dateTimeBetween('-6 months', 'now') : null,
            'work_start_time'        => fake()->randomElement(['08:30', '09:00']),
            'work_end_time'          => fake()->randomElement(['17:30', '18:00']),
        ];
    }
}
