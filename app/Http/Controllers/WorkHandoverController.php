<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Form;
use App\Models\WorkHandover;
use App\Models\FormApprover;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WorkHandoverController extends Controller
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
        $user = Auth::user();
        $employee = $user->employee;

        // Employees list for "handover to"
        $employees = Employee::with('employment.department')
            ->where('employee_id', '!=', $employee->employee_id) // cannot handover to self
            ->whereHas('employment.status', fn($q) => $q->where('name', 'active'))
            ->orderBy('full_name')
            ->get();

        return view('form.work-handover-form', compact('employees'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $employee = $user->employee;

        $request->validate([
            'handover_reason' => 'required|string',
            'handover_to' => 'required|exists:employees,employee_id',
            'last_working_day' => 'required|date',
            'handover_notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $employee) {

            // 1. Create MASTER FORM
            $form = Form::create([
                'form_type' => 'work_handover',
                'employee_id' => $employee->employee_id,
                'form_status' => 'pending',
                'approval_level' => 0,
            ]);

            // 2. Create DETAIL FORM
            WorkHandover::create([
                'form_id' => $form->id,
                'last_working_day' => $request->last_working_day,
                'handover_to' => $request->handover_to,
                'handover_reason' => $request->handover_reason,
                'handover_notes' => $request->handover_notes,
                'tasks' => array_values(array_filter($request->tasks ?? [])),
                'documents' => array_values(array_filter($request->documents ?? [])),
                'electronic_files' => array_values(array_filter($request->efiles ?? [])),
                'passwords' => array_values(array_filter($request->passwords ?? [])),
                'financial_commitments' => array_values(array_filter($request->commitments ?? [])),
                'inventory' => array_values(array_filter($request->inventory ?? [])),

            ]);

            // 3. FormApprovers will be created ONLY when admin explicitly assigns them via the modal
            // This allows the form to be admin-only by default unless approvers are assigned
        });

        return redirect()->route('form.myforms')->with('success', 'Form submitted successfully');
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
