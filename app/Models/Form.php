<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    /** @use HasFactory<\Database\Factories\FormFactory> */
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'form_type',
        'form_description',

        'approved_by',
        'approval_level',
        'approval_at',

        'form_status',   // form_status
        'reject_reason',
    ];

    // Who submitted the form
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by', 'employee_id');
    }

    // One-to-one detail forms
    public function workHandover()
    {
        return $this->hasOne(WorkHandover::class);
    }

    public function formApprovers()
    {
        return $this->hasMany(FormApprover::class);
    }
}
