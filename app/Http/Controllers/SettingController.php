<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\LeaveEntitlement;
use App\Models\EventCategory;
use App\Models\EmploymentType;
use App\Models\EmploymentStatus;
use App\Models\CompanyBranch;
use App\Models\Department;
use App\Models\Setting;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.admin-setting', [
            'settings'           => Setting::all()->pluck('value', 'key'),
            'leaveEntitlements'  => LeaveEntitlement::orderBy('name')->get(),
            'eventCategories'    => EventCategory::orderBy('name')->get(),
            'employmentTypes'    => EmploymentType::orderBy('name')->get(),
            'employmentStatuses' => EmploymentStatus::orderBy('name')->get(),
            'companyBranches'    => CompanyBranch::orderBy('name')->get(),
            'departments'        => Department::orderBy('name')->get(),
        ]);
    }


    /**
     * Update numeric / simple system settings
     */
    public function updateGeneral(Request $request)
    {
        $data = $request->validate([
            'max_timeslip_hours' => 'required|integer|min:1|max:24',
        ]);

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return back()->with('success', 'System settings updated.');
    }

    /**
     * Leave entitlement CRUD (inline)
     */
    public function updateLeaveEntitlements(Request $request)
    {
        $data = $request->validate([
            'entitlements'               => 'array',
            'entitlements.*.name'        => 'required|string|max:50',
            'entitlements.*.days'        => 'required|numeric|min:0|max:365',
        ]);

        DB::transaction(function () use ($data) {
            LeaveEntitlement::truncate();

            foreach ($data['entitlements'] as $row) {
                LeaveEntitlement::create([
                    'name'              => $row['name'],
                    'full_entitlement'  => $row['days'],
                ]);
            }
        });

        return back()->with('success', 'Leave entitlements updated.');
    }

    /**
     * Generic master data update
     */
    public function updateMasterData(Request $request)
    {
        $maps = [
            'event_categories'    => EventCategory::class,
            'employment_types'    => EmploymentType::class,
            'employment_statuses' => EmploymentStatus::class,
            'company_branches'    => CompanyBranch::class,
            'departments'         => Department::class,
        ];

        foreach ($maps as $input => $model) {
            if (!$request->has($input)) continue;

            DB::transaction(function () use ($request, $input, $model) {
                $model::truncate();

                foreach ($request->input($input) as $value) {
                    if ($value !== null && trim($value) !== '') {
                        $model::create(['name' => trim($value)]);
                    }
                }
            });
        }

        return back()->with('success', 'Master data updated.');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
