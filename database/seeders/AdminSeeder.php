<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create the admin user
        User::firstOrCreate(
            ['email' => 'tehaiium@gmail.com'], // email of admin
            [
                'name'     => 'System Administrator',
                'password' => Hash::make('password'), // default password
                'role_id'  => 2, 
            ]
        );
    }
}
