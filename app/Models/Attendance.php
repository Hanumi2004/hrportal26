<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    /** @use HasFactory<\Database\Factories\AttendanceFactory> */
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'date',
        'time_in',
        'time_in_lat',
        'time_in_lng',
        'location_in',
        'time_out',
        'time_out_lat',
        'time_out_lng',
        'location_out',
        'status_time_in',
        'status_time_out',
        'late_reason',
        'early_leave_reason',
        'time_slip_start',
        'time_slip_end',
        'time_slip_reason',
        'time_slip_status'
    ];

    protected $casts = [
        'date' => 'date',
        'time_in' => 'datetime:H:i',
        'time_out' => 'datetime:H:i',
        'time_slip_start' => 'datetime:H:i',
        'time_slip_end' => 'datetime:H:i'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }

    public function isTimeSlipPending()
    {
        return $this->time_slip_status === 'pending';
    }

    public function isTimeSlipApproved()
    {
        return $this->time_slip_status === 'approved';
    }
}
