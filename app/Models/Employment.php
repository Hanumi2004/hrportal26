<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employment extends Model
{
    /** @use HasFactory<\Database\Factories\EmploymentFactory> */
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'department_id',
        'employment_type_id',
        'employment_status_id',
        'company_branch_id',
        'report_to',
        'position',

        'date_of_employment', // for all & could be same date for contract_start

        'contract_start', // for contract/intern employees
        'contract_end',

        'probation_start', // for probation employees
        'probation_end',

        'suspension_start', // for suspended employees
        'suspension_end',

        'resignation_date', // for resigned employees
        'last_working_day',

        'termination_date', // for terminated employees

        'work_start_time', // not everyone has work_start/end time, e.g:part time
        'work_end_time',
    ];

    protected $casts = [
        'date_of_employment' => 'date',
        'contract_start' => 'date',
        'contract_end' => 'date',
        'probation_start' => 'date',
        'probation_end' => 'date',
        'suspension_start' => 'date',
        'suspension_end' => 'date',
        'resignation_date' => 'date',
        'last_working_day' => 'date',
        'termination_date' => 'date',
        'work_start_time' => 'datetime:H:i',
        'work_end_time' => 'datetime:H:i'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }

    public function reportToEmployee()
    {
        return $this->belongsTo(Employee::class, 'report_to', 'employee_id');
    }
    
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function branch()
    {
        return $this->belongsTo(CompanyBranch::class, 'company_branch_id');
    }

    public function type()
    {
        return $this->belongsTo(EmploymentType::class, 'employment_type_id');
    }

    public function status()
    {
        return $this->belongsTo(EmploymentStatus::class, 'employment_status_id');
    }
}
