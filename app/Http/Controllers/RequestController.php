<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Leave;
use App\Models\Attendance;
use App\Models\RequestApprover;
use App\Models\LeaveEntitlement;

class RequestController extends Controller
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

    // to show requests for approval (for approvers)
    public function requests(Request $request)
    {
        $user = Auth::user();
        $employee = $user->employee; // null for admin if no employee record

        $employeeId = optional(Auth::user()->employee)->employee_id;

        $leaveTypes = LeaveEntitlement::orderBy('name')->get();

        // --- Leave Requests with filters ---
        $leavesQuery = Leave::with(['employee', 'entitlement'])
            ->where('leave_status', 'pending')
            ->whereHas('employee.approvers', function ($q) use ($employeeId) {
                $q->where('approver_id', $employeeId)
                    ->whereColumn('request_approvers.level', 'leaves.approval_level');
            })
            ->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $leavesQuery->whereHas('employee', function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('employee_id', 'like', "%{$search}%");
            });
        }

        if ($request->filled('leave_entitlement_id')) {
            $leavesQuery->where('leave_entitlement_id', $request->leave_entitlement_id);
        }

        if ($request->filled('start_date')) {
            $leavesQuery->whereDate('start_date', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $leavesQuery->whereDate('end_date', $request->end_date);
        }

        if ($request->filled('created_at')) {
            $leavesQuery->whereDate('created_at', $request->created_at);
        }

        $pendingLeaves = $leavesQuery->get();

        // --- Time Slip Requests with filters ---
        $timeSlipsQuery = Attendance::with('employee')
            ->whereNotNull('time_slip_start')
            ->where('time_slip_status', 'pending')
            ->whereHas('employee.approvers', function ($q) use ($employeeId) {
                $q->where('approver_id', $employeeId);
            })
            ->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $timeSlipsQuery->whereHas('employee', function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('employee_id', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date')) {
            $timeSlipsQuery->whereDate('date', $request->date);
        }

        $pendingTimeSlips = $timeSlipsQuery->get();

        return view('employee.approver-request', compact(
            'leaveTypes',
            'pendingLeaves',
            'pendingTimeSlips'
        ));
    }

    // to show my submitted requests (for employees)
    public function myRequests(Request $request)
    {
        $user = Auth::user();
        $employee = $user->employee; // null for admin if no employee record

        $leaveTypes = LeaveEntitlement::orderBy('name')->get();

        // --- Leave Requests with filters ---
        $leavesQuery = Leave::with(['employee', 'entitlement'])
            ->where('employee_id', $employee->employee_id)
            ->where('leave_status', 'pending')
            ->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $leavesQuery->whereHas('employee', function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('employee_id', 'like', "%{$search}%");
            });
        }

        if ($request->filled('leave_entitlement_id')) {
            $leavesQuery->where('leave_entitlement_id', $request->leave_entitlement_id);
        }

        if ($request->filled('start_date')) {
            $leavesQuery->whereDate('start_date', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $leavesQuery->whereDate('end_date', $request->end_date);
        }

        if ($request->filled('created_at')) {
            $leavesQuery->whereDate('created_at', $request->created_at);
        }

        $pendingLeaves = $leavesQuery->get();

        // --- Time Slip Requests with filters ---
        $timeSlipsQuery = Attendance::with('employee')
            ->where('employee_id', $employee->employee_id)
            ->whereNotNull('time_slip_start')
            ->where('time_slip_status', 'pending')
            ->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $timeSlipsQuery->whereHas('employee', function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('employee_id', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date')) {
            $timeSlipsQuery->whereDate('date', $request->date);
        }

        $pendingTimeSlips = $timeSlipsQuery->get();

        return view('employee.employee-request', compact(
            'leaveTypes',
            'pendingLeaves',
            'pendingTimeSlips'
        ));
    }

    // to show all requests for approval (for admin)
    public function adminRequests(Request $request)
    {
        $leaveTypes = LeaveEntitlement::orderBy('name')->get();

        // --- Leave Requests with filters ---
        $leavesQuery = Leave::with(['employee', 'entitlement', 'employee.approvers'])
            ->where('leave_status', 'pending')
            ->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $leavesQuery->whereHas('employee', function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('employee_id', 'like', "%{$search}%");
            });
        }

        if ($request->filled('leave_entitlement_id')) {
            $leavesQuery->where('leave_entitlement_id', $request->leave_entitlement_id);
        }

        if ($request->filled('start_date')) {
            $leavesQuery->whereDate('start_date', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $leavesQuery->whereDate('end_date', $request->end_date);
        }

        if ($request->filled('created_at')) {
            $leavesQuery->whereDate('created_at', $request->created_at);
        }

        $pendingLeaves = $leavesQuery->get();

        // --- Time Slip Requests with filters ---
        $timeSlipsQuery = Attendance::with('employee')
            ->whereNotNull('time_slip_start')
            ->where('time_slip_status', 'pending')
            ->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $timeSlipsQuery->whereHas('employee', function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('employee_id', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date')) {
            $timeSlipsQuery->whereDate('date', $request->date);
        }

        $pendingTimeSlips = $timeSlipsQuery->get();

        return view('admin.admin-request', compact(
            'pendingLeaves',
            'pendingTimeSlips',
            'leaveTypes'
        ));
    }

    public function approveLeaves(Request $request, Leave $leave)
    {
        $request->validate([
            'action' => 'required|in:approved,rejected'
        ]);

        $user = Auth::user();
        $employeeId = optional($user->employee)->employee_id ?? null; // null if admin

        // Prevent approving leaves that are already done
        if ($leave->leave_status !== 'pending') {
            return back()->with('error', 'This leave request has already been processed.');
        }

        // Determine current approver
        $currentApprover = RequestApprover::where('employee_id', $leave->employee_id)
            ->where('approver_id', $employeeId)
            ->where('level', $leave->approval_level)
            ->first();

        // Fallback for admin (level 0 or any level with no approver assigned)
        if (!$currentApprover && $user->role_id === 2) {
            $currentApprover = true;
        }

        if (!$currentApprover) {
            abort(403, 'You are not authorized to approve this leave.');
        }

        // Handle rejection: always final
        if ($request->action === 'rejected') {
            $leave->update([
                'leave_status' => 'rejected',
                'approved_by' => $employeeId,
                'approved_at' => now(),
            ]);
            return back()->with('error', 'Leave request has been rejected.');
        }

        // Check if there’s a next approver
        $nextLevelExists = RequestApprover::where('employee_id', $leave->employee_id)
            ->where('level', '>', $leave->approval_level)
            ->exists();

        if ($nextLevelExists) {
            // Forward to next level
            $leave->increment('approval_level');
            return back()->with('success', 'Leave request has been approved and forwarded to the next approver.');
        }

        // Final approval (no next approver)
        $leave->update([
            'leave_status' => 'approved',
            'approved_by' => $employeeId,
            'approved_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Leave request has been fully approved.');
    }

    public function approveTimeSlips(Request $request, Attendance $attendance)
    {
        $request->validate([
            'action' => 'required|in:approved,rejected'
        ]);

        $attendance->time_slip_status = $request->action;
        $attendance->save();

        if ($request->action === 'rejected') {
            return redirect()->back()->with('error', 'Time slip has been rejected.');
        }

        return redirect()->back()->with('success', 'Time slip has been approved.');
    }
}
