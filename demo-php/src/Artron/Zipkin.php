<?php


namespace Artron;


use Zipkin\Endpoint;
use Zipkin\Reporters\Http;
use Zipkin\Samplers\BinarySampler;
use Zipkin\TracingBuilder;
use Zipkin\Reporters\Http\CurlFactory;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Logger;

class Zipkin
{
    protected $server = '';

    function __construct($server = 'http://localhost:9411')
    {
        $this->server = $server;
    }

    function tracing($endpointName, $ipv4)
    {
        $endpoint = Endpoint::create($endpointName, $ipv4, null, 2555);

        /* Do not copy this logger into production.
        * Read https://github.com/Seldaek/monolog/blob/master/doc/01-usage.md#log-levels
        */
        $logger = new Logger('log');
        $logger->pushHandler(new ErrorLogHandler());

        $reporter = new Http(CurlFactory::create(), [
            'endpoint_url' => $this->server . '/api/v2/spans'
        ]);
        $sampler = BinarySampler::createAsAlwaysSample();
        $tracing = TracingBuilder::create()
            ->havingLocalEndpoint($endpoint)
            ->havingSampler($sampler)
            ->havingReporter($reporter)
            ->build();
        return $tracing;
    }
}