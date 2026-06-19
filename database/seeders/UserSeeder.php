<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Employee;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 30 random users with employee profiles in one go
        // Make sure in UserFactory already defines ->has(Employee::factory())
        User::factory()
            ->has(Employee::factory()) // // relies on hasOne relationship in User model
            ->count(30)
            ->create([
                'role_id' => 3, // Employee
            ]);
    }
}
