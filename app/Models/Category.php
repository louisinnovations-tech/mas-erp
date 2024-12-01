<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', 'color','created_by',
        'type'
];

public function users()
{
    return $this->belongsToMany('App\Models\User', 'user_categories', 'category_id','user_id');
}
public static $categoryType = [
    'Item',
    'Estimates',
    'Project',
];
}
