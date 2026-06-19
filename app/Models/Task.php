<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    /** @use HasFactory<\Database\Factories\TaskFactory> */
    use HasFactory;

    protected $fillable = [
        'project_id',
        'created_by',
        'task_name',
        'task_desc',
        'task_status',
        'notes',
        'due_date',
        'attachments',
    ];

    protected $casts = [
        'due_date'    => 'date',
        'attachments' => 'array',
    ];

    public function project()
	{
    // foreign key on tasks: project_id, owner key on projects: id
    return $this->belongsTo(Project::class, 'project_id', 'id');
	}

	public function createdBy()
	{
    // foreign key on tasks: created_by, owner key on employees: employee_id
    return $this->belongsTo(Employee::class, 'created_by', 'employee_id');
	}

    public function assignedTo()
    {
        return $this->belongsToMany
        	(Employee::class,
        	'task_assignments',
    		'task_id',
    		'employee_id',
    		'id',
    		'employee_id')
		->withPivot([
			'department_id',
            'assigned_by',
            'employee_status',
            'employee_remarks',
            'progress_updated_at',
            ])
            ->withTimestamps();

    }
	
	// Get assignment with assignee and assigner details
	public function getAssignmentDetails()
	{
		return $this->assignedTo()
        	->with(['employment.department'])
        	->with(['assignedByEmployee'])
    		->get()
        	->map(function ($employee) {
        		return [
                	'employee_id' => $employee->employee_id,
                	'full_name' => $employee->full_name,
                	'department' => $employee->employment?->department?->name ?? '-',
                	'assigned_by' => $employee->pivot->assigned_by,
                	'assigned_by_name' => $employee->assignedByEmployee?->full_name ?? '-',
                	'assigned_at' => $employee->pivot->created_at,
                	'status' => $employee->pivot->employee_status ?? 'pending',
					
                	'remarks' => $employee->pivot->employee_remarks,
                	'progress_updated_at' => $employee->pivot->progress_updated_at,
        	];
    	});
}

	
	public function assignments()
    {
        return $this->hasMany(TaskAssignment::class, 'task_id');
    }

    public function progressLogs()
    {
        return $this->hasMany(TaskProgressLog::class)->orderBy('progress_updated_at', 'desc');
    }

    public function getCompletionPercentageAttribute()
    {
        $assignments = $this->assignedTo;

        if ($assignments->isEmpty()) {
            return 0;
        }

        $completed = $assignments->where('pivot.employee_status', 'completed')->count();

        return round(($completed / $assignments->count()) * 100);
    }
	
}
