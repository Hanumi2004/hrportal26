<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EmploymentStatus;

class EmploymentStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        EmploymentStatus::insert([
            ['name' => 'Active'],
            ['name' => 'Probation'],
            ['name' => 'Suspended'],
            ['name' => 'Resigned'],
            ['name' => 'Terminated'],
        ]);
    }
}
