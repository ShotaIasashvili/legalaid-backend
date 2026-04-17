<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class LegacyPostAssetController extends Controller
{
    public function show(string $path): BinaryFileResponse
    {
        $assetPath = Post::legacyPublicAssetPath($path);

        abort_unless($assetPath !== null, 404);

        return response()->file($assetPath, [
            'Cache-Control' => 'public, max-age=604800',
        ]);
    }
}