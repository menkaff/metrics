<?php

namespace Podro\Metrics\Controllers;

use App\Http\Controllers\Controller;
use Laravel\Lumen\Http\ResponseFactory;
use Prometheus\RenderTextFormat;
use Symfony\Component\HttpFoundation\Response;

class MetricsController extends Controller
{
    /**
     * @var ResponseFactory
     */
    protected $responseFactory;

    /**
     * @param ResponseFactory    $responseFactory
     * @param PrometheusExporter $prometheusExporter
     */
    public function __construct()
    {
        if (app() instanceof \Illuminate\Foundation\Application) {
            $this->responseFactory = new \Illuminate\Contracts\Routing\ResponseFactory;
        } else {
            $this->responseFactory = new \Laravel\Lumen\Http\ResponseFactory;
        }
        
    }

    /**
     * GET /metrics
     *
     * The route path is configurable in the prometheus.metrics_route_path config var, or the
     * PROMETHEUS_METRICS_ROUTE_PATH env var.
     *
     * @return Response
     */
    public function getMetrics(): Response
    {

        $registry = \Prometheus\CollectorRegistry::getDefault();

        $renderer = new RenderTextFormat();
        $result = $renderer->render($registry->getMetricFamilySamples());

        return $this->responseFactory->make($result, 200, ['Content-Type' => RenderTextFormat::MIME_TYPE]);
    }
}
