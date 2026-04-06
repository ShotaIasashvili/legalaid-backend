<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

    public function download(int $id): StreamedResponse
    {
        $document = Document::findOrFail($id);
        $document->incrementDownloads();

        abort_unless(Storage::disk('public')->exists($document->file_path), 404, 'File not found.');

        return Storage::disk('public')->download(
            $document->file_path,
            $document->file_name ?? basename($document->file_path)
        );
    }
}
