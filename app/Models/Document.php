<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'type',
        'size',
        'user_id',
        'meeting_id',
        'permissions'
    ];

    protected $casts = [
        'permissions' => 'array',
        'size' => 'integer'
    ];

    public function versions()
    {
        return $this->hasMany(DocumentVersion::class)->orderBy('version', 'desc');
    }

    public function latestVersion()
    {
        return $this->hasOne(DocumentVersion::class)->latestOfMany('version');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function meeting()
    {
        return $this->belongsTo(Meeting::class);
    }

    public function getLatestVersionAttribute()
    {
        return $this->versions()->first();
    }

    public function getSizeFormattedAttribute()
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $size = $this->size;
        $unit = 0;

        while ($size >= 1024 && $unit < count($units) - 1) {
            $size /= 1024;
            $unit++;
        }

        return round($size, 2) . ' ' . $units[$unit];
    }
}