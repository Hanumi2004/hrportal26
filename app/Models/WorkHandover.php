<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkHandover extends Model
{
    /** @use HasFactory<\Database\Factories\WorkHandoverFactory> */
    use HasFactory;

    protected $fillable = [
        'form_id',
        'last_working_day',
        'handover_to',
        'handover_reason',
        'handover_notes',

        'tasks',
        'documents',
        'electronic_files',
        'passwords',
        'financial_commitments',
        'inventory',
    ];

    protected $casts = [
        'tasks' => 'array',
        'documents' => 'array',
        'electronic_files' => 'array',
        'passwords' => 'array',
        'financial_commitments' => 'array',
        'inventory' => 'array',
    ];

    public function form()
    {
        return $this->belongsTo(Form::class);
    }

    public function handoverTo()
    {
        return $this->belongsTo(Employee::class, 'handover_to', 'employee_id');
    }
}
