<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveEntitlement extends Model
{
    protected $fillable = [
        'name',
        'full_entitlement', // number of days entitled per year
    ];

    public function leaves()
    {
        return $this->hasMany(Leave::class);
    }
}
