<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
        return $query->where(function ($query) {
            $query->where(function ($published) {
                $published->where('status', 'published')
                    ->where(function ($date) {
                        $date->whereNull('published_at')
                            ->orWhere('published_at', '<=', now());
                    });
            })->orWhere(function ($scheduled) {
                $scheduled->where('status', 'scheduled')
                    ->whereNotNull('published_at')
                    ->where('published_at', '<=', now());
            });
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getFeaturedImageUrlAttribute(): ?string
    {
        return static::resolveImageUrl($this->featured_image);
    }

    public function getFeaturedImageThumbnailUrlAttribute(): ?string
    {
        return static::resolveImageUrl($this->featured_image_thumbnail ?: $this->featured_image);
    }

    public function getFeaturedImagePopupUrlAttribute(): ?string
    {
        return static::resolveImageUrl($this->featured_image_popup ?: $this->featured_image);
    }

    public function getFeaturedImageSingleUrlAttribute(): ?string
    {
        return static::resolveImageUrl($this->featured_image_single ?: $this->featured_image);
    }

    public function getFeaturedImageWebpUrlAttribute(): ?string
    {
        return static::resolveImageUrl($this->featured_image_webp);
    }

    public function getOgImageUrlAttribute(): ?string
    {
        return static::resolveImageUrl($this->og_image ?: $this->featured_image_single ?: $this->featured_image);
    }

    public function getExtraImageUrlsAttribute(): array
    {
        return collect($this->extra_images ?? [])
            ->map(function ($image): ?string {
                if (is_array($image)) {
                    $image = $image['path'] ?? $image['url'] ?? $image['src'] ?? null;
                }

                return is_string($image) ? static::resolveImageUrl($image) : null;
            })
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    public static function resolveImageUrl(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        if (filter_var($path, FILTER_VALIDATE_URL) !== false || Str::startsWith($path, ['//', 'data:'])) {
            return $path;
        }

        $normalizedPath = ltrim(str_replace('\\', '/', $path), '/');
        $frontendBaseUrl = static::frontendBaseUrl();

        if (static::isLegacyPublicAsset($normalizedPath)) {
            if (static::legacyPublicAssetPath($normalizedPath) !== null) {
                return url('/legacy-post-assets/' . $normalizedPath);
            }

            if (filled($frontendBaseUrl)) {
                return rtrim($frontendBaseUrl, '/') . '/' . $normalizedPath;
            }
        }

        if (Storage::disk('public')->exists($normalizedPath)) {
            return asset('storage/' . $normalizedPath);
        }

        if (is_file(public_path($normalizedPath))) {
            return asset($normalizedPath);
        }

        return asset($normalizedPath);
    }

    public static function legacyPublicAssetPath(string $path): ?string
    {
        $normalizedPath = ltrim(str_replace('\\', '/', $path), '/');

        if (! static::isLegacyPublicAsset($normalizedPath)) {
            return null;
        }

        $legacyBasePath = realpath(config('app.legacy_frontend_public_path'));

        if ($legacyBasePath === false) {
            return null;
        }

        $candidatePath = $legacyBasePath . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $normalizedPath);
        $resolvedPath = realpath($candidatePath);

        if ($resolvedPath === false || ! str_starts_with($resolvedPath, $legacyBasePath . DIRECTORY_SEPARATOR) || ! is_file($resolvedPath)) {
            return null;
        }

        return $resolvedPath;
    }

    protected static function isLegacyPublicAsset(string $path): bool
    {
        return Str::startsWith($path, 'news-assets/');
    }

    protected static function frontendBaseUrl(): ?string
    {
        return app()->environment('production')
            ? config('app.frontend_prod_url')
            : config('app.frontend_url');
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Post $post) {
            if (empty($post->slug)) {
                $post->slug = Str::slug($post->title);
            }
            if (empty($post->excerpt) && $post->content) {
                $post->excerpt = Str::limit(strip_tags($post->content), 200);
            }
        });
    }
}
