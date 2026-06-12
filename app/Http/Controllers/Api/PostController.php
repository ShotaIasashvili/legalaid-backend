<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PostController extends Controller
{
    /**
     * GET /api/posts
     * Supports: ?page=1&per_page=12&category=სიახლეები&search=query&featured=1&include_content=1
     */
    public function index(Request $request): JsonResponse
    {
        $query = Post::with('categories')
            ->published()
            ->orderByDesc('published_at')
            ->orderByDesc('id');

        if ($request->filled('category')) {
            $query->whereHas('categories', fn ($q) => $q->where('name', $request->category));
        }

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(fn ($q) => $q->where('title', 'like', "%{$term}%")
                ->orWhere('excerpt', 'like', "%{$term}%"));
        }

        if ($request->boolean('featured')) {
            $query->where('is_featured', true);
        }

        $perPage = min((int) $request->get('per_page', 12), 100);
        $full = $request->boolean('include_content') || $request->boolean('full');
        $posts   = $query->paginate($perPage);

        return response()->json([
            'data'  => $posts->map(fn ($p) => $this->formatPost($p, $full))->values(),
            'meta'  => [
                'current_page' => $posts->currentPage(),
                'last_page'    => $posts->lastPage(),
                'total'        => $posts->total(),
                'per_page'     => $posts->perPage(),
                'from'         => $posts->firstItem(),
                'to'           => $posts->lastItem(),
            ],
        ]);
    }

    /**
     * GET /api/posts/{slug}
     */
    public function show(string $slug): JsonResponse
    {
        $post = Post::with('categories')
            ->where('slug', $slug)
            ->published()
            ->firstOrFail();

        $post->increment('views');

        return response()->json([
            'data' => $this->formatPost($post->refresh()->load('categories'), true),
        ]);
    }

    /**
     * GET /api/posts/latest?limit=5&category=სიახლეები
     */
    public function latest(Request $request): JsonResponse
    {
        $limit = min((int) $request->get('limit', 5), 20);
        $query = Post::with('categories')
            ->published()
            ->orderByDesc('published_at')
            ->orderByDesc('id');

        if ($request->filled('category')) {
            $query->whereHas('categories', fn ($q) => $q->where('name', $request->category));
        }

        $posts = $query
            ->limit($limit)
            ->get();

        return response()->json([
            'data' => $posts->map(fn ($p) => $this->formatPost($p, false))->values(),
        ]);
    }

    /**
     * GET /api/posts/archive
     *
     * Full public archive used by the React news list and article pages.
     */
    public function archive(Request $request): JsonResponse
    {
        $query = Post::with('categories')
            ->published()
            ->orderByDesc('published_at')
            ->orderByDesc('id');

        if ($request->filled('category')) {
            $query->whereHas('categories', fn ($q) => $q->where('name', $request->category));
        }

        return response()->json([
            'data' => $query->get()->map(fn ($p) => $this->formatPost($p, true))->values(),
        ]);
    }

    private function formatPost(Post $post, bool $full = true): array
    {
        $data = [
            'id'           => $post->id,
            'title'        => $post->title,
            'slug'         => $post->slug,
            'excerpt'      => $post->excerpt,
            'date'         => $post->published_at?->format('Y-m-d') ?? $post->created_at->format('Y-m-d'),
            'categories'   => $post->categories->pluck('name'),
            'author'       => $post->author,
            'is_featured'  => $post->is_featured,
            'views'        => $post->views,
            'sourceUrl'    => $post->source_url,
            // Image URLs
            'featuredImage'          => $post->featured_image_url,
            'featuredImageThumbnail' => $post->featured_image_thumbnail_url,
            'featuredImagePopup'     => $post->featured_image_popup_url,
            'featuredImageSingle'    => $post->featured_image_single_url,
            'featuredImageWebp'      => $post->featured_image_webp_url,
        ];

        if ($full) {
            $data['content']        = $post->content;
            $data['images']         = $post->extra_image_urls;
            $data['extra_images']   = $post->extra_image_urls;
            $data['seo_title']      = $post->seo_title;
            $data['seo_description'] = $post->seo_description;
            $data['og_image']       = $post->og_image_url;
            $data['source_url']     = $post->source_url;
        }

        return $data;
    }
}
