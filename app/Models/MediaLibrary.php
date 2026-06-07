<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaLibrary extends Model
{
    protected $table = 'media_library';

    protected $fillable = [
        'original_name',
        'file_path',
        'thumbnail_path',
        'popup_path',
        'webp_path',
        'thumbnail_webp_path',
        'mime_type',
        'file_size',
        'width',
        'height',
        'alt',
        'caption',
        'folder',
    ];

    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        return $this->thumbnail_path ? asset('storage/' . $this->thumbnail_path) : null;
    }
}
