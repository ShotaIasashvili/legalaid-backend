<?php

use App\Http\Controllers\Api\ContentController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\FaqController;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\MiscController;
use App\Http\Controllers\Api\OfficeController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\StaffController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public API Routes  (no auth required — read-only front-end data)
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // ── News / Posts ──────────────────────────────────────────────────────
    Route::get('/posts',          [PostController::class, 'index']);
    Route::get('/posts/latest',   [PostController::class, 'latest']);
    Route::get('/posts/{slug}',   [PostController::class, 'show']);

    // ── Services ──────────────────────────────────────────────────────────
    Route::get('/services',             [ServiceController::class, 'index']);
    Route::get('/services/categories',  [ServiceController::class, 'categories']);
    Route::get('/services/{slug}',      [ServiceController::class, 'show']);

    // ── FAQ & Legal Questions ─────────────────────────────────────────────
    Route::get('/faqs',                        [FaqController::class, 'faqs']);
    Route::get('/faqs/categories',             [FaqController::class, 'faqCategories']);
    Route::get('/legal-questions',             [FaqController::class, 'legalQuestions']);
    Route::get('/legal-questions/categories',  [FaqController::class, 'legalQuestionCategories']);

    // ── Documents / PDFs ─────────────────────────────────────────────────
    Route::get('/documents',          [DocumentController::class, 'index']);
    Route::get('/documents/{id}/download',  [DocumentController::class, 'download'])
         ->name('api.documents.download');

    // ── Staff ─────────────────────────────────────────────────────────────
    Route::get('/staff',      [StaffController::class, 'index']);
    Route::get('/staff/{id}', [StaffController::class, 'show']);

    // ── Offices ───────────────────────────────────────────────────────────
    Route::get('/offices',         [OfficeController::class, 'index']);
    Route::get('/offices/regions', [OfficeController::class, 'regions']);

    // ── Journals ──────────────────────────────────────────────────────────
    Route::get('/journals',                    [MiscController::class, 'journals']);
    Route::get('/journals/{id}/download',      [MiscController::class, 'downloadJournal'])
         ->name('api.journals.download');

    // ── Vacancies ─────────────────────────────────────────────────────────
    Route::get('/vacancies',        [MiscController::class, 'vacancies']);
    Route::get('/vacancies/{slug}', [MiscController::class, 'vacancy']);

    // ── Videos ───────────────────────────────────────────────────────────
    Route::get('/videos', [MiscController::class, 'videos']);

    // ── Projects ─────────────────────────────────────────────────────────
    Route::get('/projects',        [MiscController::class, 'projects']);
    Route::get('/projects/{slug}', [MiscController::class, 'project']);

    // ── Page Content & Settings ───────────────────────────────────────────
    Route::get('/content/{page}', [ContentController::class, 'page']);
    Route::get('/settings',       [ContentController::class, 'settings']);
    Route::get('/stats',          [ContentController::class, 'stats']);
    Route::get('/search',         [ContentController::class, 'search']);

    /*
    |----------------------------------------------------------------------
    | Protected Routes (require Sanctum token — admin/editor)
    |----------------------------------------------------------------------
    */
    Route::middleware('auth:sanctum')->group(function () {

        Route::get('/user', fn (Request $request) => $request->user());

        // Media Library
        Route::post('/media/upload', [MediaController::class, 'upload']);
        Route::get('/media',         [MediaController::class, 'index']);
        Route::delete('/media/{id}', [MediaController::class, 'destroy']);
    });
});

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/
Route::post('/auth/login', function (Request $request) {
    $request->validate(['email' => 'required|email', 'password' => 'required']);

    $user = \App\Models\User::where('email', $request->email)->first();

    if (!$user || !\Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'Invalid credentials.'], 401);
    }

    $token = $user->createToken('api-token', ['*'], now()->addDays(30))->plainTextToken;

    return response()->json(['token' => $token, 'user' => ['id' => $user->id, 'name' => $user->name, 'email' => $user->email]]);
});

Route::middleware('auth:sanctum')->post('/auth/logout', function (Request $request) {
    $request->user()->currentAccessToken()->delete();
    return response()->json(['message' => 'Logged out.']);
});
