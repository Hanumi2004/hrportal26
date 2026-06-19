<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;

class Leave extends Model
{
    /** @use HasFactory<\Database\Factories\LeaveFactory> */
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'leave_entitlement_id',
        'leave_length',
        'leave_reason',
        'start_date',
        'end_date',
        'days',
        'attachment',

        'approved_by',
        'approval_level',
        'approval_at',

        'leave_status',
        'reject_reason',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'approved_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by', 'employee_id');
    }

    public function entitlement()
    {
        return $this->belongsTo(LeaveEntitlement::class, 'leave_entitlement_id', 'id');
    }

    public function approvers()
    {
        return $this->employee->approvers();
    }
	
	protected function attachmentUrl(): Attribute
	{
    return Attribute::get(fn () => $this->attachment 
        ? asset('storage/' . $this->attachment) 
        : null
    );
	
}
}