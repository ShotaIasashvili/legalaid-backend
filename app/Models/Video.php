<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $fillable = [
        'title',
        'description',
        'youtube_id',
        'youtube_url',
        'thumbnail',
        'category',
        'is_active',
        'published_at',
        'sort_order',
    ];

    protected $casts = [
        'published_at' => 'date',
        'is_active'    => 'boolean',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::saving(function (Video $video) {
            // Always re-extract YouTube ID from URL if URL is provided
            if ($video->youtube_url) {
                preg_match(
                    '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/',
                    $video->youtube_url,
                    $matches
                );
                if (!empty($matches[1])) {
                    $video->youtube_id = $matches[1];
                }
            }
            // Always regenerate thumbnail from YouTube ID
            if ($video->youtube_id) {
                $video->thumbnail = "https://img.youtube.com/vi/{$video->youtube_id}/maxresdefault.jpg";
            }
        });
    }
}
