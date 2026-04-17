<?php

use App\Http\Controllers\LegacyPostAssetController;
use App\Http\Controllers\PublicStorageController;
use Illuminate\Support\Facades\Route;

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

Route::get('/storage/{path}', [PublicStorageController::class, 'show'])
    ->where('path', '.*')
    ->name('public-storage.show');
