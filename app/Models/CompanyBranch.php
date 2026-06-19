<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyBranch extends Model
{
    protected $fillable = ['name'];

    public function employments()
    {
        return $this->hasMany(Employment::class);
    }
}
