<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});

// Health check
Route::get('/up', function () {
    return response()->json(['status' => 'ok', 'time' => now()->toIso8601String()]);
});
