<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class, // Seed fixed roles first
            EventCategorySeeder::class,
            EmploymentTypeSeeder::class,
            EmploymentStatusSeeder::class,
            CompanyBranchSeeder::class,
            DepartmentSeeder::class,
            LeaveEntitlementSeeder::class,
            AdminSeeder::class,
            UserSeeder::class,
            EmployeeSeeder::class,
            EmploymentSeeder::class,
            ProjectSeeder::class,
            TaskSeeder::class,
            AttendanceSeeder::class,
            EventSeeder::class,
            LeaveSeeder::class,
            AnnouncementSeeder::class,
            SettingSeeder::class,
        ]);
    }
}
