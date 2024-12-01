<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'mobile_number',
        'whats_app_number',
        'address',
        'city',
        'state',
        'zip_code',
        'landmark',
        'about',
        'building_number',
        'street_number',
        'zone_number',
        'qid_number',
        'passport_number',
        'land_phone',
        'extension_number',
        'profession',
        'department',
        'language'
    ];

    public static function getUserDetail($id)
    {
        $detail  = UserDetail::where('user_id', $id)->first();
        return $detail;
    }
}
