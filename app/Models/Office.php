<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Office extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'region',
        'city',
        'address',
        'phone',
        'mobile',
        'email',
        'head',
        'working_hours',
        'lat',
        'lng',
        'description',
        'photo',
        'is_active',
        'is_specialized',
        'sort_order',
        'services',
    ];

    protected $casts = [
        'lat'            => 'float',
        'lng'            => 'float',
        'is_active'      => 'boolean',
        'is_specialized' => 'boolean',
        'services'       => 'array',
    ];
}
