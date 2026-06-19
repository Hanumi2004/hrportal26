<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Employment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmploymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create employment records for existing employees
        $employees = Employee::all();
        
        foreach ($employees as $employee) {
            Employment::factory()->create([
                'employee_id' => $employee->employee_id,
                'report_to' => $employees->random()->employee_id, 
            ]);
        }

        // // Get or create employees
        // $employees = Employee::all();
        
        // if ($employees->isEmpty()) {
        //     $employees = Employee::factory()->count(10)->create();
        // }

        // // Create employment for each employee
        // foreach ($employees as $employee) {
        //     Employment::factory()->create([
        //         'employee_id' => $employee->employee_id, // Use employee_id, not id
        //     ]);
        // }

        // // Set up reporting structure
        // $allEmployments = Employment::all();
        // $managers = $allEmployments->take(3);
        
        // // Set managers (no report_to)
        // foreach ($managers as $manager) {
        //     $manager->update(['report_to' => null]);
        // }

        // // Assign team members to managers
        // $teamMembers = $allEmployments->slice(3);
        // foreach ($teamMembers as $member) {
        //     $member->update([
        //         'report_to' => $managers->random()->employee_id // Use employee_id
        //     ]);
        // }
    }
}
