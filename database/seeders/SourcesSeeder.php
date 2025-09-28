<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Source;

/**
 * Seeds the sources table with initial news providers:
 * - NewsAPI
 * - The Guardian
 * - New York Times
 */

class SourcesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // You can use Eloquent models or DB facade to insert data into the sources table
        // Example using Eloquent model (assuming you have a Source model)
        $sources = [
            [
                'name'     => 'NewsAPI',
                'slug'     => 'newsapi',
                'source-provider' => 'newsapi',
                'meta'     => json_encode(['url' => 'https://newsapi.org/v2/everything']),
            ],
            [
                'name'     => 'The Guardian',
                'slug'     => 'guardian',
                'source-provider' => 'guardian',
                'meta'     => json_encode(['url' => 'https://content.guardianapis.com/search']),
            ],
            [
                'name'     => 'New York Times',
                'slug'     => 'nyt',
                'source-provider' => 'nyt',
                'meta'     => json_encode(['url' => 'https://api.nytimes.com/svc/search/v2/articlesearch.json']),
            ],
        ];

        foreach ($sources as $src) {
            Source::updateOrCreate(
                ['slug' => $src['slug']], // avoid duplicates
                $src
            );
        }
    }
}
