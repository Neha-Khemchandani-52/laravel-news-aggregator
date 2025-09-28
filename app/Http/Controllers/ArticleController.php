<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use App\Http\Resources\ArticleResource;
use Illuminate\Support\Facades\Cache;

/**
 * Class ArticleController
 *
 * Handles read-only access to articles via REST API.
 *
 * Endpoints:
 * ----------
 * - GET /api/articles
 *   Supports filters: search (q), source, author, category, date.
 *   Returns paginated results with Eloquent relationships eager loaded.
 */

class ArticleController extends Controller
{
    /**
     * Display a paginated list of articles with filters.
     *
     * Example requests:
     * -----------------
     * - /api/articles?q=bitcoin
     * - /api/articles?source=guardian
     * - /api/articles?category=Technology
     * - /api/articles?from=2025-09-01&to=2025-09-28
     **/
    public function index(Request $request)
    {
        $request->validate([
            'q' => 'nullable|string|max:255',
            'from' => 'nullable|date',
            'to' => 'nullable|date',
            'source' => 'nullable|string',
            'category' => 'nullable|string',
            'author' => 'nullable|string',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        $perPage = $request->input('per_page', 100);
        $query = Article::with(['source','author','categories']);

        if ($q = $request->input('q')) {
            $query->where(function($qb) use ($q) {
                $qb->where('title', 'like', "%{$q}%")
                   ->orWhere('description', 'like', "%{$q}%")
                   ->orWhere('content', 'like', "%{$q}%");
            });
        }

        if ($request->filled('from')) {
            $query->where('published_at', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->where('published_at', '<=', $request->to);
        }

        if ($request->filled('source')) {
            $query->whereHas('source', fn($s) => $s->where('slug', $request->source));
        }

        if ($request->filled('category')) {
            $query->whereHas('categories', fn($c) => $c->where('name', $request->category));
        }

        if ($request->filled('author')) {
            $query->whereHas('author', fn($a) => $a->where('name', $request->author));
        }

        $cacheKey = 'articles:'.md5(serialize($request->all()));

        $results = Cache::remember($cacheKey, 60, fn() =>
            $query->orderByDesc('published_at')->paginate($perPage)
        );

        return ArticleResource::collection($results);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Article $article)
    {
        return new ArticleResource($article->load(['source','author','categories']));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
