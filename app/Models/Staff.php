<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Staff extends Model
{
    use SoftDeletes;

    protected $table = 'staff';

    protected $fillable = [
        'name',
        'position',
        'department',
        'type',
        'bio',
        'full_bio',
        'photo',
        'photo_thumbnail',
        'email',
        'phone',
        'from_date',
        'to_date',
        'is_active',
        'sort_order',
        'achievements',
        'education',
        'career',
    ];

    protected $casts = [
        'from_date'    => 'date',
        'to_date'      => 'date',
        'is_active'    => 'boolean',
        'achievements' => 'array',
        'education'    => 'array',
        'career'       => 'array',
    ];
}
