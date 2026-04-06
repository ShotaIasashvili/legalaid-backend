<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'subtitle',
        'description',
        'content',
        'featured_image',
        'featured_image_thumbnail',
        'featured_image_popup',
        'featured_image_webp',
        'featured_image_thumbnail_webp',
        'partner',
        'donor',
        'external_url',
        'start_date',
        'end_date',
        'status',
        'is_active',
        'is_featured',
        'sort_order',
    ];

    protected $casts = [
        'start_date'  => 'date',
        'end_date'    => 'date',
        'is_active'   => 'boolean',
        'is_featured' => 'boolean',
    ];
}
