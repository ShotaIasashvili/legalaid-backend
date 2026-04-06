<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Service::where('is_active', true)->orderBy('sort_order');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $services = $query->get();

        return response()->json($services->map(fn ($s) => [
            'id'          => $s->id,
            'title'       => $s->title,
            'subtitle'    => $s->subtitle,
            'slug'        => $s->slug,
            'description' => $s->description,
            'icon'        => $s->icon,
            'category'    => $s->category,
            'color'       => $s->color,
        ]));
    }

    public function show(string $slug): JsonResponse
    {
        $service = Service::where('slug', $slug)->where('is_active', true)->firstOrFail();

        return response()->json([
            'id'                             => $service->id,
            'title'                          => $service->title,
            'subtitle'                       => $service->subtitle,
            'slug'                           => $service->slug,
            'description'                    => $service->description,
            'full_content'                   => $service->full_content,
            'icon'                           => $service->icon,
            'category'                       => $service->category,
            'color'                          => $service->color,
            'requirements'                   => $service->requirements ?? [],
            'how_to_apply'                   => $service->how_to_apply ?? [],
            'related_services'               => $service->related_services ?? [],
            'special_eligibility_categories' => $service->special_eligibility_categories ?? [],
            'download_links'                 => $service->download_links ?? [],
            'featured_image'                 => $service->featured_image ? asset('storage/' . $service->featured_image) : null,
        ]);
    }

    public function categories(): JsonResponse
    {
        $categories = Service::where('is_active', true)
            ->distinct()
            ->pluck('category')
            ->filter()
            ->values();

        return response()->json($categories);
    }
}
