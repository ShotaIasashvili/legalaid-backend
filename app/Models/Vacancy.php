<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vacancy extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'department',
        'location',
        'type',
        'status',
        'deadline',
        'is_active',
        'sort_order',
        'requirements',
        'responsibilities',
        'contact_email',
        'application_url',
    ];

    protected $casts = [
        'deadline'         => 'date',
        'is_active'        => 'boolean',
        'requirements'     => 'array',
        'responsibilities' => 'array',
    ];

    public function scopeOpen($query)
    {
        return $query->where('status', 'open')
                     ->where(function ($q) {
                         $q->whereNull('deadline')->orWhere('deadline', '>=', today());
                     });
    }
}
