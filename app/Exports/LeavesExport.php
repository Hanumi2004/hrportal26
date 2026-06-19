<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\LeaveEntitlement;
use Carbon\Carbon;

class LeavesExport implements FromView
{
    protected $year;

    public function __construct($year = null)
    {
        $this->year = $year ?? now()->year; 
    }

    public function view(): View
    {
        $employee = Auth::user()->employee;

        // -------------------------
        // Build reportData: days taken per leave_type per month
        // Use SUM(days) so we count days, not number of records
        // -------------------------
        $leaveReport = DB::table('leaves')
            ->leftJoin('leave_entitlements', 'leaves.leave_entitlement_id', '=', 'leave_entitlements.id')
            ->selectRaw('leaves.leave_entitlement_id, leave_entitlements.name AS leave_type, MONTH(start_date) AS month, SUM(days) AS total')
            ->where('employee_id', $employee->employee_id)
            ->whereYear('start_date', now()->year)
            ->groupBy('leaves.leave_entitlement_id', 'leave_entitlements.name', 'month')
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
        // make sure join date is Carbon (employee model should cast it)
        $joinDate = $employee->date_of_employment ? Carbon::parse($employee->date_of_employment) : null;

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


        return view('exports.leave_report', compact(
            'reportData',
            'leaveTypes',
            'finalEntitlements',
        ));

    }
}

// FromView (for complex exports with custom formatting)
// Laravel Excel renders this view as an HTML table and converts it to Excel
// must create the Blade file for this to work

// FromCollection or FromQuery (for simple exports)
// You return a collection or query
// Laravel Excel handles the data and columns automatically, no blade needed
