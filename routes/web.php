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

Route::get('/generated-post-thumbnails/{postId}.svg', function (int $postId) {
    $post = Post::withTrashed()->findOrFail($postId);
    $title = trim($post->title ?: 'სიახლე');
    $words = preg_split('/\s+/u', $title) ?: [];
    $lines = [];
    $currentLine = '';

    foreach ($words as $word) {
        $candidate = trim($currentLine . ' ' . $word);

        if (mb_strlen($candidate) > 34 && $currentLine !== '') {
            $lines[] = $currentLine;
            $currentLine = $word;
            continue;
        }

        $currentLine = $candidate;
    }

    if ($currentLine !== '') {
        $lines[] = $currentLine;
    }

    $lines = array_slice($lines ?: [$title], 0, 4);
    $startY = 330 - ((count($lines) - 1) * 36);
    $text = collect($lines)
        ->map(fn (string $line, int $index): string => '<text x="96" y="' . ($startY + ($index * 72)) . '" font-size="52" font-weight="700" fill="#ffffff">' . e($line) . '</text>')
        ->implode("\n");
    $escapedTitle = e($title);

    $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="1200" height="750" viewBox="0 0 1200 750" role="img" aria-label="{$escapedTitle}">
  <defs>
    <linearGradient id="bg" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="#941613"/>
      <stop offset="58%" stop-color="#24324a"/>
      <stop offset="100%" stop-color="#0f172a"/>
    </linearGradient>
  </defs>
  <rect width="1200" height="750" fill="url(#bg)"/>
  <rect x="64" y="64" width="1072" height="622" rx="28" fill="rgba(255,255,255,0.08)" stroke="rgba(255,255,255,0.22)" stroke-width="2"/>
  <text x="96" y="150" font-size="28" font-weight="700" fill="rgba(255,255,255,0.72)">იურიდიული დახმარების სამსახური</text>
  {$text}
  <rect x="96" y="610" width="160" height="8" rx="4" fill="#ffffff" opacity="0.72"/>
</svg>
SVG;

    return response($svg, 200)
        ->header('Content-Type', 'image/svg+xml; charset=UTF-8')
        ->header('Cache-Control', 'public, max-age=86400');
})->whereNumber('postId');

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
