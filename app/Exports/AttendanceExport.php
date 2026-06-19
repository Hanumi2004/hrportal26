<?php

namespace App\Exports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class AttendanceExport implements FromCollection, WithHeadings, WithMapping
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
        // Accepts an array of filters (e.g., ['start_date' => ..., 'end_date' => ...]).
        // Stores them for use in the query.
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // base query
        $query = Attendance::with('employee')->orderBy('date', 'desc');

        // example filter by date range
        if (!empty($this->filters['start_date']) && !empty($this->filters['end_date'])) {
            $query->whereBetween('start_date', [
                $this->filters['start_date'],
                $this->filters['end_date']
            ]);
        }
        return $query->get();
    }

    // WithMapping: Customize how each row is mapped (formatted) in the export
    public function map($attendance): array
    {
        return [
            $attendance->date,
            $attendance->time_in,
            $attendance->location,
            ucfirst($attendance->status_time_in),
            $attendance->late_reason,
            $attendance->time_out,
            ucfirst($attendance->status_time_out),
            $attendance->early_leave_reason,
            ucfirst($attendance->status),
        ];
    }

    // WithHeadings: Allows to define custom column headings for the exported file
    public function headings(): array
    {
        return [
            'Date',
            'Time In',
            'Location',
            'Status Time In',
            'Late Reason',
            'Time Out',
            'Status Time Out',
            'Early Leave Reason',
            'Status',
        ];
    }


}
