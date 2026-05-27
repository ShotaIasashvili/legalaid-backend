<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class LegacyPostAssetController extends Controller
{
    public function show(string $path): BinaryFileResponse
    {
        return $this->serveAsset($path);

    }

    public function showNewsAsset(string $path): BinaryFileResponse
    {
        return $this->serveAsset('news-assets/' . ltrim($path, '/'));
    }

    private function serveAsset(string $path): BinaryFileResponse
    {
        $assetPath = $this->resolveAssetPath($path);

        abort_unless($assetPath !== null, 404);

        return response()->file($assetPath, [
            'Cache-Control' => 'public, max-age=604800',
        ]);
    }

    private function resolveAssetPath(string $path): ?string
    {
        $normalizedPath = ltrim(str_replace('\\', '/', $path), '/');

        $legacyPath = Post::legacyPublicAssetPath($normalizedPath);

        if ($legacyPath !== null) {
            return $legacyPath;
        }

        $candidateRoots = array_filter([
            dirname(base_path()),
            public_path(),
        ]);

        foreach ($candidateRoots as $root) {
            $resolvedRoot = realpath($root);

            if ($resolvedRoot === false) {
                continue;
            }

            $candidatePath = $resolvedRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $normalizedPath);
            $resolvedPath = realpath($candidatePath);

            if ($resolvedPath === false || ! str_starts_with($resolvedPath, $resolvedRoot . DIRECTORY_SEPARATOR) || ! is_file($resolvedPath)) {
                continue;
            }

            return $resolvedPath;
        }

        if (Str::startsWith($normalizedPath, 'news-assets/')) {
            $publicRelativePath = Str::after($normalizedPath, 'news-assets/');
            $publicAssetPath = public_path('news-assets/' . $publicRelativePath);

            if (is_file($publicAssetPath)) {
                return $publicAssetPath;
            }
        }

        return null;
    }
}