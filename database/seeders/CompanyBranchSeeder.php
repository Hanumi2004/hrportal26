<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CompanyBranch;

class CompanyBranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CompanyBranch::insert([
            ['name' => 'AHG'],
            ['name' => 'D-8CEFC'],
        ]);
    }
}
