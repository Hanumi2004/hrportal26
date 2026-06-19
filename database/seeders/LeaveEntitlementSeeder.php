<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\LeaveEntitlement;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LeaveEntitlementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('leave_entitlements')->insert([
            ['name' => 'Annual Leave', 'full_entitlement' => 14],
            ['name' => 'Medical Leave', 'full_entitlement' => 14],
            ['name' => 'Emergency Leave', 'full_entitlement' => 3],
            ['name' => 'Hospitalization Leave', 'full_entitlement' => 60],
            ['name' => 'Maternity Leave', 'full_entitlement' => 90],
            ['name' => 'Compassionate Leave', 'full_entitlement' => 3],
            ['name' => 'Replacement Leave', 'full_entitlement' => 10],
            ['name' => 'Unpaid Leave', 'full_entitlement' => 90],
            ['name' => 'Marriage Leave', 'full_entitlement' => 7],
        ]);
    }
}
