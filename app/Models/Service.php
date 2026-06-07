<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'subtitle',
        'slug',
        'description',
        'full_content',
        'icon',
        'category',
        'color',
        'requirements',
        'how_to_apply',
        'related_services',
        'special_eligibility_categories',
        'download_links',
        'is_active',
        'sort_order',
        'featured_image',
    ];

    protected $casts = [
        'requirements'                  => 'array',
        'how_to_apply'                  => 'array',
        'related_services'              => 'array',
        'special_eligibility_categories' => 'array',
        'download_links'                => 'array',
        'is_active'                     => 'boolean',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
