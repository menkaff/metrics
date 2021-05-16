# LinkShortner

  

## Installation

First add these repositories in 

> composer.json

    {
     "type": "git",
     "url": "https://github.com/mp3000mp/prometheus_client_php"
    },
    {
     "type": "git",
     "url": "https://github.com/menkaff/metrics"
    }
    
Then run these commands

    composer require promphp/prometheus_client_php:dev-feat/add-summary-support
    
And then if you have a lumen project just add this line in 

> bootsratp/app.php

    $app->register(PrometheusServiceProvider::class);

Finally you can copy prometheus.php file to your laravel\lumen **config** folder
and add these lines to your ***.env***

    PROMETHEUS_NAMESPACE=
    PROMETHEUS_SERVICE=main
    PROMETHEUS_METRICS_ROUTE_PATH=main/metrics
    PROMETHEUS_REDIS_PREFIX=PROMETHEUS_

