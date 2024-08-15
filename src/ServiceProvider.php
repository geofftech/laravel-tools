<?php

namespace GeoffTech\LaravelTools;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        // $this->mergeConfigFrom(__DIR__.'/../config/tools.php', 'tools');
    }

    public function boot(): void
    {
        // $this->publishes([
        //     __DIR__.'/../config/tools.php' => config_path('tools.php'),
        // ]);

        // $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        // $this->loadViewsFrom(__DIR__ . '/../resources/views', 'tools');
    }
}
