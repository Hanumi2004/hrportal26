<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskAssignment extends Model
{
    /** @use HasFactory<\Database\Factories\TaskAssignmentFactory> */
    use HasFactory;

	protected $table = 'task_assignments';
	
    protected $fillable = [
		'task_id', 
		'department_id', 
		'employee_id',
		'assigned_by',
		'employee_status', 
        'employee_remarks',	
        'progress_updated_at',
        'approval_status',
	];
	
	protected $casts = [
        'progress_updated_at' => 'datetime',
		
	];


    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

	
	// Who is assigned
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }
	
	// NEW: Who assigned this employee
	public function assignedByEmployee()
	{
    	return $this->belongsTo(Employee::class, 'assigned_by', 'employee_id');
	}

	public function getKpiSubmissionStatusAttribute()
    {
        if (!$this->task || !$this->task->due_date) {
            return 'no_due_date';
        }

        $dueDateEnd = Carbon::parse($this->task->due_date)->endOfDay();

        if ($this->employee_status === 'completed') {
            if (!$this->progress_updated_at) {
                return 'completed';
            }

            return $this->progress_updated_at->lte($dueDateEnd)
                ? 'within_time'
                : 'late_submission';
        }

        return now()->gt($dueDateEnd)
            ? 'not_submitted'
            : 'pending';
    }

    public function getIsLockedAttribute()
    {
        return $this->employee_status === 'completed';
    }

}
