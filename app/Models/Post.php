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

    public const DEFAULT_NEWS_CATEGORY = 'სიახლეები';

    public const IMAGE_FIELDS = [
        'featured_image',
        'featured_image_thumbnail',
        'featured_image_popup',
        'featured_image_single',
        'og_image',
        'featured_image_webp',
        'featured_image_thumbnail_webp',
    ];

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
        'video_url',
        'video_provider',
        'video_embed_url',
        'extra_images',
        'views',
        'sort_order',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_featured'  => 'boolean',
        'extra_images' => 'array',
    ];

    public function setExtraImagesAttribute(mixed $value): void
    {
        $images = static::normalizeImagePaths($value);

        $this->attributes['extra_images'] = $images === []
            ? null
            : json_encode($images, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

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
        return $this->firstResolvedImageUrl([
            $this->featured_image,
            $this->featured_image_single,
            $this->featured_image_popup,
            $this->featured_image_thumbnail,
            $this->og_image,
        ]) ?: $this->generated_post_thumbnail_url;
    }

    public function getFeaturedImageThumbnailUrlAttribute(): ?string
    {
        return $this->firstResolvedImageUrl([
            $this->featured_image_thumbnail,
            $this->featured_image,
            $this->featured_image_single,
            $this->featured_image_popup,
            $this->og_image,
        ]) ?: $this->generated_post_thumbnail_url;
    }

    public function getFeaturedImagePopupUrlAttribute(): ?string
    {
        return $this->firstResolvedImageUrl([
            $this->featured_image_popup,
            $this->featured_image_single,
            $this->featured_image,
            $this->featured_image_thumbnail,
            $this->og_image,
        ]) ?: $this->generated_post_thumbnail_url;
    }

    public function getFeaturedImageSingleUrlAttribute(): ?string
    {
        return $this->firstResolvedImageUrl([
            $this->featured_image_single,
            $this->featured_image,
            $this->featured_image_popup,
            $this->featured_image_thumbnail,
            $this->og_image,
        ]) ?: $this->generated_post_thumbnail_url;
    }

    public function getFeaturedImageWebpUrlAttribute(): ?string
    {
        return static::resolveImageUrl($this->featured_image_webp);
    }

    public function getOgImageUrlAttribute(): ?string
    {
        return $this->firstResolvedImageUrl([
            $this->og_image,
            $this->featured_image_single,
            $this->featured_image,
            $this->featured_image_popup,
            $this->featured_image_thumbnail,
        ]) ?: $this->generated_post_thumbnail_url;
    }

    public function getExtraImageUrlsAttribute(): array
    {
        return collect(static::normalizeImagePaths($this->extra_images ?? []))
            ->map(fn (string $image): ?string => static::resolveImageUrl($image))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    public function getGeneratedPostThumbnailUrlAttribute(): string
    {
        return url('/generated-post-thumbnails/' . $this->getKey() . '.svg');
    }

    public static function videoDataFromUrl(?string $url): array
    {
        $url = trim((string) $url);

        if ($url === '') {
            return [
                'video_url' => null,
                'video_provider' => null,
                'video_embed_url' => null,
            ];
        }

        if ($youtubeId = static::extractYoutubeId($url)) {
            return [
                'video_url' => $url,
                'video_provider' => 'youtube',
                'video_embed_url' => "https://www.youtube.com/embed/{$youtubeId}",
            ];
        }

        if (static::isFacebookVideoUrl($url)) {
            return [
                'video_url' => $url,
                'video_provider' => 'facebook',
                'video_embed_url' => 'https://www.facebook.com/plugins/video.php?href=' . rawurlencode($url) . '&show_text=false&width=734',
            ];
        }

        return [
            'video_url' => $url,
            'video_provider' => null,
            'video_embed_url' => null,
        ];
    }

    protected function firstResolvedImageUrl(array $paths): ?string
    {
        foreach ($paths as $path) {
            $url = static::resolveImageUrl($path);

            if ($url !== null) {
                return $url;
            }
        }

        return null;
    }

    public static function defaultNewsCategory(): Category
    {
        return Category::firstOrCreate(
            ['name' => static::DEFAULT_NEWS_CATEGORY, 'type' => 'news'],
            ['slug' => 'news']
        );
    }

    public function ensureDefaultNewsCategory(): void
    {
        if (! $this->exists || $this->categories()->exists()) {
            return;
        }

        $this->categories()->syncWithoutDetaching([static::defaultNewsCategory()->id]);
    }

    public static function normalizeImagePaths(mixed $value): array
    {
        if ($value === null || $value === '') {
            return [];
        }

        if ($value instanceof \Illuminate\Support\Collection) {
            $value = $value->all();
        }

        if (is_string($value)) {
            $path = trim($value);

            if ($path === '') {
                return [];
            }

            if (Str::startsWith($path, ['[', '{'])) {
                $decoded = json_decode($path, true);

                if (json_last_error() === JSON_ERROR_NONE) {
                    return static::normalizeImagePaths($decoded);
                }
            }

            return [static::normalizeStoredImagePath($path)];
        }

        if (! is_array($value)) {
            return [];
        }

        $paths = [];

        foreach ($value as $item) {
            if (is_array($item)) {
                $directPath = $item['path'] ?? $item['url'] ?? $item['src'] ?? $item['file'] ?? null;

                if ($directPath !== null) {
                    array_push($paths, ...static::normalizeImagePaths($directPath));
                    continue;
                }

                array_push($paths, ...static::normalizeImagePaths(array_values($item)));
                continue;
            }

            array_push($paths, ...static::normalizeImagePaths($item));
        }

        return collect($paths)
            ->map(fn (string $path): string => static::normalizeStoredImagePath($path))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    protected static function normalizeStoredImagePath(string $path): string
    {
        if (filter_var($path, FILTER_VALIDATE_URL) !== false || Str::startsWith($path, ['//', 'data:'])) {
            return $path;
        }

        return ltrim(str_replace('\\', '/', $path), '/');
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
            if (static::frontendPublicAssetPath($normalizedPath) !== null || static::legacyPublicAssetPath($normalizedPath) !== null) {
                return url('/' . $normalizedPath);
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

        if (static::frontendPublicAssetPath($normalizedPath) !== null) {
            return url('/' . $normalizedPath);
        }

        return null;
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

    public static function frontendPublicAssetPath(string $path): ?string
    {
        $normalizedPath = ltrim(str_replace('\\', '/', $path), '/');
        $candidateRoot = dirname(base_path());
        $resolvedRoot = realpath($candidateRoot);

        if ($resolvedRoot === false) {
            return null;
        }

        $candidatePath = $resolvedRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $normalizedPath);
        $resolvedPath = realpath($candidatePath);

        if ($resolvedPath === false || ! str_starts_with($resolvedPath, $resolvedRoot . DIRECTORY_SEPARATOR) || ! is_file($resolvedPath)) {
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

    protected static function extractYoutubeId(string $url): ?string
    {
        if (preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/|shorts\/)|youtu\.be\/)([A-Za-z0-9_-]{11})/i', $url, $matches)) {
            return $matches[1];
        }

        $query = parse_url($url, PHP_URL_QUERY);

        if (is_string($query)) {
            parse_str($query, $params);
            $id = $params['v'] ?? null;

            if (is_string($id) && preg_match('/^[A-Za-z0-9_-]{11}$/', $id)) {
                return $id;
            }
        }

        return null;
    }

    protected static function isFacebookVideoUrl(string $url): bool
    {
        $host = parse_url($url, PHP_URL_HOST);

        if (! is_string($host) || ! Str::contains($host, 'facebook.com')) {
            return false;
        }

        return Str::contains($url, ['/videos/', '/watch/', 'watch?v=', '/reel/']);
    }

    protected static function boot(): void
    {
        parent::boot();

        static::saving(function (Post $post) {
            if (empty($post->slug)) {
                $post->slug = Str::slug($post->title);
            }
            if (empty($post->excerpt) && $post->content) {
                $post->excerpt = Str::limit(strip_tags($post->content), 200);
            }

            if ($post->status === 'published' && blank($post->published_at)) {
                $post->published_at = now('Asia/Tbilisi');
            }

            $videoData = static::videoDataFromUrl($post->video_url);
            $post->video_url = $videoData['video_url'];
            $post->video_provider = $videoData['video_provider'];
            $post->video_embed_url = $videoData['video_embed_url'];

            if ($post->exists) {
                foreach (static::IMAGE_FIELDS as $field) {
                    if ($post->isDirty($field) && blank($post->{$field}) && filled($post->getOriginal($field))) {
                        $post->{$field} = $post->getOriginal($field);
                    }
                }

                $currentExtraImages = static::normalizeImagePaths($post->extra_images ?? []);
                $originalExtraImages = static::normalizeImagePaths($post->getOriginal('extra_images'));

                if ($post->isDirty('extra_images') && $currentExtraImages === [] && $originalExtraImages !== []) {
                    $post->extra_images = $originalExtraImages;
                }
            }
        });
    }
}
