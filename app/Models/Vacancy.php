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
        'publish_starts_at',
        'publish_ends_at',
        'is_active',
        'sort_order',
        'requirements',
        'responsibilities',
        'contact_email',
        'application_url',
    ];

    protected $casts = [
        'deadline'         => 'date',
        'publish_starts_at' => 'datetime',
        'publish_ends_at'   => 'datetime',
        'is_active'        => 'boolean',
        'requirements'     => 'array',
        'responsibilities' => 'array',
    ];

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopePublished($query)
    {
        $now = now('Asia/Tbilisi');

        return $query->where('is_active', true)
            ->where('status', 'open')
            ->where(function ($q) use ($now) {
                $q->whereNull('publish_starts_at')->orWhere('publish_starts_at', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('publish_ends_at')->orWhere('publish_ends_at', '>=', $now);
            });
    }
}
