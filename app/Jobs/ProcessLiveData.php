<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\Source;
use App\Repositories\ArticleRepository;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

 /**
 * Job: ProcessLiveData Job
 *
 * Purpose:
 * --------
 * This job fetches live articles from a given news provider (e.g., NewsAPI, Guardian, NYT),
 * normalizes them into a unified payload, and persists them into the database.
 *
 * Key Features:
 * -------------
 * - Uses IoC binding to resolve provider implementation dynamically.
 * - Looks up the Source model (from `sources` table) seeded earlier.
 * - Calls the provider's fetch() method with optional query params.
 * - Iterates over fetched articles, maps them to a normalized payload, and
 *   saves them via the ArticleRepository (handles deduplication).
 *
 * Runs on the queue to avoid blocking the main request cycle.
 */

class ProcessLiveData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public string $providerKey;
    public ?string $query;
    /**
     * Create a new job instance.
     */
   
    public function __construct(string $providerKey, ?string $query = null)
    {
        $this->providerKey = $providerKey;
        $this->query = $query;
    }

    /**
     * Execute the job.
     */
    public function handle(ArticleRepository $repo): void
    {
        Log::info("FetchProviderJob started for {$this->providerKey}");

         // 1. Get the provider from IoC
         $providers = app('news.providers');
         $provider = $providers[$this->providerKey] ?? null;

         if (! $provider) {
            Log::warning("Provider {$this->providerKey} not found");
             return;
         }
 
         // 2. Get the Source record (we seeded earlier)
         $source = Source::where('slug', $this->providerKey)->first();
         if (! $source) {
            Log::warning("Source {$this->providerKey} not found in DB");
             return;
         }
 
        //3. Fetch live articles
        $params = [
            'q' => $this->query ?? 'latest',
            'page' => 1,
        ];

        $items = $provider->fetch($params);

        Log::info("No items fetched from provider {$this->providerKey}: ".count($items));

        if (empty($items)) {
            Log::info("No items fetched from provider {$this->providerKey}");
            return;
        }

 
         // 4. For each article, normalize & persist
         foreach ($items as $raw) {
             $payload = $provider->mapToArticlePayload($raw, $source);
 
             // Repository handles dedupe + save
             $repo->upsertFromPayload($payload);
         }
         
         Log::info("FetchProviderJob finished for {$this->providerKey}");
    }
}

