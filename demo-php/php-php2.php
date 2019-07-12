<?php

use GuzzleHttp\Client;
use const Zipkin\Kind\CLIENT;
use Zipkin\Propagation\DefaultSamplingFlags;
use Zipkin\Propagation\Map;
use Zipkin\Timestamp;

require "vendor/autoload.php";
require "src/Artron/Zipkin.php";
require "src/Artron/Consul.php";

$tracing = (new \Artron\Zipkin('http://zipkin:9411'))->tracing('php-php2-root', '127.0.0.1');
$tracer = $tracing->getTracer();
/* Always sample traces */
$defaultSamplingFlags = DefaultSamplingFlags::createAsSampled();
/* Creates the main span */
$span = $tracer->newTrace($defaultSamplingFlags);
$span->start(Timestamp\now());
$span->setName('app starting ......');
$span->setKind(Zipkin\Kind\SERVER);


/**
 * 查找服务
 */
//$childSpan = $tracer->newChild($span->getContext());
////$childSpan->start();
////$childSpan->setKind(Zipkin\Kind\CLIENT);
////$childSpan->setName('request http://consul-service/goods');
////$headers = [];
////$injector = $tracing->getPropagation()->getInjector(new Map());
////$injector($childSpan->getContext(), $headers);
////$httpClient = new Client();
////$childSpan->annotate('request_started', Timestamp\now());
///
///
///

$headers = [];
$injector = $tracing->getPropagation()->getInjector(new Map());
$injector($span->getContext(), $headers);
$httpClient = new Client();
$span->annotate('request_started', Timestamp\now());

$client = new Client([
    'base_uri' => 'http://192.168.66.181:8000',
    'timeout' => 10.0,
]);
$response = $client->request('GET', '/users', [
    'headers' => $headers
]);
var_dump($response->getBody()->getContents());
var_dump($headers);
$span->annotate('request_finished', Timestamp\now());
//$childSpan->finish();
//
//$span->finish();

/* Sends the trace to zipkin once the response is served */
register_shutdown_function(function () use ($tracer) {
    $tracer->flush();
});


//

