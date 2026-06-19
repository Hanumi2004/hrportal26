<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Exports\LeavesExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\LeaveEntitlement;
use App\Models\Employee;
use Illuminate\Validation\Rule;

class LeaveController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // Show leave summary for logged-in employee
    public function index(Request $request)
    {
        $user = Auth::user();
        $employee = $user->employee; // null for admin if no employee record

        // FOR LEAVE APPLICATION TAB
        // Total approved days used

        $query = Leave::with(['employee', 'entitlement'])->orderBy('created_at', 'desc');

        // Only apply filters if the inputs exist
        if (in_array($user->role_id, [1, 2])) {
            // Search by name or employee_id
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->whereHas('employee', function ($qe) use ($search) {
                        $qe->where('full_name', 'like', "%{$search}%");
                    })
                        ->orWhere('employee_id', 'like', "%{$search}%");
                });
            }
        } else { // non-admin sees own leaves
            $query->where('employee_id', $employee->employee_id);
        }

        if ($request->filled('leave_entitlement_id')) {
            $query->where('leave_entitlement_id', $request->leave_entitlement_id);
        }

        if ($request->filled('leave_status')) {
            $query->where('leave_status', $request->leave_status);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('start_date', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('end_date', $request->end_date);
        }

        if ($request->filled('created_at')) {
            $query->whereDate('created_at', $request->created_at);
        }

        // Clone query BEFORE pagination (use code clone for pagination here, but apply later when stable)
        $summaryQuery = clone $query;

        $totalRequests  = $summaryQuery->count();

        $approvedLeaves = (clone $summaryQuery)->where('leave_status', 'approved')->count();
        $pendingLeaves  = (clone $summaryQuery)->where('leave_status', 'pending')->count();
        $rejectedLeaves = (clone $summaryQuery)->where('leave_status', 'rejected')->count();

        $usedDays = (clone $summaryQuery)->where('leave_status', 'approved')->sum('days');
        $leaves = $query->get();

        // FOR LEAVE REPORT TAB

        $selectedYear = request('year', now()->year);   // get year from URL (?year=2025) or default to current year
        $selectedEmployeeName = null;

        if (in_array($user->role_id, [1, 2])) {  // Only admins get multiple employee options
            $selectedEmployeeName = request('full_name');
            $employees = $employees = Employee::orderBy('full_name')->get();
        } else {
            $selectedEmployeeName = $employee->full_name;   // Employee cannot switch to other names
            $employees = collect([$employee]); // Still pass 1-item collection so <select> doesn’t break
        }

        // -------------------------
        // Build reportData: days taken per leave_type per month
        // Use SUM(days) so we count days, not number of records
        // -------------------------

        // Base query
        $reportQuery = DB::table('leaves')
            ->join('employees', 'leaves.employee_id', '=', 'employees.employee_id')
            ->leftJoin('leave_entitlements', 'leaves.leave_entitlement_id', '=', 'leave_entitlements.id')
            // combine leaves with employees to filter or display based on employee details (e.g. full name)
            ->selectRaw('leaves.leave_entitlement_id, leave_entitlements.name AS leave_type, MONTH(leaves.start_date) AS month, SUM(leaves.days) AS total')
            ->whereYear('leaves.start_date', $selectedYear);
        // Filter by whatever year the user selected instead of always now()->year

        // only add the name condition if the user (admin) picked one
        if (!in_array($user->role_id, [1, 2])) { // employee roles
            $reportQuery->where('employees.employee_id', $employee->employee_id);
        } elseif ($selectedEmployeeName && $selectedEmployeeName !== 'all') {
            $reportQuery->where('employees.full_name', $selectedEmployeeName);
        }

        $leaveReport = $reportQuery
            ->groupBy('leaves.leave_entitlement_id', 'leave_entitlements.name', 'month')     // grouping by leave type and month
            ->get();

        // pivot the results into [leave_type => [1=>count, 2=>count, ...]]
        $reportData = [];
        foreach ($leaveReport as $row) {
            $reportData[$row->leave_type ?? 'Unknown'][(int)$row->month] = (float)$row->total;
        }

        // -------------------------
        // Leave types (entitlements master)
        // Prefer the master table LeaveEntitlement. If empty, fallback to leave types present in reportData.
        // -------------------------
        $leaveTypes = LeaveEntitlement::all();
        // returns a Collection of LeaveEntitlement MODELS (objects with ->name)

        if ($leaveTypes->isEmpty()) {
            // fallback: convert the keys found in reportData to objects with leave_type and full_entitlement=0
            $leaveTypeNames = array_keys($reportData);
            $leaveTypes = collect(array_map(function ($name) {
                return (object)['name' => $name, 'full_entitlement' => 0];
            }, $leaveTypeNames));
        }

        // -------------------------
        // Compute final entitlements (prorated if joined this year)
        // -------------------------
        $finalEntitlements = [];

        // admins should not have “join date”
        // $employee will be null for admin bcs no employee record (same as in AttendanceController)
        // so we fix:

        $joinDate = null;

        if (!in_array($user->role_id, [1, 2]) && $employee && $employee->date_of_employment) {
            $joinDate = Carbon::parse($employee->date_of_employment);
        }

        foreach ($leaveTypes as $lt) {
            // lt may be model or fallback object; unify to string and full value
            $typeName = is_object($lt) ? ($lt->name ?? '') : (string)$lt;
            $full     = is_object($lt) ? ($lt->full_entitlement ?? 0) : 0;

            if ($joinDate && $joinDate->year == now()->year) {
                // prorate for first calendar year (months remaining including join month)
                $monthsLeft = 12 - $joinDate->month + 1;
                $prorated = round(($full / 12) * $monthsLeft, 2); // keep 2 decimal precision
                $finalEntitlements[$typeName] = $prorated;
            } else {
                $finalEntitlements[$typeName] = (float) $full;
            }
        }

        $view = in_array($user->role_id, [1, 2])? 'admin.admin-leave' : 'employee.employee-leave';

        return view($view, compact(
            'totalRequests',
            'approvedLeaves',
            'pendingLeaves',
            'rejectedLeaves',
            'usedDays',
            'leaves',
            'reportData',
            'leaveTypes',
            'finalEntitlements',
            'selectedYear',
            'selectedEmployeeName',
            'employees'
        ));
    }

    public function export(Request $request)
    {
        $year = $request->input('year', now()->year);
        // collect any filters passed by users (optional)
        // $filters = $request->only(['start_date', 'end_date']);

        return Excel::download(new LeavesExport($year), 'leave_report.xlsx');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $leaveTypes = LeaveEntitlement::orderBy('name')->get();
        $leaveLengthEnum = ['full_day', 'AM', 'PM'];

        return view('employee.applyleave', compact('leaveTypes', 'leaveLengthEnum'));
    }

    /**
     * Store a newly created resource in storage.
     */
    // Submit new leave request
    public function store(Request $request)
    {
        $employee = Auth::user()->employee;

        $request->validate([
            'leave_entitlement_id' => ['required', Rule::exists('leave_entitlements', 'id')],
            'start_date'    => 'required|date',
            'end_date'      => 'required|date|after_or_equal:start_date',
            'leave_reason'  => 'required|string|max:255',
            'attachment'    => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048'
        ]);

        $days = (new \Carbon\Carbon($request->start_date))
            ->diffInDays(new \Carbon\Carbon($request->end_date)) + 1;

        $leave = new Leave();
        $leave->employee_id  = $employee->employee_id;
        $leave->created_at   = Carbon::now()->toDateString();
        $leave->leave_entitlement_id = $request->leave_entitlement_id;
        $leave->leave_reason = $request->leave_reason;
        $leave->start_date   = $request->start_date;
        $leave->end_date     = $request->end_date;
        $leave->days         = $days;

        if ($request->hasFile('attachment')) {
            $filePath = $request->file('attachment')->store('leave_attachments', 'public');
            $leave->attachment = $filePath;
        }

        $leave->leave_status = 'pending';
        $leave->save();

        return redirect()->route('leave.index.employee')->with('success', 'Leave request submitted successfully!');
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
    public function cancel(Leave $leave)
    {
        $employee = Auth::user()->employee;
        if (! $employee || $leave->employee_id !== $employee->employee_id) {
            abort(403, 'Unauthorized.');
        }

        // Only allow cancelling a pending leave
        if ($leave->leave_status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending requests can be cancelled.');
        }

        $leave->delete();   // or $leave->update(['leave_status' => 'cancelled']) for history

        return redirect()->back()->with('success', 'Leave request cancelled.');
    }

    public function destroy(Leave $leave)
    {
        $employee = Auth::user()->employee;
        $role_id = Auth::user()->role_id;

        if (!in_array($user->role_id, [1, 2]) && $leave->employee_id !== $employee->employee_id) {
            abort(403, 'Unauthorized.');
        }

        $leave->delete();

        return redirect()->back()->with('success', 'Leave record deleted.');
    }
}
