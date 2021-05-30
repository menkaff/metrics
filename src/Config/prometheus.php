<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Namespace
    |--------------------------------------------------------------------------
    |
    | The namespace to use as a prefix for all metrics.
    |
    | This will typically be the name of your project, eg: 'search'.
    |
     */

    'namespace' => env('PROMETHEUS_NAMESPACE', 'app'),

    /*
    |--------------------------------------------------------------------------
    | Service
    |--------------------------------------------------------------------------
    |
    | The service to use as a name of service.
    |
    | This will typically be the name of your project, eg: 'main'.
    |
     */

    'service' => env('PROMETHEUS_SERVICE', 'main'),

    /*
    |--------------------------------------------------------------------------
    | Metrics Route Path
    |--------------------------------------------------------------------------
    |
    | The path at which prometheus metrics are exported.
    |
    | This is only applicable if metrics_route_enabled is set to true.
    |
     */

    'metrics_route_path' => env('PROMETHEUS_METRICS_ROUTE_PATH', 'api/main/metrics'),

    /*
    |--------------------------------------------------------------------------
    | Metrics Route Path
    |--------------------------------------------------------------------------
    |
    | The path at which prometheus metrics are exported.
    |
    | This is only applicable if metrics_route_enabled is set to true.
    |
     */

    'metrics_filter_path' => env('PROMETHEUS_METRICS_FILTER_PATH', 'api/*'),

    /*
    |--------------------------------------------------------------------------
    | Storage Adapters
    |--------------------------------------------------------------------------
    |
    | The storage adapter configs.
    |
     */

    'storage_adapters' => [

        'redis' => [
            'host' => env('REDIS_HOST', 'redis'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', 6379),
            'timeout' => 0.1,
            'read_timeout' => 10,
            'persistent_connections' => false,
            'prefix' => env('PROMETHEUS_REDIS_PREFIX', 'PROMETHEUS_'),
        ],

    ],

];
