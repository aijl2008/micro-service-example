<?php

use GuzzleHttp\Client;
use const Zipkin\Kind\CLIENT;
use Zipkin\Propagation\DefaultSamplingFlags;
use Zipkin\Propagation\Map;
use Zipkin\Timestamp;

require "vendor/autoload.php";
require "src/Artron/Zipkin.php";
require "src/Artron/Consul.php";

$tracing = (new \Artron\Zipkin('http://zipkin:9411'))->tracing('demo-root', '127.0.0.1');
$tracer = $tracing->getTracer();
/* Always sample traces */
$defaultSamplingFlags = DefaultSamplingFlags::createAsSampled();
/* Creates the main span */
$span = $tracer->newTrace($defaultSamplingFlags);
$span->start(Timestamp\now());
$span->setName('app starting ......');
$span->setKind(Zipkin\Kind\SERVER);
usleep(100 * mt_rand(1, 3));

/**
 * 查找服务
 */
$childSpan = $tracer->newChild($span->getContext());
$childSpan->start();
$childSpan->setKind(Zipkin\Kind\CLIENT);
$childSpan->setName('request http://consul-service/goods');
$headers = [];
$injector = $tracing->getPropagation()->getInjector(new Map());
$injector($childSpan->getContext(), $headers);
$httpClient = new Client();
$childSpan->annotate('request_started', Timestamp\now());

//
///**
// * 取 shopService
// */
//$consul = new \Artron\Consul(
//    [
//        'serviceBaseUri' => 'http://consul:8500'
//    ]
//);
//$shopService = $consul->baseUri('auction_cache');
////$shopService = $consul->baseUri('shop-java');
//$childSpan->annotate('request_finished', Timestamp\now());
//
///**
// * 请求商品列表
// */
//$childSpan = $tracer->newChild($span->getContext());
//$childSpan->start();
//$childSpan->setKind(Zipkin\Kind\CLIENT);
//$childSpan->setName('request http://shop-service/goodses');
//$headers = [];
//$injector = $tracing->getPropagation()->getInjector(new Map());
//$injector($childSpan->getContext(), $headers);
//$childSpan->annotate('request_started', Timestamp\now());
//$shopService = str_replace('9000', '8000', $shopService);
//var_dump($shopService);
//$client = new Client([
//    'base_uri' => $shopService,
//    'timeout' => 10.0,
//]);
//
//$response = $client->request('GET', '/auction-cache.artron.net/getArtCodeCache?artcode=art99360182,art5059670449', [
//    'headers' => $headers
//]);
//$goods = json_decode($response->getBody()->getContents(), true);
//var_export($goods);
//
//
//
////$response = $client->request('GET', '/goodses', [
////    'headers' => $headers
////]);
////$goods = json_decode($response->getBody()->getContents(), true);
////var_export($goods);
////sleep(3);
//$childSpan->annotate('request_finished', Timestamp\now());

/**
 * 请求用户列表
 */
$childSpan = $tracer->newChild($span->getContext());
$childSpan->start();
$childSpan->setKind(Zipkin\Kind\CLIENT);
$childSpan->setName('request http://member-service/goods');
$headers = [];
$injector = $tracing->getPropagation()->getInjector(new Map());
$injector($childSpan->getContext(), $headers);

$childSpan->annotate('request_started', Timestamp\now());
$client = new Client([
    'base_uri' => 'http://192.168.66.181:8000',
    'timeout' => 10.0,
]);
$response = $client->request('GET', '/users', [
    'headers' => $headers
]);
var_dump($response->getBody()->getContents());
var_dump($headers);
$childSpan->annotate('request_finished', Timestamp\now());
//$childSpan->finish();
//
//$span->finish();

/* Sends the trace to zipkin once the response is served */
register_shutdown_function(function () use ($tracer) {
    $tracer->flush();
});

