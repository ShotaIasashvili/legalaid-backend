<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MediaLibrary;
use App\Services\ImageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    public function __construct(private readonly ImageService $imageService) {}

    /**
     * POST /api/media/upload
     * Upload an image, auto-generate all sizes + WebP variants.
     */
    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'image'  => 'required|file',
            'folder' => 'nullable|string|max:50',
            'alt'    => 'nullable|string|max:255',
            'caption' => 'nullable|string|max:500',
        ]);

        $file = $request->file('image');
        $this->imageService->validate($file);

        $folder = $request->get('folder', 'general');
        $paths  = $this->imageService->processUpload($file, $folder);

        $media = MediaLibrary::create([
            'original_name'       => $file->getClientOriginalName(),
            'file_path'           => $paths['original'],
            'thumbnail_path'      => $paths['thumbnail'] ?? null,
            'popup_path'          => $paths['popup'] ?? null,
            'webp_path'           => $paths['original_webp'] ?? null,
            'thumbnail_webp_path' => $paths['thumbnail_webp'] ?? null,
            'mime_type'           => $file->getMimeType(),
            'file_size'           => $file->getSize(),
            'alt'                 => $request->get('alt'),
            'caption'             => $request->get('caption'),
            'folder'              => $folder,
        ]);

        return response()->json([
            'id'            => $media->id,
            'url'           => asset('storage/' . $media->file_path),
            'thumbnail_url' => $media->thumbnail_path ? asset('storage/' . $media->thumbnail_path) : null,
            'popup_url'     => $media->popup_path ? asset('storage/' . $media->popup_path) : null,
            'webp_url'      => $media->webp_path ? asset('storage/' . $media->webp_path) : null,
        ], 201);
    }

    /**
     * GET /api/media
     */
    public function index(Request $request): JsonResponse
    {
        $query = MediaLibrary::orderByDesc('created_at');

        if ($request->filled('folder')) {
            $query->where('folder', $request->folder);
        }

        $media = $query->paginate(50);

        return response()->json([
            'data' => $media->map(fn ($m) => [
                'id'            => $m->id,
                'original_name' => $m->original_name,
                'url'           => asset('storage/' . $m->file_path),
                'thumbnail_url' => $m->thumbnail_path ? asset('storage/' . $m->thumbnail_path) : null,
                'popup_url'     => $m->popup_path ? asset('storage/' . $m->popup_path) : null,
                'alt'           => $m->alt,
                'caption'       => $m->caption,
                'folder'        => $m->folder,
                'created_at'    => $m->created_at->toDateTimeString(),
            ]),
            'meta' => [
                'total'        => $media->total(),
                'current_page' => $media->currentPage(),
                'last_page'    => $media->lastPage(),
            ],
        ]);
    }

    /**
     * DELETE /api/media/{id}
     */
    public function destroy(int $id): JsonResponse
    {
        $media = MediaLibrary::findOrFail($id);
        $this->imageService->deleteAll($media->file_path);
        $media->delete();

        return response()->json(['message' => 'Deleted.']);
    }
}
