<?php

namespace App\Services\NewsProviders;
use Illuminate\Support\Facades\Http;
use App\Models\Source;

// uses https://api.nytimes.com/svc/search/v2/articlesearch.json?api-key=KEY&q=...
class NytProvider implements NewsProviderInterface {
    public function getProviderName(): string { return 'nyt'; }
    public function fetch(array $params=[]): array {
        $key = config('services.nyt.key', env('NYT_KEY'));
        $query = array_merge(['page'=>0,'q'=>''], $params);
        $res = Http::get('https://api.nytimes.com/svc/search/v2/articlesearch.json', array_merge($query, ['api-key'=>$key]));
        if(!$res->successful()) return [];
        return $res->json('response.docs', []);
    }
    public function mapToArticlePayload(array $raw, Source $source): array {
        $title = $raw['headline']['main'] ?? null;
        $published = $raw['pub_date'] ?? null;
        $url = $raw['web_url'] ?? null;
        $fingerprint = md5(strtolower(trim($title ?? ''))."|".$published."|".$source->slug);
        return [
            'source_id' => $source->id,
            'fingerprint' => $fingerprint,
            'title'=>$title,
            'description'=>$raw['abstract'] ?? null,
            'content'=>$raw['lead_paragraph'] ?? null,
            'url'=>$url,
            'url_to_image'=>null,
            'published_at'=>$published,
            'raw'=>$raw,
        ];
    }
}