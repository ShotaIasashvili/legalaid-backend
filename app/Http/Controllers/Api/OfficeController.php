<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Office;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OfficeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Office::where('is_active', true)->orderBy('sort_order');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('region')) {
            $query->where('region', $request->region);
        }

        return response()->json($query->get()->map(fn ($o) => [
            'id'           => $o->id,
            'name'         => $o->name,
            'type'         => $o->type,
            'region'       => $o->region,
            'city'         => $o->city,
            'address'      => $o->address,
            'phone'        => $o->phone,
            'email'        => $o->email,
            'working_hours' => $o->working_hours,
            'lat'          => $o->lat,
            'lng'          => $o->lng,
            'description'  => $o->description,
            'photo'        => $o->photo ? asset('storage/' . $o->photo) : null,
            'services'     => $o->services ?? [],
        ]));
    }

    public function regions(): JsonResponse
    {
        return response()->json(
            Office::where('is_active', true)->distinct()->pluck('region')->filter()->values()
        );
    }
}
