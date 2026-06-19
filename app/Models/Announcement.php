<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    /** @use HasFactory<\Database\Factories\AnnouncementFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'category',
        'created_by',
        'priority',
        'expires_date',
    ];

    protected $casts = [
        'expires_date' => 'date'
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
