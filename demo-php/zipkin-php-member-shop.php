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
$span->annotate(__FILE__ . '[' . __LINE__ . ']', \Zipkin\Timestamp\now());

$headers = [];
$injector = $tracing->getPropagation()->getInjector(new \Zipkin\Propagation\Map());
$injector($span->getContext(), $headers);

echo __FILE__ . __LINE__ . PHP_EOL;
$client = new Client([
    'base_uri' => "http://localhost:8000",
    'timeout' => 2.0,
]);
$response = $client->request(
    'GET',
    '/request_shop_java_with_rest_template',
    [
        'headers' => $headers
    ]
);
echo $response->getStatusCode() . PHP_EOL;
//echo $response->getBody()->getContents();
echo PHP_EOL;

//echo __FILE__ . __LINE__ . PHP_EOL;
//$client = new Client([
//    'base_uri' => "http://localhost:8001",
//    'timeout' => 2.0,
//]);
//$response = $client->request(
//    'GET',
//    '/goodses',
//    [
//        'headers' => $headers
//    ]
//);
//echo $response->getStatusCode() . PHP_EOL;
//echo $response->getBody()->getContents();
//echo PHP_EOL;
foreach ((array)$span->getContext() as $key => $value) {
    echo $key . ":" . $value . PHP_EOL;
}
register_shutdown_function(function () use ($tracer) {
    $tracer->flush();

});