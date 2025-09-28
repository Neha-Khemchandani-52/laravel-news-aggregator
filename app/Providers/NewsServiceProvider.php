<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\NewsProviders\NewsApiProvider;
use App\Services\NewsProviders\GuardianProvider;
use App\Services\NewsProviders\NytProvider;

class NewsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register individual providers as singletons
        $this->app->singleton('news.provider.newsapi', function () {
            return new NewsApiProvider();
        });

        $this->app->singleton('news.provider.guardian', function () {
            return new GuardianProvider();
        });

        $this->app->singleton('news.provider.nyt', function () {
            return new NytProvider();
        });

        // Register a collection of all providers
        $this->app->singleton('news.providers', function ($app) {
            return [
                'newsapi'  => $app->make('news.provider.newsapi'),
                'guardian' => $app->make('news.provider.guardian'),
                'nyt'      => $app->make('news.provider.nyt'),
            ];
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // nothing special to boot here, but method is required
    }
}
