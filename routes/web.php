<?php

use App\Http\Controllers\InstallController;
use App\Http\Controllers\LegacyPostAssetController;
use App\Http\Controllers\PublicStorageController;
use App\Models\Post;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

Route::get('/install', [InstallController::class, 'show'])->name('install.show');
Route::post('/install', [InstallController::class, 'install'])->name('install.run');

Route::get('/', function () {
    return redirect('/admin');
});

// Health check
Route::get('/up', function () {
    return response()->json(['status' => 'ok', 'time' => now()->toIso8601String()]);
});

Route::get('/news/{slug}', function (string $slug) {
    $indexPath = dirname(base_path()) . DIRECTORY_SEPARATOR . 'index.html';

    abort_unless(is_file($indexPath), 404);

    $html = file_get_contents($indexPath);

    try {
        $post = Post::published()->where('slug', $slug)->first();
    } catch (\Throwable) {
        $post = null;
    }

    if (! $post) {
        return response($html)->header('Content-Type', 'text/html; charset=UTF-8');
    }

    $title = e($post->seo_title ?: $post->title);
    $description = e(Str::limit(
        trim(preg_replace('/\s+/', ' ', strip_tags($post->seo_description ?: $post->excerpt ?: $post->content))),
        180,
        '',
    ));
    $url = e(url('/news/' . $post->slug));
    $image = e($post->og_image_url ?: $post->featured_image_single_url ?: $post->featured_image_url ?: asset('logo.svg'));

    $meta = <<<HTML
        <title>{$title} - იურიდიული დახმარება</title>
        <meta name="description" content="{$description}" />
        <meta property="og:title" content="{$title}" />
        <meta property="og:description" content="{$description}" />
        <meta property="og:type" content="article" />
        <meta property="og:url" content="{$url}" />
        <meta property="og:image" content="{$image}" />
        <meta property="og:site_name" content="იურიდიული დახმარება" />
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" content="{$title}" />
        <meta name="twitter:description" content="{$description}" />
        <meta name="twitter:image" content="{$image}" />
    HTML;

    $html = preg_replace('/<title>.*?<\/title>/is', '', $html, 1) ?? $html;
    $html = preg_replace('/\s*<meta\s+name=["\']description["\'][^>]*>\s*/i', "\n", $html) ?? $html;
    $html = preg_replace('/\s*<meta\s+property=["\']og:[^"\']+["\'][^>]*>\s*/i', "\n", $html) ?? $html;
    $html = preg_replace('/\s*<meta\s+name=["\']twitter:[^"\']+["\'][^>]*>\s*/i', "\n", $html) ?? $html;
    $html = str_replace('</head>', $meta . "\n</head>", $html);

    return response($html)->header('Content-Type', 'text/html; charset=UTF-8');
})->where('slug', '[^/]+');

Route::get('/legacy-post-assets/{path}', [LegacyPostAssetController::class, 'show'])
    ->where('path', '.*')
    ->name('legacy-post-assets.show');

Route::get('/news-assets/{path}', [LegacyPostAssetController::class, 'showNewsAsset'])
    ->where('path', '.*')
    ->name('news-assets.show');

Route::get('/storage/{path}', [PublicStorageController::class, 'show'])
    ->where('path', '.*')
    ->name('public-storage.show');
