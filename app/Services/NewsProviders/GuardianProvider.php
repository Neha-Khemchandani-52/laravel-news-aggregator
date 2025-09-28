<?php

namespace App\Services\NewsProviders;
use Illuminate\Support\Facades\Http;
use App\Models\Source;

// uses https://content.guardianapis.com/search?api-key=KEY&show-fields=all
class GuardianProvider implements NewsProviderInterface {
    public function getProviderName(): string { return 'guardian'; }
    public function fetch(array $params=[]): array {
        $key = config('services.guardian.key', env('GUARDIAN_KEY'));
        $query = array_merge(['page-size'=>50,'show-fields'=>'all','page'=>1], $params);
        $res = Http::get('https://content.guardianapis.com/search', array_merge($query, ['api-key'=>$key]));
        if(!$res->successful()) return [];
        return $res->json('response.results', []);
    }
    public function mapToArticlePayload(array $raw, Source $source): array {
        $fields = $raw['fields'] ?? [];
        $title = $raw['webTitle'] ?? null;
        $published = $raw['webPublicationDate'] ?? null;
        $fingerprint = md5(strtolower(trim($title ?? ''))."|".$published."|".$source->slug);
        return [
            'source_id'=>$source->id,
            'fingerprint'=>$fingerprint,
            'title'=>$title,
            'description'=>$fields['trailText'] ?? null,
            'content'=>$fields['bodyText'] ?? $fields['body'] ?? null,
            'url'=>$raw['webUrl'] ?? null,
            'url_to_image'=>$fields['thumbnail'] ?? null,
            'published_at'=>$published,
            'raw'=>$raw,
        ];
    }
}