<?php

namespace Menkaff\Metrics\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Symfony\Component\HttpFoundation\Response;

class PrometheusMiddleware
{
    /**
     * @var \Prometheus\CollectorRegistry
     */
    private $exporter;

    private $adaptor;

    private $service;

    private $platform;

    private $labels;

    /**
     * @var string
     */
    private $namespace;

    /**
     * PrometheusMiddleware constructor.
     *
     * @param  PrometheusExporter  $exporter
     */
    public function __construct(
    ) {
        // $this->wipeStorage();
        \Prometheus\Storage\Redis::setDefaultOptions(
            [
                'host' => config('prometheus.storage_adapters.redis.host'),
                'port' => config('prometheus.storage_adapters.redis.port'),
                'password' => config('prometheus.storage_adapters.redis.password'),
                'timeout' => config('prometheus.storage_adapters.redis.timeout'),
                'read_timeout' => config('prometheus.storage_adapters.redis.read_timeout'),
                'persistent_connections' => config('prometheus.storage_adapters.redis.persistent_connections'),
            ]
        );

        $this->namespace = config('prometheus.namespace');

        $this->exporter = \Prometheus\CollectorRegistry::getDefault();

        $this->service = config('prometheus.service');

        $this->platform = 'HTTP';

        $this->labels = [
            'method',
            'service',
            'platform',
        ];
    }

    /**
     * @param  Request  $request
     * @param  Closure  $next
     *
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $start = microtime(true);
        $response = $next($request);
        if (app() instanceof \Illuminate\Foundation\Application && $request->route()) {
            $duration = microtime(true) - $start;
            if (app() instanceof \Illuminate\Foundation\Application) {
                $params = $request->route()->parameters();
            } elseif (isset($request->route()[2])) {
                $params = $request->route()[2];
            } else {
                $params = [];
            }
            $path = $request->path();

            if (isset($params) && count($params)) {
                foreach ($params as $key => $value) {
                    $path = str_replace($value, "{" . $key . "}", $path);
                }
            }

            $labelValues = [
                $request->method() . ' ' . $path,
                $this->service,
                $this->platform,
            ];

            $this->increaseSLITotal($labelValues);

            if ($this->isResponseSuccessful($response)) {
                $this->increaseSLISuccess($labelValues);
                $this->observeSLIDurationSummary($duration, $labelValues);
            } else {
                $this->increaseSLIFail($labelValues);
            }
        }

        return $response;
    }

    /**
     * @param  Response  $response
     *
     * @return bool
     */
    private function isResponseSuccessful(Response $response): bool
    {
        $statusCodee = $response->getStatusCode();
        return 200 <= $statusCodee && 500 > $statusCodee;
    }

    /**
     * @param  float     $duration
     * @param  Request   $request
     * @param  Route     $route
     * @param  Response  $response
     */
    private function observeSLIDurationHistogram(float $duration, array $labelValues): void
    {

        $this->exporter->getOrRegisterHistogram(
            $this->namespace,
            'sli_duration',
            'The histogram of response duration',
            $this->labels,
            [0.5, 0.9, 0.99]
        )->observe(
            $duration,
            $labelValues
        );

    }

    /**
     * @param  float     $duration
     * @param  Request   $request
     * @param  Route     $route
     * @param  Response  $response
     */
    private function observeSLIDurationSummary(float $duration, array $labelValues): void
    {

        $this->exporter->getOrRegisterSummary(
            $this->namespace,
            'sli_duration',
            'The summary of response duration',
            $this->labels,
            600,
            [0.5, 0.9, 0.99]
        )->observe(
            $duration,
            $labelValues
        );
    }

    private function increaseSLITotal(array $labelValues): void
    {

        $this->increaseRequestCounter('sli_total', 'The total number of request given', $labelValues);
    }

    private function increaseSLISuccess(array $labelValues): void
    {

        $this->increaseRequestCounter('sli_success', 'The total number of success response', $labelValues);
    }

    private function increaseSLIFail(array $labelValues): void
    {

        $this->increaseRequestCounter('sli_fail', 'The total number of fail response', $labelValues);
    }

    private function wipeStorage()
    {
        $adapter = new \Prometheus\Storage\Redis(['host' => config('prometheus.storage_adapters.redis.host')]);

        $adapter->wipeStorage();
    }

    /**
     * @param  string  $name
     * @param  string  $help
     * @param  array  $help
     */
    private function increaseRequestCounter(string $name, string $help, array $labelValues): void
    {
        $this->exporter->getOrRegisterCounter($this->namespace, $name, $help, $this->labels)->inc(
            $labelValues
        );
    }

}
