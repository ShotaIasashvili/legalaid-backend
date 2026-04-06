<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PageContent;
use App\Models\Setting;
use App\Models\Stat;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContentController extends Controller
{
    /**
     * GET /api/content/{page}
     * Returns all content blocks for a page.
     */
    public function page(string $page): JsonResponse
    {
        $content = PageContent::forPage($page);
        return response()->json($content);
    }

    /**
     * GET /api/settings
     * Returns public site settings.
     */
    public function settings(Request $request): JsonResponse
    {
        $group = $request->get('group', 'general');
        return response()->json(Setting::getGroup($group));
    }

    /**
     * GET /api/stats
     */
    public function stats(Request $request): JsonResponse
    {
        $group = $request->get('group', 'homepage');
        return response()->json(
            Stat::where('group', $group)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get(['id', 'label', 'value', 'suffix', 'icon', 'color'])
        );
    }

    /**
     * GET /api/search?q=query
     * Global search across posts, services, FAQs.
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate(['q' => 'required|string|min:2|max:100']);
        $term = $request->q;

        $posts = \App\Models\Post::published()
            ->where(fn ($q) => $q->where('title', 'like', "%{$term}%")->orWhere('excerpt', 'like', "%{$term}%"))
            ->limit(10)
            ->get(['id', 'title', 'slug', 'excerpt', 'published_at', 'featured_image_thumbnail']);

        $services = \App\Models\Service::where('is_active', true)
            ->where(fn ($q) => $q->where('title', 'like', "%{$term}%")->orWhere('description', 'like', "%{$term}%"))
            ->limit(5)
            ->get(['id', 'title', 'slug', 'description']);

        $faqs = \App\Models\Faq::where('is_active', true)
            ->where(fn ($q) => $q->where('question', 'like', "%{$term}%")->orWhere('answer_text', 'like', "%{$term}%"))
            ->limit(5)
            ->get(['id', 'question', 'answer_text', 'category']);

        return response()->json([
            'posts'    => $posts,
            'services' => $services,
            'faqs'     => $faqs,
        ]);
    }
}
