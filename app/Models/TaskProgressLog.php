<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
 
class TaskProgressLog extends Model
{
    protected $table = 'task_progress_logs';
 
    protected $fillable = [
			'task_id',
			'employee_id',
			'employee_status',
			'employee_remarks',
			'attachment_path',
			'progress_updated_at',
		];
 
    protected $casts = [
        'progress_updated_at' => 'datetime',
        'attachment_path'     => 'array',
    ];
 
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }
 
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }
}