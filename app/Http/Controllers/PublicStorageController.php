<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PublicStorageController extends Controller
{
    public function show(string $path): BinaryFileResponse
    {
        $normalizedPath = ltrim(str_replace('\\', '/', $path), '/');

        abort_unless($normalizedPath !== '' && ! str_contains($normalizedPath, '../'), 404);

        $disk = Storage::disk('public');
        $publicRoot = realpath($disk->path(''));

        abort_unless($publicRoot !== false, 404);

        $resolvedPath = realpath($disk->path($normalizedPath));

        abort_unless(
            $resolvedPath !== false
                && str_starts_with($resolvedPath, $publicRoot . DIRECTORY_SEPARATOR)
                && is_file($resolvedPath),
            404,
        );

        return response()->file($resolvedPath, [
            'Cache-Control' => 'public, max-age=300, must-revalidate',
        ]);
    }
}
