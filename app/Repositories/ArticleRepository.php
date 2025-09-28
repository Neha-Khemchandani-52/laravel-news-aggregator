<?php

namespace App\Repositories;

use App\Models\Article;
use App\Models\Author;
use App\Models\Category;

/**
 * Class ArticleRepository
 *
 * Handles persistence of articles in the database.
 * - Deduplicates using a fingerprint (unique hash).
 * - Creates/associates authors and categories if provided.
 * - Saves normalized article payloads from providers.
 *
 * This abstraction ensures controllers and jobs do not directly deal
 * with Eloquent queries, following the Repository pattern (SOLID).
 */

class ArticleRepository
{
    /**
     * Save or update an article in DB.
     * Deduplicates using fingerprint and syncs categories/authors.
     */
    public function upsertFromPayload(array $payload): Article
    {
        // Step 1: Handle author if present

        $authorId = null;

        if (!empty($payload['author'])) {
            // If API provided an author
            $author = Author::firstOrCreate(['name' => $payload['author']]);
            $authorId = $author->id;
        } else {
            // Fallback: use the organization (source name)
            $sourceName = $payload['source_name'] ?? null;
            if ($sourceName) {
                $author = Author::firstOrCreate(['name' => $sourceName]);
                $authorId = $author->id;
            } else {
                // Optional: fallback to "Anonymous"
                $author = Author::firstOrCreate(['name' => 'Anonymous']);
                $authorId = $author->id;
            }
        }

        // Step 2: Upsert article
        // Fingerprint ensures we don't insert duplicate articles
        $article = Article::updateOrCreate(
            ['fingerprint' => $payload['fingerprint']],
            [
                'title'        => $payload['title'],
                'description'  => $payload['description'] ?? null,
                'content'      => $payload['content'] ?? null,
                'url'          => $payload['url'] ?? null,
                'url_to_image' => $payload['url_to_image'] ?? null,
                'published_at' => $payload['published_at'] ?? null,
                'raw'          => $payload['raw'] ?? null,
                'source_id'    => $payload['source_id'],
                'author_id'    => $authorId,   // now linked to author
            ]
        );

        // Step 3: Handle categories if present

        if (!empty($payload['categories'])) {
            // Provider gave categories, insert them
            $categoryIds = [];
            foreach ($payload['categories'] as $catName) {
                $category = Category::firstOrCreate(['name' => $catName]);
                $categoryIds[] = $category->id;
            }
            $article->categories()->syncWithoutDetaching($categoryIds);
        } else {
            // Fallback: derive from source or set as 'General'
            $fallbackCategory = $payload['source_category'] ?? 'General';
            $category = Category::firstOrCreate(['name' => $fallbackCategory]);
            $article->categories()->syncWithoutDetaching([$category->id]);
        }

        return $article;
    }
}