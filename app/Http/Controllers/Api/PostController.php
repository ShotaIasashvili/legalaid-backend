<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Category;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function __construct(private readonly ImageService $imageService) {}

    /**
     * GET /api/posts
     * Supports: ?page=1&per_page=12&category=სიახლეები&search=query&featured=1
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
        $posts   = $query->paginate($perPage);

        return response()->json([
            'data'  => $posts->map(fn ($p) => $this->formatPost($p, false)),
            'meta'  => [
                'current_page' => $posts->currentPage(),
                'last_page'    => $posts->lastPage(),
                'total'        => $posts->total(),
                'per_page'     => $posts->perPage(),
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

        return response()->json($this->formatPost($post, true));
    }

    /**
     * GET /api/posts/latest?limit=5
     */
    public function latest(Request $request): JsonResponse
    {
        $limit = min((int) $request->get('limit', 5), 20);
        $posts = Post::with('categories')
            ->published()
            ->orderByDesc('published_at')
            ->limit($limit)
            ->get();

        return response()->json($posts->map(fn ($p) => $this->formatPost($p, false)));
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
            // Image URLs
            'featuredImage'          => $post->featured_image ? asset('storage/' . $post->featured_image) : null,
            'featuredImageThumbnail' => $post->featured_image_thumbnail ? asset('storage/' . $post->featured_image_thumbnail) : null,
            'featuredImagePopup'     => $post->featured_image_popup ? asset('storage/' . $post->featured_image_popup) : null,
            'featuredImageWebp'      => $post->featured_image_webp ? asset('storage/' . $post->featured_image_webp) : null,
        ];

        if ($full) {
            $data['content']        = $post->content;
            $data['extra_images']   = $post->extra_images ?? [];
            $data['seo_title']      = $post->seo_title;
            $data['seo_description'] = $post->seo_description;
            $data['og_image']       = $post->og_image ? asset('storage/' . $post->og_image) : null;
            $data['source_url']     = $post->source_url;
            $data['featuredImageSingle'] = $post->featured_image_single
                ? asset('storage/' . $post->featured_image_single) : null;
        }

        return $data;
    }
}
