<?php

namespace App\Services\NewsProviders;
use Illuminate\Support\Facades\Http;
use App\Models\Source;
use Illuminate\Support\Facades\Log;

/**
 * Handles fetching articles from NewsAPI and mapping them into
 * our unified payload structure.
 */
class NewsApiProvider implements NewsProviderInterface {
    public function getProviderName(): string { return 'newsapi'; }

    public function fetch(array $params = []): array {
        $key = config('services.newsapi.key', env('NEWSAPI_KEY'));
        $query = array_merge(['pageSize'=>100,'page'=>1,'q'=>''], $params);
        $res = Http::withHeaders(['Authorization' => "Bearer {$key}"])
                   ->get('https://newsapi.org/v2/everything', $query);
        if(!$res->successful()) return [];
        return $res->json('articles', []);
    }

    public function mapToArticlePayload(array $raw, Source $source): array {

        Log::info("Inside mapToArticlePayload started for ".$source->slug);

        $title = $raw['title'] ?? null;
        $published = $raw['publishedAt'] ?? null;
        $fingerprint = md5(strtolower(trim($title ?? ''))."|".$published."|".$source->slug);
        return [
            'source_id'=>$source->id,
            'fingerprint'=>$fingerprint,
            'title'=>$title,
            'description'=>$raw['description'] ?? null,
            'content'=>$raw['content'] ?? null,
            'url'=>$raw['url'] ?? null,
            'url_to_image'=>$raw['urlToImage'] ?? null,
            'published_at'=>$published,
            'raw'=>$raw,
        ];
    }
}
