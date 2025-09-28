<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Jobs\ProcessLiveData;

/**
 * Command: news:fetch {provider} {query?}
 *
 * Dispatches a job to fetch and process live articles for the given provider.
 * Example: php artisan news:fetch newsapi sports
 */

class SyncLiveData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'news:fetch {provider} {query?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch latest news from all providers or a specific one,with optional query';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Dispatch job to queue
        $providers = app('news.providers'); // all registered providers

        foreach ($providers as $key => $provider) {
            // If a provider argument was passed, only run for that one
            if ($this->argument('provider') && $this->argument('provider') !== $key) {
                continue;
            }

            $query = $this->argument('query'); // can be null

            $this->info("Dispatching job for provider: {$key} with query: " . ($query ?? 'latest'));

            dispatch(new ProcessLiveData($key,$query));
        }

        $this->info('Live data sync job dispatched successfully.');
    }
}
