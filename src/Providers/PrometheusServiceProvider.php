<?php

namespace Podro\Metrics\Providers;

use Illuminate\Support\ServiceProvider;

class PrometheusServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/prometheus.php' => $this->configPath('prometheus.php'),
        ]);
        $this->loadRoutesFrom(__DIR__ . '/../../routes/routes.php');

    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../Config/prometheus.php', 'prometheus');

    }

    private function configPath($path): string
    {
        return $this->app->basePath() . ($path ? DIRECTORY_SEPARATOR . $path : '');
    }
}
