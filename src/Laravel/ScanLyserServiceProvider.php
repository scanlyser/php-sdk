<?php

declare(strict_types=1);

namespace ScanLyser\Laravel;

use Illuminate\Support\ServiceProvider;
use ScanLyser\Client;

class ScanLyserServiceProvider extends ServiceProvider
{
    /**
     * Register the ScanLyser client as a singleton.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/config/scanlyser.php', 'scanlyser');

        $this->app->singleton(Client::class, fn () => new Client(
            apiKey: config('scanlyser.token'),
        ));
    }

    /**
     * Publish the configuration file.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/config/scanlyser.php' => config_path('scanlyser.php'),
            ], 'scanlyser-config');
        }
    }
}
