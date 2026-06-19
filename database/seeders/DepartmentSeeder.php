<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Department;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('departments')->insert([
            ['id' => 1, 'name' => 'Human Resources'],
            ['id' => 2, 'name' => 'Information Technology'],
            ['id' => 3, 'name' => 'Finance'],
            ['id' => 4, 'name' => 'Marketing'],
            ['id' => 5, 'name' => 'Operations'],
            ['id' => 6, 'name' => 'Others'],
        ]);
    }
}
