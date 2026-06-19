<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;

class RequestApproverController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(Request $request, Employee $employee)
{
    $user = $request->user();
    $adminEmployeeId = $user->employee?->employee_id;
    
    // Dapatkan department employee
	$employeeDeptId = $employee->employment?->department_id;

	foreach ($request->approvers as $approver) {
		if (empty($approver['id'])) {
			continue;
		}

		// Check bukan diri sendiri
		if ($approver['id'] === $employee->employee_id) {
			abort(422, 'Employee cannot be their own approver.');
		}

		// Check bukan admin
		if ($approver['id'] === $adminEmployeeId) {
			abort(422, 'Admin approver is implicit and cannot be assigned.');
		}

		// VALIDATION BARU: Pastikan approver dari department yang sama
		if ($employeeDeptId) {
			$approverEmployee = Employee::find($approver['id']);
			$approverDeptId = $approverEmployee?->employment?->department_id;

			if ($approverDeptId !== $employeeDeptId) {
				abort(422, 'Approver must be from the same department as the employee.');
			}
		}
	}

    $employee->approvers()->sync([]);

    foreach ($request->approvers as $approver) {
        if (empty($approver['id'])) {
            continue;
        }
        $employee->approvers()->attach(
            $approver['id'],
            ['level' => $approver['level']]
        );
    }

    return redirect()->route('profile.show', $employee->employee_id)
        ->with('success', 'Approvers assigned successfully');
}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
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
