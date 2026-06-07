<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Journal extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'cover_image',
        'cover_image_thumbnail',
        'file_path',
        'year',
        'volume',
        'issue_number',
        'published_at',
        'is_active',
        'sort_order',
        'download_count',
    ];

    protected $casts = [
        'published_at'   => 'date',
        'is_active'      => 'boolean',
        'download_count' => 'integer',
    ];

    public function incrementDownloads(): void
    {
        $this->increment('download_count');
    }
}
