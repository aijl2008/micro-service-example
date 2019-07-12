<?php

use GuzzleHttp\Client;


require "vendor/autoload.php";
require "src/Artron/Zipkin.php";

$tracing = (new \Artron\Zipkin('http://zipkin:9411'))->tracing('demo-php', '127.0.0.1');
$tracer = $tracing->getTracer();
$defaultSamplingFlags = \Zipkin\Propagation\DefaultSamplingFlags::createAsSampled();
$span = $tracer->newTrace($defaultSamplingFlags);
$span->start(\Zipkin\Timestamp\now());
$span->setName(__FILE__ . '[' . __LINE__ . ']');
$span->setKind(\Zipkin\Kind\SERVER);

$headers = [];
$task = function ($span, $name, closure $task) use (&$tracer, &$tracing, &$headers) {
    $childSpan = $tracer->newChild($span->getContext());
    $childSpan->start();
    $childSpan->setKind(Zipkin\Kind\CLIENT);
    $childSpan->setName($name);

    $injector = $tracing->getPropagation()->getInjector(new \Zipkin\Propagation\Map());
    $injector($span->getContext(), $headers);

    $childSpan->annotate('request_started', \Zipkin\Timestamp\now());
    $task($headers);
    $childSpan->annotate('request_finished', \Zipkin\Timestamp\now());

    return $childSpan;
};

$childSpan = $task($span, 'goods-shop', function ($headers) {
    $client = new Client([
        'base_uri' => "http://192.168.64.106:8001",
        'timeout' => 2.0,
    ]);
    $response = $client->request(
        'GET',
        '/goodses',
        [
            'headers' => $headers
        ]
    );
    echo $response->getStatusCode() . PHP_EOL;
    echo PHP_EOL;
});
$childSpan2 = $task($childSpan, 'test', function () {
    sleep(2);
});

$childSpan->flush();
$childSpan2->flush();

register_shutdown_function(function () use ($tracer) {
    $tracer->flush();
});