<?php

use GuzzleHttp\Client;
use whitemerry\phpkin\Metadata;
use whitemerry\phpkin\Tracer;
use whitemerry\phpkin\Endpoint;
use whitemerry\phpkin\Span;
use whitemerry\phpkin\Identifier\SpanIdentifier;
use whitemerry\phpkin\AnnotationBlock;
use whitemerry\phpkin\Logger\SimpleHttpLogger;
use whitemerry\phpkin\TracerInfo;

require "vendor/autoload.php";
require "ServiceDiscovery.php";
/**
 * 定义服务与发现的客户端
 */
$serviceDiscovery = new ServiceDiscovery(
    [
        'serviceBaseUri' => 'http://localhost:8500'
    ]
);

/**
 * 取 shopService 和 zipkinService
 */
$shopService = "http://" . $serviceDiscovery->hostAndPort('shop-java');
$zipkinService = "http://192.168.64.153:9411";

/**
 * 定义 zipkin 入口
 */
$logger = new SimpleHttpLogger([
    'host' => $zipkinService,
    'muteErrors' => false
]);

$endpoint = new Endpoint('demo-root-' . date('H-i-s'), $_SERVER["SERVER_NAME"] ?? '127.0.0.1', $_SERVER["SERVER_PORT"] ?? 80);
$tracer = new Tracer('home', $endpoint, $logger);
$requestStart = zipkin_timestamp();
$spanId = new SpanIdentifier();
///**
// * 业务部分，取商品列表
// */
//try {
//    $client = new Client([
//        'base_uri' => $shopService,
//        'timeout' => 10.0,
//    ]);
//    $response = $client->request('GET', '/goods', [
//        'headers' => [
//            'X-B3-TraceId: ' . TracerInfo::getTraceId() . "\r\n" .
//            'X-B3-SpanId: ' . ((string)$spanId) . "\r\n" .
//            'X-B3-ParentSpanId: ' . TracerInfo::getTraceSpanId() . "\r\n" .
//            'X-B3-Sampled: ' . ((int)TracerInfo::isSampled()) . "\r\n"
//        ]
//    ]);
//    $goods = json_decode($response->getBody()->getContents(), true);
//} catch (\Exception $e) {
//    echo $e->getMessage() . PHP_EOL;
//    $goods = [];
//}
///**
// * 向 zipkin 发送 span
// */
////$endpoint = new Endpoint('loop ' . $i . ' ' . $s, '127.0.0.1', '80');
//$span = new Span($spanId, $shopService . '/goods', new AnnotationBlock($endpoint, $requestStart));
//$tracer->addSpan($span);
//$tracer->trace();
//var_dump($goods);
//exit();
/**
 * 业务部分，取用户列表
 */
$memberService = 'http://127.0.0.1:10020/';
try {
    $client = new Client([
        'base_uri' => $memberService,
        'timeout' => 10.0,
    ]);
    $response = $client->request('GET', '/',
        [
            'headers' => [
                'X-B3-TraceId: ' . TracerInfo::getTraceId() . "\r\n" .
                'X-B3-SpanId: ' . ((string)$spanId) . "\r\n" .
                'X-B3-ParentSpanId: ' . TracerInfo::getTraceSpanId() . "\r\n" .
                'X-B3-Sampled: ' . ((int)TracerInfo::isSampled()) . "\r\n"
            ]
        ]
    );
    $members = json_decode($response->getBody()->getContents(), true);
} catch (\Exception $e) {
    echo $e->getMessage() . PHP_EOL;
    $members = [];
}
/**
 * 向 zipkin 发送 span
 */
//$endpoint = new Endpoint('loop ' . $i . ' ' . $s, '127.0.0.1', '80');
$span = new Span($spanId, $memberService . '/', new AnnotationBlock($endpoint, $requestStart));
$tracer->addSpan($span);
$tracer->setProfile(Tracer::FRONTEND);
$tracer->trace();
var_dump($members);
