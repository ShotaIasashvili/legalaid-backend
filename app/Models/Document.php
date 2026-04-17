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

    public function normalizedFilePath(): ?string
    {
        if (blank($this->file_path)) {
            return null;
        }

        return ltrim(str_replace('\\', '/', $this->file_path), '/');
    }

    public function legacyPublicFilePath(): ?string
    {
        $normalizedPath = $this->normalizedFilePath();

        if (blank($normalizedPath)) {
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

    public function frontendHostedFileUrl(): ?string
    {
        if (blank($this->file_path)) {
            return null;
        }

        if (filter_var($this->file_path, FILTER_VALIDATE_URL) !== false) {
            return $this->file_path;
        }

        $normalizedPath = $this->normalizedFilePath();

        if (blank($normalizedPath)) {
            return null;
        }

        $frontendBaseUrl = app()->environment('production')
            ? config('app.frontend_prod_url')
            : config('app.frontend_url');

        if (blank($frontendBaseUrl)) {
            return null;
        }

        return rtrim((string) $frontendBaseUrl, '/') . '/' . $normalizedPath;
    }
}
