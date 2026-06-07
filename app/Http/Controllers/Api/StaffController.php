<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Staff::where('is_active', true)->orderBy('sort_order');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        return response()->json($query->get()->map(fn ($s) => [
            'id'            => $s->id,
            'name'          => $s->name,
            'position'      => $s->position,
            'department'    => $s->department,
            'type'          => $s->type,
            'bio'           => $s->bio,
            'photo'         => $s->photo ? asset('storage/' . $s->photo) : null,
            'photo_thumbnail' => $s->photo_thumbnail ? asset('storage/' . $s->photo_thumbnail) : null,
            'email'         => $s->email,
            'phone'         => $s->phone,
            'from_date'     => $s->from_date?->format('Y-m-d'),
            'to_date'       => $s->to_date?->format('Y-m-d'),
        ]));
    }

    public function show(int $id): JsonResponse
    {
        $staff = Staff::where('is_active', true)->findOrFail($id);

        return response()->json([
            'id'           => $staff->id,
            'name'         => $staff->name,
            'position'     => $staff->position,
            'department'   => $staff->department,
            'type'         => $staff->type,
            'bio'          => $staff->bio,
            'full_bio'     => $staff->full_bio,
            'photo'        => $staff->photo ? asset('storage/' . $staff->photo) : null,
            'photo_thumbnail' => $staff->photo_thumbnail ? asset('storage/' . $staff->photo_thumbnail) : null,
            'email'        => $staff->email,
            'phone'        => $staff->phone,
            'from_date'    => $staff->from_date?->format('Y-m-d'),
            'to_date'      => $staff->to_date?->format('Y-m-d'),
            'achievements' => $staff->achievements ?? [],
            'education'    => $staff->education ?? [],
            'career'       => $staff->career ?? [],
        ]);
    }
}
