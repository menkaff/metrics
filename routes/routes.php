<?php

use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Menkaff\Metrics\Controllers'], function () {
    Route::get(config('prometheus.metrics_route_path'), 'MetricsController@getMetrics');
});
