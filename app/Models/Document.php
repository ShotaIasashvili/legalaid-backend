<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'file_path',
        'file_name',
        'file_size',
        'file_type',
        'type',
        'badge',
        'category',
        'issued_at',
        'issuer',
        'is_active',
        'sort_order',
        'download_count',
    ];

    protected $casts = [
        'issued_at'      => 'date',
        'is_active'      => 'boolean',
        'download_count' => 'integer',
    ];

    public function incrementDownloads(): void
    {
        $this->increment('download_count');
    }
}
