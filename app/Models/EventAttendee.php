<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventAttendee extends Model
{
    /** @use HasFactory<\Database\Factories\EventAttendeeFactory> */
    use HasFactory;

    protected $fillable = [
        'event_id',
        'department_id',
        'employee_id',
        'response_status',
        'decline_reason',
        'responded_at',
        'attendance_status',
        'notes'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }
}
