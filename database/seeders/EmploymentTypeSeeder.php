<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EmploymentType;

class EmploymentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        EmploymentType::insert([
            ['name' => 'Full Time'],
            ['name' => 'Part Time'],
            ['name' => 'Contract'],
            ['name' => 'Intern'],
        ]);
    }
}
