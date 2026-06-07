<?php

use App\Http\Controllers\InstallController;
use App\Http\Controllers\LegacyPostAssetController;
use App\Http\Controllers\PublicStorageController;
use Illuminate\Support\Facades\Route;

Route::get('/install', [InstallController::class, 'show'])->name('install.show');
Route::post('/install', [InstallController::class, 'install'])->name('install.run');

Route::get('/', function () {
    return redirect('/admin');
});

// Health check
Route::get('/up', function () {
    return response()->json(['status' => 'ok', 'time' => now()->toIso8601String()]);
});

Route::get('/legacy-post-assets/{path}', [LegacyPostAssetController::class, 'show'])
    ->where('path', '.*')
    ->name('legacy-post-assets.show');

Route::get('/news-assets/{path}', [LegacyPostAssetController::class, 'showNewsAsset'])
    ->where('path', '.*')
    ->name('news-assets.show');

Route::get('/storage/{path}', [PublicStorageController::class, 'show'])
    ->where('path', '.*')
    ->name('public-storage.show');
