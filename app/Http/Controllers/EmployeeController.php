<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Employment;
use App\Models\Event;
use App\Models\Attendance;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Leave;
use App\Models\Announcement;
use App\Models\Department;
use App\Models\EmploymentType;
use App\Models\EmploymentStatus;
use App\Models\CompanyBranch;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    public function showDashboardForLoggedInUser()
    {
        $employee = Auth::user()->employee;

        // Get today's attendance with employee details
        $todayAttendance = Attendance::where('employee_id', $employee->employee_id)
            ->whereDate('date', Carbon::today())
            ->first();

        // recent announcements
        $announcements = Announcement::orderBy('created_at', 'desc')->take(5)->get();

        $employeeId = $employee->employee_id;
        $recentActivities = $this->getMyRecentActivities();
        $recentRequests = $this->getMyRecentRequests();

        // --- Leave Counts ---
        $totalPendingLeaves   = Leave::where('employee_id', $employeeId)->where('leave_status', 'pending')->count();
        $totalApprovedLeaves  = Leave::where('employee_id', $employeeId)->where('leave_status', 'approved')->count();
        $totalRejectedLeaves  = Leave::where('employee_id', $employeeId)->where('leave_status', 'rejected')->count();

        // --- Time Slip Counts ---
        $totalPendingTimeSlips   = Attendance::where('employee_id', $employeeId)->whereNotNull('time_slip_start')
            ->where('time_slip_status', 'pending')->count();

        $totalApprovedTimeSlips  = Attendance::where('employee_id', $employeeId)->whereNotNull('time_slip_start')
            ->where('time_slip_status', 'approved')->count();

        $totalRejectedTimeSlips  = Attendance::where('employee_id', $employeeId)->whereNotNull('time_slip_start')
            ->where('time_slip_status', 'rejected')->count();

        // --- Combined (ALL pending/approved/rejected) ---
        $totalPending   = $totalPendingLeaves + $totalPendingTimeSlips;
        $totalApproved  = $totalApprovedLeaves + $totalApprovedTimeSlips;
        $totalRejected  = $totalRejectedLeaves + $totalRejectedTimeSlips;

        return view(
            'employee.employee-dashboard',
            compact(
                'employee',
                'todayAttendance',
                'announcements',
                'recentActivities',
                'recentRequests',
                'totalPending',
                'totalApproved',
                'totalRejected',
            )
        );
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employees = Employee::with('user')->get();
        return response()->json($employees);
    }

    /**
     * Get employee's requests (leaves and time slips)
     */
    public function getMyRecentRequests()
    {
        $employeeId = Auth::user()->employee->employee_id;

        $requests = [];

        // Get leaves
        $leaves = Leave::with('entitlement')
            ->where('employee_id', $employeeId)
            ->where('leave_status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($leave) {
                return [
                    'is_time_slip'      => false,
                    'type'              => ucfirst($leave->entitlement?->name ?? 'Leave'),
                    'status'            => $leave->leave_status,
                    'submitted_date'    => $leave->created_at->format('d M Y g:i A'),
                    'duration'          => $leave->start_date->format('d M Y') . ' → ' . $leave->end_date->format('d M Y'),
                    'timestamp'         => $leave->created_at,
                ];
            });

        // Get time slips
        $timeSlips = Attendance::where('employee_id', $employeeId)
            ->whereNotNull('time_slip_start')
            ->where('time_slip_status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($ts) {
                return [
                    'is_time_slip'      => true,
                    'type'              => 'Time Slip',
                    'status'            => $ts->time_slip_status,
                    'submitted_date'    => $ts->created_at->format('d M Y g:i A'),
                    'duration'          => $ts->time_slip_start->format('g:i A') . ' - ' . $ts->time_slip_end->format('g:i A'),                   
                    'timestamp'         => $ts->created_at,
                ];
            });

        $requests = collect()
            ->merge($leaves)
            ->merge($timeSlips)
            ->sortByDesc('timestamp')
            ->values()
            ->toArray();

        return $requests;
    }

    /**
     * Get employee's recent activities
     */
    public function getMyRecentActivities()
    {
        $employeeId = Auth::user()->employee->employee_id;

        $activities = [];

        // Recent attendance punches
        $punches = Attendance::where('employee_id', $employeeId)
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get();

        foreach ($punches as $punch) {
            $activities[] = [
                'icon' => $punch->time_in ? 'person-check' : 'person-x',
                'title' => $punch->time_in ? 'Punched In' : 'Punched Out',
                'description' => $punch->time_in ? 'Clocked in at ' . $punch->time_in : 'Clocked out',
                'time' => $punch->created_at->diffForHumans(),
                'timestamp' => $punch->created_at,
            ];
        }

        // Recent leave requests
        $leaves = Leave::with('entitlement')
            ->where('employee_id', $employeeId)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        foreach ($leaves as $leave) {
            $activities[] = [
                'icon' => 'calendar-plus',
                'title' => 'Leave Request',
                'description' => 'Applied for ' . ucfirst($leave->entitlement?->name ?? 'leave') . ' leave (' . $leave->leave_status . ')',
                'time' => $leave->created_at->diffForHumans(),
                'timestamp' => $leave->created_at,
            ];
        }

        // Sort and limit to 10
        usort($activities, function ($a, $b) {
            return $b['timestamp']->timestamp - $a['timestamp']->timestamp;
        });

        return array_slice($activities, 0, 10);
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
    public function show(?Employee $employee = null)
    {
        $user = Auth::user();

        $role_id = $user->role_id;

        /**
         * CASE 1: No employee passed in route
         * - Show own profile
         */
        if (! $employee) {
            $employee = $user->employee; // may be null (admin)
        }

        /**
         * CASE 2: Employee passed in route
         * - Admin can view anyone
         * - Employee can only view themselves
         */
        if ($employee) {
            if (!in_array($user->role_id, [1, 2])) {
                if (! $user->employee || $employee->employee_id !== $user->employee->employee_id) {
                    abort(403, 'Unauthorized action.');
                }
            }
        }

        $employment = $employee?->employment()
            ->with('reportToEmployee')
            ->first();

        return view('profile.show', compact('user', 'role_id', 'employee', 'employment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function settings()
    {
        $employee = Auth::user()->employee;

        return view('profile.settings', compact('employee'));
    }

    public function editPersonal(Employee $employee)
    {
        // Admin can view anyone; employee sees themselves
        if (!in_array(Auth::user()->role_id, [1, 2])) {
            // Ensure employee can only edit their own profile
            if ($employee->employee_id != Auth::user()->employee->employee_id) {
                abort(403, 'Unauthorized action.');
            }
        }

        return view('profile.editprofile', compact('employee'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function updatePersonal(Request $request, Employee $employee)
    {
        // Admin can update anyone; employee can only update themselves
        if (!in_array(Auth::user()->role_id, [1, 2])) {
            if ($employee->employee_id != Auth::user()->employee->employee_id) {
                abort(403, 'Unauthorized action.');
            }
        }

        $validated = $request->validate([
            'full_name'         => 'required|string|max:255',
            'email'             => 'required|string',
            'phone_number'      => 'required|string|max:20',
            'address'           => 'required|string|max:500',
            'gender'            => 'required|string|max:10',
            'birthday'          => 'required|date',
            'marital_status'    => 'required|string|max:50',
            'nationality'       => 'required|string|max:50',
            'emergency_contact_name' => 'required|string|max:255',
            'emergency_contact_number' => 'required|string|max:20',
            'emergency_contact_relationship' => 'required|string|max:50',
            'ic_number'         => 'required|string|max:20',
            'highest_education_level' => 'required|string|max:100',
            'highest_education_institution' => 'required|string|max:255',
            'graduation_year'   => 'required|integer|min:1900|max:' . date('Y'),
        ]);

        $employee->update($validated);
        return redirect()->route('profile.show', $employee->employee_id)->with('success', 'Profile updated successfully!');
    }

        public function editEmployment(Employee $employee)
    {
        // Only superadmin and admin can edit employment details
        if (!in_array(Auth::user()->role_id, [1, 2])) {
        abort(403, 'Unauthorized action.');
    }

        $employment = Employment::where('employee_id', $employee->employee_id)->first();
        $departments = Department::orderBy('name')->get();
        $employmentTypes = EmploymentType::orderBy('name')->get();
        $employmentStatuses = EmploymentStatus::orderBy('name')->get();
        $companyBranches = CompanyBranch::orderBy('name')->get();

        $employee->load('employment.department', 'approvers');
        $departmentId = $employee->employment?->department_id;

        // 1. Manager (role_id=4) dari department YANG SAMA - Priority Level 1
        $managerCandidates = collect();
        if ($departmentId) {
            $managerCandidates = Employee::whereHas('user', function ($q) {
                $q->where('role_id', 4); // Role "Manager"
            })->whereHas('employment', function ($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            })->where('employee_id', '!=', $employee->employee_id)
              ->get();
        }

        // 2. Staff lain dari department YANG SAMA (untuk Level 2 atau optional)
        $otherCandidates = Employee::whereHas('employment', function ($q) use ($departmentId) {
            $q->where('department_id', $departmentId);
        })->where('employee_id', '!=', $employee->employee_id)
          ->whereNotIn('employee_id', $managerCandidates->pluck('employee_id')->toArray())
          ->get();

        // 3. Combine: Manager dulu, then others
        $approverCandidates = $managerCandidates->merge($otherCandidates);

        // 4. Fallback: Kalau tiada department, tunjuk semua staff (biar admin pilih manual)
        if ($approverCandidates->isEmpty()) {
            $approverCandidates = Employee::where('employee_id', '!=', $employee->employee_id)->get();
        }

        $level1Approver = $employee->approvers->firstWhere('pivot.level', 1);
        $level2Approver = $employee->approvers->firstWhere('pivot.level', 2);

        return view('profile.editemployment', compact(
            'employee',
            'employment',
            'departments',
            'employmentTypes',
            'employmentStatuses',
            'companyBranches',
            'approverCandidates',
            'level1Approver',
            'level2Approver'
        ));
    }


    /**
     * Update the specified resource in storage.
     */
    public function updateEmployment(Request $request, Employee $employee)
    {
        // Only admin can update employment details
        if (!in_array(Auth::user()->role_id, [1, 2])) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'employment_type_id'    => 'required|exists:employment_types,id',
            'employment_status_id'  => 'required|exists:employment_statuses,id',
            'company_branch_id'     => 'required|exists:company_branches,id',
            'report_to'             => 'nullable|exists:employees,employee_id',
            'department_id'         => 'required|exists:departments,id',
            'position'              => 'nullable|string|max:100',
            'date_of_employment'    => 'required|date',
            'contract_start'        => 'nullable|date',
            'contract_end'          => 'nullable|date',
            'probation_start'       => 'nullable|date',
            'probation_end'         => 'nullable|date',
            'suspension_start'      => 'nullable|date',
            'suspension_end'        => 'nullable|date',
            'resignation_date'      => 'nullable|date',
            'last_working_day'      => 'nullable|date',
            'termination_date'      => 'nullable|date',
            'work_start_time'       => 'nullable|date_format:H:i',
            'work_end_time'         => 'nullable|date_format:H:i',
        ]);

        Employment::updateOrCreate(
            ['employee_id' => $employee->employee_id],
            $validated
        );

        return redirect()->route('profile.show', $employee->employee_id)->with('success', 'Employment details updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function downloadProfile(string $id)
    {
        $employee = Employee::findOrFail($id);
        $employment = Employment::where('employee_id', $id)->first();

        $pdf = Pdf::loadView('pdf.employee-profile', compact('employee', 'employment'));

        return $pdf->download("employee-profile-{$id}.pdf");
    }

    public function updateProfilePhoto(Request $request, Employee $employee)
    {
        abort_unless(in_array(Auth::user()->role_id, [1, 2]), 403);

        $request->validate([
            'profile_photo' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $user = $employee->user;

        if (!$user) {
            return back()->withErrors('Employee has no linked user account.');
        }

        // Delete old photo
        if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        // Store new photo
        $path = $request->file('profile_photo')->store('profile-photos', 'public');

        $user->update([
            'profile_photo_path' => $path,
        ]);

        return back()->with('success', 'Profile photo updated successfully.');
    }

    public function getAllEmployees()
    {
        return Employee::with('employment.department')
            ->whereHas('employment.status', fn($q) => $q->where('name', 'active'))
            ->get()
            ->map(fn($e) => [
                'id'         => $e->employee_id,
                'name'       => $e->full_name,
                'department' => $e->employment->department->name ?? 'N/A',
            ]);
    }
}
