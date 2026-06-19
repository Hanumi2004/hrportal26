<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Form;
use App\Models\Employee;
use App\Models\Leave;
use App\Models\Attendance;

class FormApproverController extends Controller
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
        $adminEmployeeId = optional(Auth::user()->employee)->employee_id;

        foreach ($request->approvers as $approver) {
            // ⛔ skip empty optional approvers
            if (empty($approver['id'])) {
                continue;
            }

            // ❌ employee approving themselves
            if ($approver['id'] === $employee->employee_id) {
                abort(422, 'Employee cannot approve themselves.');
            }

            // ❌ admin cannot be manually assigned
            if ($approver['id'] === $adminEmployeeId) {
                abort(422, 'Admin approver is implicit and cannot be assigned.');
            }
        }

        $employee->formApprovers()->sync([]);

        foreach ($request->approvers as $approver) {
            if (empty($approver['id'])) {
                continue;
            }

            $employee->formApprovers()->attach(
                $approver['id'],
                ['level' => $approver['level']]
            );
        }

        return redirect()->back()->with('success', 'Approvers assigned successfully');
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
