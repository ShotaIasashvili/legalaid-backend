<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class DocumentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Document::where('is_active', true)->orderBy('sort_order');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        return response()->json($query->get()->map(fn ($d) => [
            'id'          => $d->id,
            'title'       => $d->title,
            'slug'        => $d->slug,
            'description' => $d->description,
            'badge'       => $d->badge,
            'type'        => $d->type,
            'category'    => $d->category,
            'file_type'   => $d->file_type,
            'file_size'   => $d->file_size,
            'issued_at'   => $d->issued_at?->format('Y-m-d'),
            'issuer'      => $d->issuer,
            'download_url' => route('api.documents.download', $d->id),
        ]));
    }

    public function download(int $id): Response
    {
        $document = Document::findOrFail($id);
        $document->incrementDownloads();

        $normalizedPath = $document->normalizedFilePath();

        if (filled($normalizedPath) && Storage::disk('public')->exists($normalizedPath)) {
            return response()->download(
                Storage::disk('public')->path($normalizedPath),
                $document->file_name ?? basename($normalizedPath)
            );
        }

        $legacyFilePath = $document->legacyPublicFilePath();

        if ($legacyFilePath !== null) {
            return response()->download(
                $legacyFilePath,
                $document->file_name ?? basename($legacyFilePath)
            );
        }

        $frontendHostedFileUrl = $document->frontendHostedFileUrl();

        if ($frontendHostedFileUrl !== null) {
            return redirect()->away($frontendHostedFileUrl);
        }

        abort(404, 'File not found.');
    }
}
