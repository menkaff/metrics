# Metric for Prometheus PHP client 

  

## Installation

Install via composer in Laravel or Lumen

    composer require menkaff/metrics
    
If you have a lumen project just add this line in 

> bootsratp/app.php

    $app->register(PrometheusServiceProvider::class);

Finally you can copy prometheus.php file to your laravel\lumen **config** folder
and add these lines to your ***.env***

    PROMETHEUS_NAMESPACE=
    PROMETHEUS_SERVICE=main
    PROMETHEUS_METRICS_ROUTE_PATH=main/metrics
    PROMETHEUS_REDIS_PREFIX=PROMETHEUS_

