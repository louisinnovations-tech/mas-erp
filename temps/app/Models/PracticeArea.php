<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PracticeArea extends Model
{
    protected $fillable = [
        'name',
    ];

    public function employee()
    {
        return $this->hasOne('App\Models\User', 'id', 'employee_id');
    }
}
