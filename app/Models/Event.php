<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    /** @use HasFactory<\Database\Factories\EventFactory> */
    use HasFactory;

    protected $fillable = [
        'created_by',
        'event_name',
        'description',
        'event_date',
        'event_time',
        'event_location',
        'event_category_id',
        'image',
        'event_status',
        'tags',
    ];

    // Optional: format date for easy usage in Blade
    protected $casts = [
        'event_date' => 'date',
        'event_time' => 'datetime:H:i',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // MAIN RELATION: raw pivot records
    public function attendees()
    {
        return $this->hasMany(EventAttendee::class);
    }

    // Convenience relation (optional, but useful)
    public function employees()
    {
        return $this->belongsToMany(
            Employee::class,
            'event_attendees',
            'event_id',
            'employee_id',
            'id',
            'employee_id',
            'response_status'
        );
    }

    public function category()
    {
        return $this->belongsTo(EventCategory::class, 'event_category_id');
    }
}
