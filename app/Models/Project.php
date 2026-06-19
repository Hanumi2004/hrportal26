<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_name',
        'project_desc',
        'created_by',
        'start_date',
        'end_date',
        'project_status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    public function tasks()
    {
        // FK: tasks.project_id -> projects.id
        return $this->hasMany(Task::class, 'project_id', 'id');
    }

    public function createdBy()
    {
        // FK: projects.created_by -> employees.employee_id
        return $this->belongsTo(Employee::class, 'created_by', 'employee_id');
    }
}