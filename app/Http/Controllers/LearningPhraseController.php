<?php

namespace App\Http\Controllers;

use App\Models\LearningPhrase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LearningPhraseController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string'],
            'english_level' => ['required', 'string', 'in:A1,A2,B1,B2,C1,C2'],
        ]);

        $phrases = LearningPhrase::query()
            ->where('episode_title', $validated['title'])
            ->where('english_level', $validated['english_level'])
            ->orderBy('id')
            ->get([
                'id',
                'episode_title',
                'english_level',
                'phrase',
                'translation',
                'context_sentence',
                'explanation',
                'created_at',
            ]);

        return response()->json([
            'title' => $validated['title'],
            'english_level' => $validated['english_level'],
            'count' => $phrases->count(),
            'data' => $phrases,
        ]);
    }
}
