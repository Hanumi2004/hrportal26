<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use App\Models\Event;
use App\Models\Attendance;
use App\Models\Task;
use App\Models\Leave;
use App\Models\Announcement;
use App\Models\Department;
use App\Models\Role;
use Carbon\Carbon;
use App\Models\Employment;
use App\Models\EmploymentStatus;
use App\Models\CompanyBranch;
use GuzzleHttp\Promise\Create;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Actions\Fortify\CreateNewUser;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Total Employees for active users
        $totalEmployees = Employee::count();

        // For attendance card
        // 1. Present today (employees who punched in today)
        $presentToday = Attendance::whereDate('date', Carbon::today())
            ->distinct('employee_id')
            ->count('employee_id');

        // 2. Absent today
        $absentToday = $totalEmployees - $presentToday;

        // Get all employees for attendance table
        $allEmployees = Employee::with('employment.department')
            ->get()
            ->map(function ($employee) {
                return [
                    'employee_id' => $employee->employee_id,
                    'full_name' => $employee->full_name,
                    'department' => $employee->employment?->department?->name ?? '-',
                    'position' => $employee->employment?->position ?? '-',
                ];
            });

        // Get today's attendance with employee details
        $todayAttendance = Attendance::with('employee')
            ->whereDate('date', Carbon::today())
            ->get()
            ->map(function ($attendance) {
                return [
                    'employee_id' => $attendance->employee_id,
                    'time_in' => $attendance->time_in ? $attendance->time_in->format('g:i A') : null,
                    'time_out' => $attendance->time_out ? $attendance->time_out->format('g:i A') : null,
                    'status_time_in' => $attendance->status_time_in,
                    'employee_name' => $attendance->employee->full_name
                ];
            });

        // recent announcements
        $announcements = Announcement::orderBy('created_at', 'desc')->take(5)->get();

        $recentActivities = $this->getRecentActivities();
        $recentRequests = $this->getRecentRequests();

        // --- Leave Counts ---
        $totalPendingLeaves   = Leave::where('leave_status', 'pending')->count();
        $totalApprovedLeaves  = Leave::where('leave_status', 'approved')->count();
        $totalRejectedLeaves  = Leave::where('leave_status', 'rejected')->count();

        // --- Time Slip Counts ---
        $totalPendingTimeSlips   = Attendance::whereNotNull('time_slip_start')
            ->where('time_slip_status', 'pending')->count();

        $totalApprovedTimeSlips  = Attendance::whereNotNull('time_slip_start')
            ->where('time_slip_status', 'approved')->count();

        $totalRejectedTimeSlips  = Attendance::whereNotNull('time_slip_start')
            ->where('time_slip_status', 'rejected')->count();

        // --- Combined (ALL pending/approved/rejected) ---
        $totalPending   = $totalPendingLeaves + $totalPendingTimeSlips;
        $totalApproved  = $totalApprovedLeaves + $totalApprovedTimeSlips;
        $totalRejected  = $totalRejectedLeaves + $totalRejectedTimeSlips;

        return view('admin.admin-dashboard', compact(
            'totalEmployees',
            'presentToday',
            'absentToday',
            'allEmployees',
            'todayAttendance',
            'announcements',
            'recentActivities',
            'recentRequests',
            'totalPending',
            'totalApproved',
            'totalRejected',
            'recentRequests',
        ));
    }

    public function showDashboardForLoggedInAdmin()
    {
        return $this->dashboard();
    }

    public function getRecentRequests()
    {
        $leaves = Leave::with(['employee', 'entitlement'])
            ->where('leave_status', 'pending')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($lv) {
                return [
                    'is_time_slip'    => false,
                    'employee'        => $lv->employee->full_name,
                    'type'            => ucfirst($lv->entitlement?->name ?? 'Leave'),
                    'status'          => $lv->leave_status,
                    'submitted_date'  => $lv->created_at->format('d M Y g:i A'),
                    'duration'        => $lv->start_date->format('d M Y') . ' → ' . $lv->end_date->format('d M Y'),
                    'timestamp'       => $lv->created_at, // For sorting
                ];
            });

        $timeSlips = Attendance::with('employee')
            ->whereNotNull('time_slip_start')
            ->where('time_slip_status', 'pending')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($ts) {
                return [
                    'is_time_slip'    => true,
                    'employee'        => $ts->employee->full_name,
                    'type'            => 'Time Slip',
                    'status'          => $ts->time_slip_status,
                    'submitted_date'  => $ts->created_at->format('d M Y g:i A'),
                    'duration'        => $ts->time_slip_start->format('g:i A') . ' - ' . $ts->time_slip_end->format('g:i A'),
                    
                    'timestamp'       => $ts->created_at, // For sorting
                ];
            });

        $recentRequests = collect()
            ->merge($timeSlips)
            ->merge($leaves)
            ->sortByDesc('timestamp')
            ->take(5)
            ->values();
        
        return $recentRequests;
    }

    public function getRecentActivities()
    {
        $activities = [];

        // Recent attendance punches
        $recentPunches = Attendance::with('employee')
            ->whereDate('date', Carbon::today())
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($recentPunches as $punch) {
            $activities[] = [
                'icon' => $punch->time_in ? 'person-check' : 'person-x',
                'title' => $punch->employee->full_name ?? 'Employee',
                'description' => $punch->time_in ? 'Punched in' : 'Punched out',
                'time' => $punch->created_at->diffForHumans()
            ];
        }

        // Recent leave requests (last 3)
        $recentLeaves = Leave::with(['employee', 'entitlement'])
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($recentLeaves as $leave) {
            $activities[] = [
                'icon' => 'calendar-plus',
                'title' => $leave->employee->full_name ?? 'Employee',
                'description' => 'Applied for ' . ($leave->entitlement?->name ?? 'leave'),
                'time' => $leave->created_at->diffForHumans()
            ];
        }

        // Sort all activities by time and take latest 10
        usort($activities, function ($a, $b) {
            return strtotime($b['time']) - strtotime($a['time']);
        });

        return array_slice($activities, 0, 10);
    }

    public function employee(Request $request)
    {
        $query = Employee::with(['employment.department', 'employment.status', 'employment.branch']);

        // Search by name or employee_id
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('employee_id', 'like', "%{$search}%");
            });
        }

        // Filter by department NAME
        if ($request->filled('department_name')) {
            $query->whereHas('employment.department', function ($q) use ($request) {
                $q->where('name', $request->department_name);
            });
        }

        // Filter by status
        if ($request->filled('employment_status_id')) {
            $query->whereHas('employment', function ($q) use ($request) {
                $q->where('employment_status_id', $request->employment_status_id);
            });
        }

        // Filter by branch
        if ($request->filled('company_branch_id')) {
            $query->whereHas('employment', function ($q) use ($request) {
                $q->where('company_branch_id', $request->company_branch_id);
            });
        }

        // Filter by date of employment
        if ($request->filled('date_of_employment')) {
            $query->whereHas('employment', function ($q) use ($request) {
                $q->whereDate('date_of_employment', $request->date_of_employment);
            });
        }

        // Card filters
        // filter for employments ending in next 30 days
        if ($request->filter === 'ending') {
            $query->whereHas('employment', function ($q) {
                $today = now();
                $next30Days = now()->addDays(30);

                $q->where(function ($sub) use ($today, $next30Days) {
                    $sub->whereBetween('contract_end', [$today, $next30Days])
                        ->orWhereBetween('termination_date', [$today, $next30Days])
                        ->orWhereBetween('last_working_day', [$today, $next30Days])
                        ->orWhereBetween('probation_end', [$today, $next30Days])
                        ->orWhereBetween('suspension_end', [$today, $next30Days]);
                });
            });
        }

        // filter for new employees this month
        if ($request->filter === 'new') {
            $query->whereHas('employment', function ($q) {
                $q->whereMonth('date_of_employment', now()->month)
                    ->whereYear('date_of_employment', now()->year);
            });
        }

        $employees = $query->orderBy('full_name')->get();

        // Stats for cards
        $totalEmployees = Employee::count();

        $employmentEnding = Employment::where(function ($q) {
            $today = now();
            $next30Days = now()->addDays(30);

            $q->whereBetween('contract_end', [$today, $next30Days])
                ->orWhereBetween('termination_date', [$today, $next30Days])
                ->orWhereBetween('last_working_day', [$today, $next30Days])
                ->orWhereBetween('probation_end', [$today, $next30Days])
                ->orWhereBetween('suspension_end', [$today, $next30Days]);
        })->count();


        $newThisMonth = Employment::whereMonth('date_of_employment', now()->month)
            ->whereYear('date_of_employment', now()->year)
            ->count();

        // Departments for dropdown
        $departments = Department::whereHas('employment')
            ->orderBy('name')
            ->pluck('name');

        $employmentStatuses = EmploymentStatus::orderBy('name')->get();
        $companyBranches = CompanyBranch::orderBy('name')->get();

        return view('admin.admin-employee', compact(
            'employees',
            'departments',
            'employmentStatuses',
            'companyBranches',
            'totalEmployees',
            'employmentEnding',
            'newThisMonth'
        ));
    }

    public function attendance()
    {
        $attendances = Attendance::with('employee')->orderBy('date', 'desc')->paginate(10);
        return view('admin.attendance', compact('attendances'));
    }

    public function tasks()
    {
        $tasks = Task::with('assignedTo')->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.tasks', compact('tasks'));
    }

    public function events()
    {
        $events = Event::orderBy('event_date', 'desc')->paginate(10);
        return view('admin.events', compact('events'));
    }

    public function createUser()
    {
        // abort_if(!auth()->user()->isAdmin(), 403);

        $roles = Role::orderBy('id')->get();

        return view('admin.admin-createemployee', compact('roles'));
    }

    public function storeUser(Request $request, CreateNewUser $creator)
    {
        $creator->create($request->all());

        return redirect()->route('admin.employee')->with('success', 'User created successfully! Please inform the user to check their email for login details.');
    }
	
	public function resetEmployeePassword(Request $request, User $user)
{
    abort_unless(auth()->user()->isAdmin(), 403);

    $validated = $request->validate([
        'password' => ['required', 'string', 'min:8', 'confirmed'],
        'force_password_reset' => ['nullable', 'boolean'],
    ]);

    $user->forceFill([
        'password' => Hash::make($validated['password']),
        'remember_token' => Str::random(60),
        'force_password_reset' => $request->boolean('force_password_reset'),
    ])->save();

    return back()->with('success', 'Employee password reset successfully.');
}

public function disableEmployee2FA(User $user)
{
    abort_unless(auth()->user()->isAdmin(), 403);

    $user->forceFill([
        'two_factor_secret' => null,
        'two_factor_recovery_codes' => null,
        'two_factor_confirmed_at' => null,
    ])->save();

    return back()->with('success', 'Two-factor authentication disabled successfully.');
}

public function forcePasswordReset(Request $request, User $user)
{
    abort_unless(auth()->user()->isAdmin(), 403);

    $action = $request->input('action');

    if ($action === 'force') {
        $user->force_password_reset = true;
    } elseif ($action === 'reset') {
        $user->force_password_reset = false;
    }

    $user->save();

    return back()->with('success', 'Password reset requirement updated successfully.');
}
}


