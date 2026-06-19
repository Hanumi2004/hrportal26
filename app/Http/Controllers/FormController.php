<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Form;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;
use App\Models\FormApprover;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\F;

class FormController extends Controller
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Form $form)
    {
        $workHandover = $form->workHandover;

        return view('form.work-handover-show', compact('form', 'workHandover'));

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

    // to show forms for approval (for approvers)
    public function forms(Request $request)
    {
        $user = Auth::user();
        $employee = $user->employee;
        $employeeId = optional($employee)->employee_id;
        $viewType = 'approver';
        $filterRoute = route('form.employee');

        $formTypes = Form::select('form_type')->distinct()->pluck('form_type');

        // --- Form Requests with filters ---
        $formsQuery = Form::with('employee')
            ->where('form_status', 'pending')
            ->whereHas('employee.formApprovers', function ($q) use ($employeeId) {
                $q->where('approver_id', $employeeId)
                    ->whereColumn('form_approvers.level', 'forms.approval_level');
            })
            ->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $formsQuery->whereHas('employee', function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('employee_id', 'like', "%{$search}%");
            });
        }

        if ($request->filled('form_type')) {
            $formsQuery->where('form_type', $request->form_type);
        }

        if ($request->filled('created_at')) {
            $formsQuery->whereDate('created_at', $request->created_at);
        }

        $pendingForms = $formsQuery->get();
        $allEmployees = collect();

        return view('form.form-dashboard', compact(
            'viewType',
            'filterRoute',
            'formTypes',
            'pendingForms',
            'allEmployees'
        ));
    }

    // to show my submitted forms (for employees)
    public function myForms(Request $request)
    {
        $user = Auth::user();
        $employee = $user->employee;
        $viewType = 'employee';
        $filterRoute = route('form.myforms');

        $formTypes = Form::select('form_type')->distinct()->pluck('form_type');

        // --- Form Requests with filters ---
        $formsQuery = Form::with('employee')
            ->where('employee_id', $employee->employee_id)
            ->where('form_status', 'pending')
            ->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $formsQuery->whereHas('employee', function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('employee_id', 'like', "%{$search}%");
            });
        }

        if ($request->filled('form_type')) {
            $formsQuery->where('form_type', $request->form_type);
        }

        if ($request->filled('created_at')) {
            $formsQuery->whereDate('created_at', $request->created_at);
        }

        $pendingForms = $formsQuery->get();
        $allEmployees = collect();

        return view('form.form-dashboard', compact(
            'viewType',
            'filterRoute',
            'formTypes',
            'pendingForms',
            'allEmployees'
        ));
    }

    // to show all forms for approval (for admin)
    public function adminForms(Request $request)
    {
        $viewType = 'admin';
        $filterRoute = route('form.admin');

        $formTypes = Form::select('form_type')->distinct()->pluck('form_type');

        // --- Form Requests with filters ---
        $formsQuery = Form::with('employee')
            ->where('form_status', 'pending')
            ->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $formsQuery->whereHas('employee', function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('employee_id', 'like', "%{$search}%");
            });
        }

        if ($request->filled('form_type')) {
            $formsQuery->where('form_type', $request->form_type);
        }

        if ($request->filled('created_at')) {
            $formsQuery->whereDate('created_at', $request->created_at);
        }

        $pendingForms = $formsQuery->get();
        $allEmployees = Employee::select('employee_id', 'full_name')->orderBy('full_name')->get();

        return view('form.form-dashboard', compact(
            'viewType',
            'filterRoute',
            'pendingForms',
            'formTypes',
            'allEmployees'
        ));
    }

    public function approveForms(Request $request, Form $form)
    {
        $request->validate([
            'action' => 'required|in:approved,rejected'
        ]);

        $user = Auth::user();
        $employee = $user->employee;
        $approvedBy = optional($employee)->employee_id;

        /**
         * STEP 1: ADMIN APPROVAL (LEVEL 0)
         */
        if ($form->approval_level === 0) {

            abort_if($user->role_id !== 2, 403);

            // if rejected, then done
            if ($request->action === 'rejected') {
                $form->update([
                    'form_status' => 'rejected',
                    'approved_by' => $approvedBy,
                    'approved_at' => now(),
                ]);
                return back()->with('error', 'Form rejected by admin');
            }

            // Check if there are any assigned approvers
            $hasApprovers = FormApprover::where('form_id', $form->id)->exists();

            if (!$hasApprovers) {
                // No approvers assigned → approve directly
                $form->update([
                    'form_status' => 'approved',
                    'approved_by' => $approvedBy,
                    'approved_at' => now(),
                ]);
                return back()->with('success', 'Form approved.');
            }

            // Has approvers → move to next level
            $form->increment('approval_level');

            return back()->with('success', 'Approved by admin. Assigned to next approver.');
        }

        /**
         * STEP 2: NORMAL APPROVER FLOW
         */

        $currentApprover = FormApprover::where('form_id', $form->id)
            ->where('level', $form->approval_level)
            ->firstOrFail();

        // Ensure correct approver
        abort_if($currentApprover->approver_id !== optional($employee)->employee_id, 403);

        if ($request->action === 'rejected') {
            $form->update([
                'form_status' => 'rejected',
                'approved_by' => $approvedBy,
                'approved_at' => now(),
            ]);
            return back()->with('error', 'Form rejected');
        }

        // Check next level
        $nextLevelExists = FormApprover::where('form_id', $form->id)
            ->where('level', '>', $form->approval_level)
            ->exists();

        if ($nextLevelExists) {
            $form->increment('approval_level');
            return back()->with('success', 'Forwarded to next approver');
        }

        // Final approval
        $form->update([
            'form_status' => 'approved',
            'approved_by' => $approvedBy,
            'approved_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Form has been approved.');
    }
}
