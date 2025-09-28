<?php

namespace App\Repositories;

use App\Models\UserPreference;
use App\Models\Source;
use App\Models\Category;
use App\Models\Author;

class UserPreferenceRepository
{
    public function savePreferences(int $userId, array $sources = [], array $categories = [], array $authors = [])
    {
        // Resolve sources
        $sourceIds = $this->resolveIds(Source::class, $sources);

        // Resolve categories
        $categoryIds = $this->resolveIds(Category::class, $categories);

        // Resolve authors
        $authorIds = $this->resolveIds(Author::class, $authors);

        return UserPreference::updateOrCreate(
            ['user_id' => $userId],
            [
                'preferred_sources' => $sourceIds,
                'preferred_categories' => $categoryIds,
                'preferred_authors' => $authorIds,
            ]
        );
    }

    public function getPreferences(int $userId): ?UserPreference
    {
        return UserPreference::where('user_id', $userId)->first();
    }

    public function getArticlesByPreferences(UserPreference $preference, int $perPage = 20)
    {
        $query = \App\Models\Article::query()->with(['source', 'author', 'categories']);

        if (!empty($preference->preferred_sources)) {
            $query->whereIn('source_id', $preference->preferred_sources);
        }

        if (!empty($preference->preferred_authors)) {
            $query->whereIn('author_id', $preference->preferred_authors);
        }

        if (!empty($preference->preferred_categories)) {
            $query->whereHas('categories', function ($q) use ($preference) {
                $q->whereIn('categories.id', $preference->preferred_categories);
            });
        }

        return $query->orderBy('published_at', 'desc')->paginate($perPage);
    }

    /**
     * Helper: Accepts IDs or names, returns array of IDs.
     */
    protected function resolveIds(string $modelClass, array $values): array
    {
        if (empty($values)) {
            return [];
        }

        // Separate numeric IDs and string names
        $ids = array_filter($values, fn($v) => is_numeric($v));
        $names = array_filter($values, fn($v) => is_string($v));

        $resolved = [];

        if (!empty($ids)) {
            $resolved = array_merge($resolved, $ids);
        }

        if (!empty($names)) {
            $resolved = array_merge(
                $resolved,
                $modelClass::whereIn('name', $names)->pluck('name')->toArray()
            );
        }

        return $resolved;
    }
}
