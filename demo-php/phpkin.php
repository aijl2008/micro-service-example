<?php

use SensioLabs\Consul\ConsulResponse;
use SensioLabs\Consul\ServiceFactory;
use SensioLabs\Consul\Services\Health;
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

$logger = new SimpleHttpLogger([
    'host' => 'http://127.0.0.1:9411', // Zipkin's API host with schema (http://) and without trailing slash
    'muteErrors' => false
]);

$endpoint = new Endpoint('555-zipkin-demo', '127.0.0.1', '80');
$tracer = new Tracer('home', $endpoint, $logger);


//$http_ret = $this->span_http_request();
$request_start = zipkin_timestamp();
$span_id = new SpanIdentifier();

//your logic code, just for demo
usleep(1500);
$tracer->trace();


$endpoint = new Endpoint('test'.__LINE__, '127.0.0.1', '80');
$span = new Span($span_id, 'request /api/user/1', new AnnotationBlock($endpoint, $request_start));
$tracer->addSpan($span);
//
//$http_ret = [
//    'code' => 0,
//    'msg' => 'http request demo'
//];
//
////
////// $grpc_ret = $this->span_grpc_request();
////$request_start = zipkin_timestamp();
////$span_id = new SpanIdentifier();
////
//////your logic code begin
////$q = '卧槽';
////$meta = new Metadata();
////$meta->set('X-B3-TraceId', (string)TracerInfo::getTraceId());
////$meta->set('X-B3-ParentSpanId', (string)TracerInfo::getTraceSpanId());
////$meta->set('X-B3-SpanId', (string)$span_id);
////$meta->set('X-B3-Sampled', true);
////
////$service_name = 'auction_cache_cache';
////$service = (new ServiceDiscovery(['serviceBaseUri'=>'http://192.168.64.103:8500']))->find($service_name);
////$hostname = $service->Address . ':' . $service->Port;
////
////$opts = [
////    'credentials' => \Grpc\ChannelCredentials::createInsecure()
////];
////
////$client = new \Pb\DemoClient($hostname, $opts);
////$request = new \Pb\HelloRequest();
////$request->setQ($q);
////$request->setN(10);
////
////
////$zipKinMetadata = [];
////foreach ($meta->toArray() as $m) {
////    $zipKinMetadata[$m['key']] = [$m['value']];
////}
////
////
////
////$call = $client->Hello($request,$zipKinMetadata);
/////**
//// * @var \Pb\HelloReply $reply
//// */
////list($reply, $status) = $call->wait();
////if ($status->code == 0) {
////    $code = $reply->getCode();
////    $msg = $reply->getMsg();
////    //TODO
////} else {
////    $code = -1;
////    $msg = 'grpc request failed';
////}
////
//////for demo
////usleep(200);
////
////
////$endpoint = new Endpoint('request ' . $service_name, gethostbyname($service->Address), $service->Port);
////$span = new Span($span_id, "q: $q", new AnnotationBlock($endpoint, $request_start));
////$tracer->addSpan($span);
//
////$grpc_ret = [$code, $msg];
//
//
//$grpc_ret = [];
//
////send to zipkin
//$tracer->trace();
//
//$context = [
//    'data' => [
//        'http_result' => $http_ret,
//        'grpc_result' => $grpc_ret,
//        'trace_id' => (string)TracerInfo::getTraceId(),
//        'parent_span_id' => (string)TracerInfo::getTraceSpanId(),
//    ]
//];
//
//var_dump($context);
//
//function getService($service_name)
//{
//    $consul_host = 'http://192.168.64.103:8500';
//
//    //guzzle request options
//    $options = [
//        'base_uri' => $consul_host,
//        'connect_timeout' => 3,
//        'timeout' => 3,
//    ];
//
//    /**
//     * @var Health $h
//     * @var ConsulResponse $resp
//     */
//    $h = (new ServiceFactory($options))->get('health');
//    $resp = $h->service($service_name);
//    $services = json_decode($resp->getBody());
//    if (!$services) {
//        throw new Exception('service ' . $service_name . ' not exists');
//    }
//
//    foreach ($services as $service) {
//        if ($service->Service->Service != $service_name) {
//            continue;
//        }
//
//        $del = false;
//        foreach ($service->Checks as $check) {
//            if ($check->Status == 'critical') {
//                $del = true;
//                break;
//            }
//        }
//        if ($del) {
//            continue;
//        }
//        //$host = $service->Service->Address.':'.$service->Service->Port;
//        return $service->Service;
//    }
//
//    throw new Exception('service ' . $service_name . ' not available');
//}
//
//exit();
//
//$endpoint = new Endpoint(
//    'My application', // Application name
//    '127.0.0.1', // Current application IP address
//    '80' // Current application port (default 80)
//);
//
//$logger = new SimpleHttpLogger([
//    'host' => 'http://127.0.0.1:9411' // Zipkin's API host with schema (http://) and without trailing slash
//]);
//
//$tracer = new Tracer(
//    'http://localhost/login', // Trace name
//    $endpoint, // Your application meta-information
//    $logger // Logger used to store/send traces
//);
//$tracer->setProfile(Tracer::FRONTEND);
//
////$tracer->trace();