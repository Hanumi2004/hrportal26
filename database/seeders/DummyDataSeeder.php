<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Employee;
use App\Models\Employment;
use App\Models\Department;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Departments
        $departments = [
            ['name' => 'IT Department'],
            ['name' => 'HR Department'],
            ['name' => 'Finance Department'],
            ['name' => 'Operations Department'],
        ];
        
        foreach ($departments as $dept) {
            Department::firstOrCreate(['name' => $dept['name']], $dept);
        }
        
        $itDept = Department::where('name', 'IT Department')->first();
        $hrDept = Department::where('name', 'HR Department')->first();
        $finDept = Department::where('name', 'Finance Department')->first();
        $opsDept = Department::where('name', 'Operations Department')->first();
        
        // 2. Create Users & Employees for each role
        
        // Super Admin (role_id=1) - IT Admin
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@dummy.test'],
            [
                'name' => 'Super Admin IT (DUMMY)',
                'password' => Hash::make('password'),
                'role_id' => 1,
                'profile_photo_path' => null,
            ]
        );
        
        $superAdminEmp = Employee::firstOrCreate(
            ['user_id' => $superAdmin->id],
            [
                'employee_id' => 'DUMMY001',
                'full_name' => 'Super Admin IT (DUMMY)',
                'email' => 'superadmin@dummy.test',
                'phone_number' => '0123456789',
                'ic_number' => '123456789012',
                'gender' => 'male',
                'birthday' => '1980-01-01',
                'marital_status' => 'married',
                'nationality' => 'Malaysian',
                'address' => '123 DUMMY IT Street, KL',
                'emergency_contact_name' => 'Emergency Contact',
                'emergency_contact_number' => '0198765432',
                'emergency_contact_relationship' => 'Spouse',
                'highest_education_level' => 'Bachelor Degree',
                'highest_education_institution' => 'University Malaya',
                'graduation_year' => 2002,
            ]
        );
        
        Employment::firstOrCreate(
            ['employee_id' => $superAdminEmp->employee_id],
            [
                'department_id' => $itDept->id,
                'employment_type_id' => 1,
                'employment_status_id' => 1,
                'company_branch_id' => 1,
                'report_to' => null,
                'position' => 'Super Admin (DUMMY)',
                'date_of_employment' => '2000-01-01',
                'work_start_time' => '09:00:00',
                'work_end_time' => '18:00:00',
            ]
        );
        
        // Admin HR (role_id=2)
        $adminHR = User::firstOrCreate(
            ['email' => 'adminhr@dummy.test'],
            [
                'name' => 'Admin HR (DUMMY)',
                'password' => Hash::make('password'),
                'role_id' => 2,
                'profile_photo_path' => null,
            ]
        );
        
        $adminHREmp = Employee::firstOrCreate(
            ['user_id' => $adminHR->id],
            [
                'employee_id' => 'DUMMY002',
                'full_name' => 'Admin HR (DUMMY)',
                'email' => 'adminhr@dummy.test',
                'phone_number' => '0134567890',
                'ic_number' => '234567890123',
                'gender' => 'female',
                'birthday' => '1985-05-15',
                'marital_status' => 'married',
                'nationality' => 'Malaysian',
                'address' => '456 DUMMY HR Street, KL',
                'emergency_contact_name' => 'HR Emergency',
                'emergency_contact_number' => '0198765433',
                'emergency_contact_relationship' => 'Sibling',
                'highest_education_level' => 'Master Degree',
                'highest_education_institution' => 'University Kebangsaan Malaysia',
                'graduation_year' => 2010,
            ]
        );
        
        Employment::firstOrCreate(
            ['employee_id' => $adminHREmp->employee_id],
            [
                'department_id' => $hrDept->id,
                'employment_type_id' => 1,
                'employment_status_id' => 1,
                'company_branch_id' => 1,
                'report_to' => null,
                'position' => 'HR Manager (DUMMY)',
                'date_of_employment' => '2005-01-01',
                'work_start_time' => '09:00:00',
                'work_end_time' => '18:00:00',
            ]
        );
        
        // President (role_id=7)
        $president = User::firstOrCreate(
            ['email' => 'president@dummy.test'],
            [
                'name' => 'President (DUMMY)',
                'password' => Hash::make('password'),
                'role_id' => 7,
                'profile_photo_path' => null,
            ]
        );
        
        $presidentEmp = Employee::firstOrCreate(
            ['user_id' => $president->id],
            [
                'employee_id' => 'DUMMY003',
                'full_name' => 'President (DUMMY)',
                'email' => 'president@dummy.test',
                'phone_number' => '0145678901',
                'ic_number' => '345678901234',
                'gender' => 'male',
                'birthday' => '1970-10-20',
                'marital_status' => 'married',
                'nationality' => 'Malaysian',
                'address' => '789 DUMMY President Villa, KL',
                'emergency_contact_name' => 'President Emergency',
                'emergency_contact_number' => '0198765434',
                'emergency_contact_relationship' => 'Spouse',
                'highest_education_level' => 'PhD',
                'highest_education_institution' => 'Oxford University',
                'graduation_year' => 1995,
            ]
        );
        
        Employment::firstOrCreate(
            ['employee_id' => $presidentEmp->employee_id],
            [
                'department_id' => $itDept->id,
                'employment_type_id' => 1,
                'employment_status_id' => 1,
                'company_branch_id' => 1,
                'report_to' => null,
                'position' => 'President (DUMMY)',
                'date_of_employment' => '1995-01-01',
                'work_start_time' => '09:00:00',
                'work_end_time' => '18:00:00',
            ]
        );
        
        // Executive Director (role_id=5)
        $director = User::firstOrCreate(
            ['email' => 'director@dummy.test'],
            [
                'name' => 'Executive Director (DUMMY)',
                'password' => Hash::make('password'),
                'role_id' => 5,
                'profile_photo_path' => null,
            ]
        );
        
        $directorEmp = Employee::firstOrCreate(
            ['user_id' => $director->id],
            [
                'employee_id' => 'DUMMY004',
                'full_name' => 'Executive Director (DUMMY)',
                'email' => 'director@dummy.test',
                'phone_number' => '0156789012',
                'ic_number' => '456789012345',
                'gender' => 'male',
                'birthday' => '1975-07-30',
                'marital_status' => 'married',
                'nationality' => 'Malaysian',
                'address' => '321 DUMMY Director House, KL',
                'emergency_contact_name' => 'Director Emergency',
                'emergency_contact_number' => '0198765435',
                'emergency_contact_relationship' => 'Spouse',
                'highest_education_level' => 'Master Degree',
                'highest_education_institution' => 'Harvard University',
                'graduation_year' => 2000,
            ]
        );
        
        Employment::firstOrCreate(
            ['employee_id' => $directorEmp->employee_id],
            [
                'department_id' => $finDept->id,
                'employment_type_id' => 1,
                'employment_status_id' => 1,
                'company_branch_id' => 1,
                'report_to' => null,
                'position' => 'Executive Director (DUMMY)',
                'date_of_employment' => '2000-01-01',
                'work_start_time' => '09:00:00',
                'work_end_time' => '18:00:00',
            ]
        );
        
        // Manager IT (role_id=4, dept=IT)
        $managerIT = User::firstOrCreate(
            ['email' => 'managerit@dummy.test'],
            [
                'name' => 'Manager IT (DUMMY)',
                'password' => Hash::make('password'),
                'role_id' => 4,
                'profile_photo_path' => null,
            ]
        );
        
        $managerITEmp = Employee::firstOrCreate(
            ['user_id' => $managerIT->id],
            [
                'employee_id' => 'DUMMY005',
                'full_name' => 'Manager IT (DUMMY)',
                'email' => 'managerit@dummy.test',
                'phone_number' => '0167890123',
                'ic_number' => '567890123456',
                'gender' => 'male',
                'birthday' => '1982-03-10',
                'marital_status' => 'married',
                'nationality' => 'Malaysian',
                'address' => '654 DUMMY IT Manager Street, KL',
                'emergency_contact_name' => 'Manager IT Emergency',
                'emergency_contact_number' => '0198765436',
                'emergency_contact_relationship' => 'Spouse',
                'highest_education_level' => 'Bachelor Degree',
                'highest_education_institution' => 'University Teknologi Malaysia',
                'graduation_year' => 2005,
            ]
        );
        
        Employment::firstOrCreate(
            ['employee_id' => $managerITEmp->employee_id],
            [
                'department_id' => $itDept->id,
                'employment_type_id' => 1,
                'employment_status_id' => 1,
                'company_branch_id' => 1,
                'report_to' => null,
                'position' => 'IT Manager (DUMMY)',
                'date_of_employment' => '2005-01-01',
                'work_start_time' => '09:00:00',
                'work_end_time' => '18:00:00',
            ]
        );
        
        // Manager HR (role_id=4, dept=HR)
        $managerHR = User::firstOrCreate(
            ['email' => 'managerhr@dummy.test'],
            [
                'name' => 'Manager HR (DUMMY)',
                'password' => Hash::make('password'),
                'role_id' => 4,
                'profile_photo_path' => null,
            ]
        );
        
        $managerHREmp = Employee::firstOrCreate(
            ['user_id' => $managerHR->id],
            [
                'employee_id' => 'DUMMY006',
                'full_name' => 'Manager HR (DUMMY)',
                'email' => 'managerhr@dummy.test',
                'phone_number' => '0178901234',
                'ic_number' => '678901234567',
                'gender' => 'female',
                'birthday' => '1983-08-25',
                'marital_status' => 'married',
                'nationality' => 'Malaysian',
                'address' => '987 DUMMY HR Manager Street, KL',
                'emergency_contact_name' => 'Manager HR Emergency',
                'emergency_contact_number' => '0198765437',
                'emergency_contact_relationship' => 'Sibling',
                'highest_education_level' => 'Bachelor Degree',
                'highest_education_institution' => 'University Putra Malaysia',
                'graduation_year' => 2006,
            ]
        );
        
        Employment::firstOrCreate(
            ['employee_id' => $managerHREmp->employee_id],
            [
                'department_id' => $hrDept->id,
                'employment_type_id' => 1,
                'employment_status_id' => 1,
                'company_branch_id' => 1,
                'report_to' => null,
                'position' => 'HR Manager (DUMMY)',
                'date_of_employment' => '2006-01-01',
                'work_start_time' => '09:00:00',
                'work_end_time' => '18:00:00',
            ]
        );
        
        // Staff IT (role_id=3, dept=IT, report_to Manager IT)
        $staffIT = User::firstOrCreate(
            ['email' => 'staffit@dummy.test'],
            [
                'name' => 'Staff IT (DUMMY)',
                'password' => Hash::make('password'),
                'role_id' => 3,
                'profile_photo_path' => null,
            ]
        );
        
        $staffITEmp = Employee::firstOrCreate(
            ['user_id' => $staffIT->id],
            [
                'employee_id' => 'DUMMY007',
                'full_name' => 'Staff IT (DUMMY)',
                'email' => 'staffit@dummy.test',
                'phone_number' => '0189012345',
                'ic_number' => '789012345678',
                'gender' => 'male',
                'birthday' => '1990-12-05',
                'marital_status' => 'single',
                'nationality' => 'Malaysian',
                'address' => '321 DUMMY Staff IT Street, KL',
                'emergency_contact_name' => 'Staff IT Emergency',
                'emergency_contact_number' => '0198765438',
                'emergency_contact_relationship' => 'Parent',
                'highest_education_level' => 'Bachelor Degree',
                'highest_education_institution' => 'University Malaya',
                'graduation_year' => 2012,
            ]
        );
        
        Employment::firstOrCreate(
            ['employee_id' => $staffITEmp->employee_id],
            [
                'department_id' => $itDept->id,
                'employment_type_id' => 1,
                'employment_status_id' => 1,
                'company_branch_id' => 1,
                'report_to' => $managerITEmp->employee_id, // Report to Manager IT
                'position' => 'Programmer (DUMMY)',
                'date_of_employment' => '2015-01-01',
                'work_start_time' => '09:00:00',
                'work_end_time' => '18:00:00',
            ]
        );
        
        // Staff HR (role_id=3, dept=HR, report_to Manager HR)
        $staffHR = User::firstOrCreate(
            ['email' => 'staffhr@dummy.test'],
            [
                'name' => 'Staff HR (DUMMY)',
                'password' => Hash::make('password'),
                'role_id' => 3,
                'profile_photo_path' => null,
            ]
        );
        
        $staffHREmp = Employee::firstOrCreate(
            ['user_id' => $staffHR->id],
            [
                'employee_id' => 'DUMMY008',
                'full_name' => 'Staff HR (DUMMY)',
                'email' => 'staffhr@dummy.test',
                'phone_number' => '0190123456',
                'ic_number' => '890123456789',
                'gender' => 'female',
                'birthday' => '1992-04-18',
                'marital_status' => 'single',
                'nationality' => 'Malaysian',
                'address' => '654 DUMMY Staff HR Street, KL',
                'emergency_contact_name' => 'Staff HR Emergency',
                'emergency_contact_number' => '0198765439',
                'emergency_contact_relationship' => 'Parent',
                'highest_education_level' => 'Bachelor Degree',
                'highest_education_institution' => 'University Kebangsaan Malaysia',
                'graduation_year' => 2014,
            ]
        );
        
        Employment::firstOrCreate(
            ['employee_id' => $staffHREmp->employee_id],
            [
                'department_id' => $hrDept->id,
                'employment_type_id' => 1,
                'employment_status_id' => 1,
                'company_branch_id' => 1,
                'report_to' => $managerHREmp->employee_id, // Report to Manager HR
                'position' => 'HR Executive (DUMMY)',
                'date_of_employment' => '2016-01-01',
                'work_start_time' => '09:00:00',
                'work_end_time' => '18:00:00',
            ]
        );
        
        // Others (role_id=6)
        $others = User::firstOrCreate(
            ['email' => 'others@dummy.test'],
            [
                'name' => 'Others Staff (DUMMY)',
                'password' => Hash::make('password'),
                'role_id' => 6,
                'profile_photo_path' => null,
            ]
        );
        
        $othersEmp = Employee::firstOrCreate(
            ['user_id' => $others->id],
            [
                'employee_id' => 'DUMMY009',
                'full_name' => 'Others Staff (DUMMY)',
                'email' => 'others@dummy.test',
                'phone_number' => '0201234567',
                'ic_number' => '901234567890',
                'gender' => 'male',
                'birthday' => '1995-09-22',
                'marital_status' => 'single',
                'nationality' => 'Malaysian',
                'address' => '987 DUMMY Others Street, KL',
                'emergency_contact_name' => 'Others Emergency',
                'emergency_contact_number' => '0198765440',
                'emergency_contact_relationship' => 'Parent',
                'highest_education_level' => 'Diploma',
                'highest_education_institution' => 'Politeknik Malaysia',
                'graduation_year' => 2016,
            ]
        );
        
        Employment::firstOrCreate(
            ['employee_id' => $othersEmp->employee_id],
            [
                'department_id' => $opsDept->id,
                'employment_type_id' => 2, // Assume 2=Contract
                'employment_status_id' => 1,
                'company_branch_id' => 1,
                'report_to' => null,
                'position' => 'Contractor (DUMMY)',
                'date_of_employment' => '2020-01-01',
                'contract_start' => '2020-01-01',
                'contract_end' => '2025-12-31',
                'work_start_time' => '09:00:00',
                'work_end_time' => '18:00:00',
            ]
        );
        
        $this->command->info('Dummy data created successfully with .test domain!');
    }
}