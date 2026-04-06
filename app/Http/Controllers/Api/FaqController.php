<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\LegalQuestion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function faqs(Request $request): JsonResponse
    {
        $query = Faq::where('is_active', true)->orderBy('sort_order');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        return response()->json($query->get(['id', 'question', 'answer_html', 'answer_text', 'category']));
    }

    public function faqCategories(): JsonResponse
    {
        return response()->json(
            Faq::where('is_active', true)->distinct()->pluck('category')->filter()->values()
        );
    }

    public function legalQuestions(Request $request): JsonResponse
    {
        $query = LegalQuestion::where('is_active', true)->orderBy('sort_order');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        return response()->json($query->get(['id', 'question', 'answer_html', 'answer_text', 'category']));
    }

    public function legalQuestionCategories(): JsonResponse
    {
        return response()->json(
            LegalQuestion::where('is_active', true)->distinct()->pluck('category')->filter()->values()
        );
    }
}
