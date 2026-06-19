<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Exports\AttendanceExport;
use Maatwebsite\Excel\Facades\Excel;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // Show attendance summary for logged-in employee
    public function index(Request $request)
    {
        $user = Auth::user();
        $employee = $user->employee; // null for admin if no employee record
        $query = Attendance::with('employee')->orderBy('created_at', 'desc');

        // Admin: view all attendance
        if (in_array($user->role_id, [1, 2])) {

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->whereHas('employee', function ($qe) use ($search) {
                        $qe->where('full_name', 'like', "%{$search}%");
                    })
                        ->orWhere('employee_id', 'like', "%{$search}%");
                });
            }
        } else {
            if (!$employee) {
                abort(403, 'Employee record not found.');
            }

            $query->where('employee_id', $employee->employee_id);
        }

        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        if ($request->filled('status_time_in')) {
            $query->where('status_time_in', $request->status_time_in);
        }

        if ($request->filled('status_time_out')) {
            $query->where('status_time_out', $request->status_time_out);
        }

        if ($request->filled('location_in')) {
            $query->where('location_in', $request->location_in);
        }

        if ($request->filled('location_out')) {
            $query->where('location_out', $request->location_out);
        }

        // Finally fetch results
        $attendances = $query->get();

        // admins should not have “today attendance”
        // $employee will be null for admin bcs no employee record (same as in LeaveController)
        // so we fix:

        $todayAttendance = null;

        if (!in_array($user->role_id, [1, 2]) && $employee) {
            $todayAttendance = Attendance::where('employee_id', $employee->employee_id)
                ->whereDate('date', Carbon::today())
                ->orderBy('date', 'desc')
                ->orderBy('created_at', 'desc')
                ->first();
        }

        // provide distinct status options to the view (move DB calls out of blade)
        $statusTimeInOptions = Attendance::select('status_time_in')->distinct()->pluck('status_time_in')->filter()->values();
        $statusTimeOutOptions = Attendance::select('status_time_out')->distinct()->pluck('status_time_out')->filter()->values();
        $locationInOptions = Attendance::select('location_in')->distinct()->pluck('location_in')->filter()->values();
        $locationOutOptions = Attendance::select('location_out')->distinct()->pluck('location_out')->filter()->values();

        $view = in_array($user->role_id, [1, 2]) ? 'admin.admin-attendance' : 'employee.employee-attendance';

        return view($view, compact('attendances', 'todayAttendance', 'statusTimeInOptions', 'statusTimeOutOptions', 'locationInOptions', 'locationOutOptions'));
    }

    // Punch in (mark attendance)
    public function punchIn(Request $request)
    {
        // must use these to punchin/out work
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->first();
        if (! $employee) {
            return response()->json(['message' => 'Employee record not found.'], 404);
        }

        // Office location
        $officeLat = 3.2017;
        $officeLng = 101.73256;

        $lat = $request->latitude;
        $lng = $request->longitude;

        $maxDistance = 1.5; // km (radius)

        //If within office radius → status = "on-site". Else "off-site"
        $distance = $this->calculateDistance($officeLat, $officeLng, $lat, $lng);
        $location_in = $distance <= $maxDistance ? 'On-site' : 'Off-site';

        $now = Carbon::now();

        // Punch In (before shift 8:30/9am) → ✅ On Time
        // Punch In (8:30/9am) → ✅ Normal
        // Punch In (after 8:30/9am) → ❌ Late

        $employment = $employee->employment;

        // normal work start time
        $workStart = $employment && $employment->work_start_time
            ? Carbon::parse($employment->work_start_time)
            : Carbon::createFromTime(8, 30, 0);

        // Get today's attendance record if exists (maybe time slip issued earlier)
        $attendance = Attendance::where('employee_id', $employee->employee_id)
            ->whereDate('date', $now->toDateString())
            ->first();

        if (! $attendance) {
            // No attendance → create fresh record (no slip)
            $attendance = new Attendance();
            $attendance->employee_id = $employee->employee_id;
            $attendance->date = $now->toDateString();
        }

        $statusTimeIn = 'On Time';

        if (
            $attendance->time_slip_status &&
            $attendance->time_slip_status === 'approved' &&
            $attendance->time_slip_start &&
            $attendance->time_slip_end
        ) {

            $slipStart = Carbon::parse($attendance->time_slip_start);
            $slipEnd   = Carbon::parse($attendance->time_slip_end);

            // Rule: If inside slip window → always On Time
            if ($now->between($slipStart, $slipEnd)) {
                $statusTimeIn = 'On Time';
            }
            // If after slip window → Late 
            else if ($now->greaterThan($slipEnd)) {
                $statusTimeIn = 'Late';
            } // If before slip window → follow normal logic
            else {
                $statusTimeIn = $now->gt($workStart) ? 'Late' : 'On Time';
            }
        }
        // No Slip → Normal Logic
        else {
            // Normal logic
            $statusTimeIn = $now->greaterThan($workStart) ? 'Late' : 'On Time';
        }

        // Save punch in
        $attendance->time_in = $now->toTimeString();
        $attendance->time_in_lat = $lat;
        $attendance->time_in_lng = $lng;
        $attendance->location_in = $location_in;
        $attendance->status_time_in = $statusTimeIn;
        $attendance->save();

        return response()->json([
            'id'   => $attendance->id,
            'time' => $attendance->date . ' ' . $attendance->time_in,  //show both date and time
            'status_time_in' => $statusTimeIn,
            'location_in'  => $location_in,
            //to differentiate punch in and punch out:
            'success' => true,
            'action'  => 'punchIn'
        ]);
    }

    // Helper function to calculate distance in KM
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c; // Distance in km
    }

    public function punchOut(Request $request)
    {
        // must use these to punchin/out work
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->first();
        if (! $employee) {
            return response()->json(['message' => 'Employee record not found.'], 404);
        }
        // just use this doesnt work: $employee = Auth::user()->employee;

        // Check location again
        $officeLat = 3.2017;
        $officeLng = 101.73256;
        $maxDistance = 1.5; // km

        $lat = $request->latitude;
        $lng = $request->longitude;

        $distance = $this->calculateDistance($officeLat, $officeLng, $lat, $lng);
        $location_out = $distance <= $maxDistance ? 'On-site' : 'Off-site';

        $now = Carbon::now();

        // Punch Out (before 5:30pm) → ❌ Early Leave
        // Punch Out (5:30pm) → ✅ Normal
        // Punch Out (after 5:30pm) → ⭐ Overtime

        $employment = $employee->employment;

        $workEnd = $employment && $employment->work_end_time
            ? Carbon::parse($employment->work_end_time)
            : Carbon::createFromTime(17, 30, 0);

        // Find today's attendance
        $attendance = Attendance::where('employee_id', $employee->employee_id)
            ->whereDate('date', $now->toDateString())
            ->first();

        if (! $attendance) {
            return response()->json(['message' => 'No punch in record found for today.'], 400);
        }

        if ($attendance->time_out) {
            return response()->json(['success' => false, 'message' => 'You already punched out today.']);
        }

        $statusTimeOut = 'On Time';

        // If has approved time slip
        if (
            $attendance->time_slip_status &&
            $attendance->time_slip_status === 'approved' &&
            $attendance->time_slip_start &&
            $attendance->time_slip_end
        ) {

            $slipEnd = Carbon::parse($attendance->time_slip_end);

            // If slip extends work end → treat slip end as the official end
            if ($now->lt($slipEnd)) {
                $statusTimeOut = 'Early Leave';
            } else {
                $statusTimeOut = 'On Time';
            }
        } else {
            // Normal logic
            $statusTimeOut = $now->lt($workEnd) ? 'Early Leave' : 'On Time';
        }

        // Save punch out
        $attendance->update([
            'time_out'       => $now->toTimeString(),
            'time_out_lat'   => $lat,
            'time_out_lng'   => $lng,
            'location_out'   => $location_out,
            'status_time_out' => $statusTimeOut
        ]);

        return response()->json([
            'id'   => $attendance->id,
            'time' => $attendance->date . ' ' . $attendance->time_out,
            'status_time_out' => $statusTimeOut,
            //to differentiate punch in and punch out:
            'success' => true,
            'action'  => 'punchOut'
        ]);
    }

    // Export attendance to Excel
    public function export(Request $request)
    {
        // get year filter from request, default to current year
        $year = $request->input('year', now()->year);
        // collect any filters passed by users (optional)
        // $filters = $request->only(['start_date', 'end_date']);

        return Excel::download(new AttendanceExport($year), 'attendance_report.xlsx');
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
    public function edit()
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Attendance $attendance)
    {
        $request->validate([
            'late_reason' => 'nullable|string|max:255',
            'early_leave_reason' => 'nullable|string|max:255',
        ]);

        $attendance->late_reason = $request->late_reason;
        $attendance->early_leave_reason = $request->early_leave_reason;
        $attendance->save();

        return redirect()->back()->with('success', 'Attendance updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Allow employee to cancel their pending time slip request.
     */
    public function destroyTimeSlip(Attendance $attendance)
    {
        $employee = Auth::user()->employee;
        if (! $employee || $attendance->employee_id !== $employee->employee_id) {
            abort(403, 'Unauthorized.');
        }

        // only act when time slip exists & is pending
        if (! $attendance->time_slip_start || $attendance->time_slip_status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending time slip requests can be cancelled.');
        }

        $now = Carbon::now();

        // Find today's attendance
        $attendance = Attendance::where('employee_id', $employee->employee_id)
            ->whereDate('date', $now->toDateString())
            ->first();

        if ($attendance->time_in === null) {
            $attendance->delete();
        } elseif ($attendance->time_in) {
            // Reset time slip fields only
            $attendance->update([
                'time_slip_start'  => null,
                'time_slip_end'    => null,
                'time_slip_reason' => null,
                'time_slip_status' => null,
            ]);
        }

        return redirect()->back()->with('success', 'Time slip request cancelled.');
    }

    public function requestTimeSlip(Request $request)
    {
        $user = Auth::user();
        $employee = $user->employee;

        $request->validate([
            'time_slip_start' => 'required|date_format:H:i',
            'time_slip_end'   => 'required|date_format:H:i|after:time_slip_start',
            'time_slip_reason' => 'required|string|max:255',
        ]);

        // Calculate duration in minutes
        $start = Carbon::createFromFormat('H:i', $request->time_slip_start);
        $end = Carbon::createFromFormat('H:i', $request->time_slip_end);
        $durationMinutes = $end->diffInMinutes($start);

        // Check if duration exceeds 2 hours (120 minutes)
        if ($durationMinutes > 120) {
            return redirect()->back()
                ->withErrors(['time_slip_end' => 'Time slip cannot exceed 2 hours.'])
                ->withInput();
        }

        $todayAttendance = Attendance::firstOrCreate(
            ['employee_id' => $employee->employee_id, 'date' => now()->toDateString()]
        );

        $todayAttendance->update([
            'time_slip_start' => $request->time_slip_start,
            'time_slip_end' => $request->time_slip_end,
            'time_slip_reason' => $request->time_slip_reason,
            'time_slip_status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Time slip request submitted.');
    }

    public function pendingTimeSlips()
    {
        $pendingTimeSlips = Attendance::with('employee')
            ->whereNotNull('time_slip_start')
            ->where('time_slip_status', 'pending')
            ->orderBy('date', 'desc')
            ->get();

        return view('admin.pending-time-slips', compact('pendingTimeSlips'));
    }
}
