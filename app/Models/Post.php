<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Post extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'legacy_id',
        'title',
        'slug',
        'content',
        'excerpt',
        'featured_image',
        'featured_image_thumbnail',
        'featured_image_popup',
        'featured_image_single',
        'featured_image_webp',
        'featured_image_thumbnail_webp',
        'status',
        'published_at',
        'is_featured',
        'seo_title',
        'seo_description',
        'og_image',
        'author',
        'source_url',
        'extra_images',
        'views',
        'sort_order',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_featured'  => 'boolean',
        'extra_images' => 'array',
    ];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                     ->where(function ($q) {
                         $q->whereNull('published_at')
                           ->orWhere('published_at', '<=', now());
                     });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Post $post) {
            if (empty($post->slug)) {
                $post->slug = \Illuminate\Support\Str::slug($post->title);
            }
            if (empty($post->excerpt) && $post->content) {
                $post->excerpt = \Illuminate\Support\Str::limit(strip_tags($post->content), 200);
            }
        });
    }
}
