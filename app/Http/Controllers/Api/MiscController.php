<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Journal;
use App\Models\Vacancy;
use App\Models\Video;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MiscController extends Controller
{
    // ── Journals ─────────────────────────────────────────────────────────────

    public function journals(Request $request): JsonResponse
    {
        $journals = Journal::where('is_active', true)
            ->orderByDesc('published_at')
            ->orderBy('sort_order')
            ->get()
            ->map(fn ($j) => [
                'id'              => $j->id,
                'title'           => $j->title,
                'slug'            => $j->slug,
                'description'     => $j->description,
                'cover_image'     => $j->cover_image ? asset('storage/' . $j->cover_image) : null,
                'cover_thumbnail' => $j->cover_image_thumbnail ? asset('storage/' . $j->cover_image_thumbnail) : null,
                'year'            => $j->year,
                'volume'          => $j->volume,
                'issue_number'    => $j->issue_number,
                'published_at'    => $j->published_at?->format('Y-m-d'),
                'file_url'        => $j->file_path ? route('api.journals.download', $j->id) : null,
                'download_count'  => $j->download_count,
            ]);

        return response()->json($journals);
    }

    public function downloadJournal(int $id)
    {
        $journal = Journal::findOrFail($id);
        $journal->incrementDownloads();
        abort_unless(Storage::disk('public')->exists($journal->file_path), 404, 'File not found.');
        return Storage::disk('public')->download($journal->file_path, "{$journal->title}.pdf");
    }

    // ── Vacancies ─────────────────────────────────────────────────────────────

    public function vacancies(Request $request): JsonResponse
    {
        $query = Vacancy::published()->orderBy('sort_order');

        return response()->json([
            'data' => $query->get()->map(fn ($v) => [
                'id'                => $v->id,
                'title'             => $v->title,
                'slug'              => $v->slug,
                'excerpt'           => $v->excerpt,
                'content'           => $v->content,
                'department'        => $v->department,
                'location'          => $v->location,
                'type'              => $v->type,
                'status'            => $v->status,
                'deadline'          => $v->deadline?->format('Y-m-d'),
                'publish_starts_at' => $v->publish_starts_at?->timezone('Asia/Tbilisi')->format('Y-m-d H:i'),
                'publish_ends_at'   => $v->publish_ends_at?->timezone('Asia/Tbilisi')->format('Y-m-d H:i'),
                'requirements'      => $v->requirements ?? [],
                'responsibilities'  => $v->responsibilities ?? [],
                'contact_email'     => $v->contact_email,
                'application_url'   => $v->application_url,
            ]),
        ]);
    }

    public function vacancy(string $slug): JsonResponse
    {
        $vacancy = Vacancy::published()->where('slug', $slug)->firstOrFail();

        return response()->json(['data' => [
            'id'                => $vacancy->id,
            'title'             => $vacancy->title,
            'slug'              => $vacancy->slug,
            'excerpt'           => $vacancy->excerpt,
            'content'           => $vacancy->content,
            'department'        => $vacancy->department,
            'location'          => $vacancy->location,
            'type'              => $vacancy->type,
            'status'            => $vacancy->status,
            'deadline'          => $vacancy->deadline?->format('Y-m-d'),
            'publish_starts_at' => $vacancy->publish_starts_at?->timezone('Asia/Tbilisi')->format('Y-m-d H:i'),
            'publish_ends_at'   => $vacancy->publish_ends_at?->timezone('Asia/Tbilisi')->format('Y-m-d H:i'),
            'requirements'      => $vacancy->requirements ?? [],
            'responsibilities'  => $vacancy->responsibilities ?? [],
            'contact_email'     => $vacancy->contact_email,
            'application_url'   => $vacancy->application_url,
        ]]);
    }

    // ── Videos ───────────────────────────────────────────────────────────────

    public function videos(Request $request): JsonResponse
    {
        $query = Video::where('is_active', true)->orderBy('sort_order');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        return response()->json($query->get(['id', 'title', 'description', 'youtube_id', 'youtube_url', 'thumbnail', 'category', 'published_at']));
    }

    // ── Projects ─────────────────────────────────────────────────────────────

    public function projects(Request $request): JsonResponse
    {
        $query = Project::where('is_active', true)->orderBy('sort_order');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return response()->json($query->get()->map(fn ($p) => [
            'id'              => $p->id,
            'title'           => $p->title,
            'slug'            => $p->slug,
            'description'     => $p->description,
            'featured_image'  => $p->featured_image ? asset('storage/' . $p->featured_image) : null,
            'partner'         => $p->partner,
            'donor'           => $p->donor,
            'start_date'      => $p->start_date?->format('Y-m-d'),
            'end_date'        => $p->end_date?->format('Y-m-d'),
            'status'          => $p->status,
        ]));
    }

    public function project(string $slug): JsonResponse
    {
        $project = Project::where('slug', $slug)->where('is_active', true)->firstOrFail();

        return response()->json([
            'id'             => $project->id,
            'title'          => $project->title,
            'slug'           => $project->slug,
            'description'    => $project->description,
            'content'        => $project->content,
            'featured_image' => $project->featured_image ? asset('storage/' . $project->featured_image) : null,
            'partner'        => $project->partner,
            'donor'          => $project->donor,
            'start_date'     => $project->start_date?->format('Y-m-d'),
            'end_date'       => $project->end_date?->format('Y-m-d'),
            'status'         => $project->status,
        ]);
    }
}
