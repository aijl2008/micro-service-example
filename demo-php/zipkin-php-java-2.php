<?php

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Logger;
use Zipkin\Annotation;
use Zipkin\Endpoint;
use const Zipkin\Kind\CLIENT;
use Zipkin\Reporters\Http\CurlFactory;
use Zipkin\Samplers\BinarySampler;
use Zipkin\TracingBuilder;
use Zipkin\Reporters\Http;

use Zipkin\Propagation\DefaultSamplingFlags;
use Zipkin\Propagation\Map;
use Zipkin\Timestamp;

require "vendor/autoload.php";


$endpoint = Endpoint::create('php-demo', '127.0.0.1', null, 2555);
//$endpoint = Endpoint::createFromGlobals();

// Logger to stdout
$logger = new Logger('log');
$logger->pushHandler(new ErrorLogHandler());
$reporter = new Zipkin\Reporters\Http(CurlFactory::create());
//$reporter = new Http();
$sampler = BinarySampler::createAsAlwaysSample();
$tracing = TracingBuilder::create()
    ->havingLocalEndpoint($endpoint)
    ->havingSampler($sampler)
    ->havingReporter($reporter)
    ->build();


$tracer = $tracing->getTracer();
/* Always sample traces */
$defaultSamplingFlags = DefaultSamplingFlags::createAsSampled();
/* Creates the main span */
$span = $tracer->newTrace($defaultSamplingFlags);
$span->start(Timestamp\now());
$span->setName('parse_request');
$span->setKind(Zipkin\Kind\SERVER);
$client = new Client([
    'base_uri' => 'http://localhost:8500',
    'timeout' => 10.0,
]);
$res = $client->request('GET', '/v1/catalog/services');



$decoded = json_decode($res->getBody()->getContents(), true);

if (!array_key_exists('shop-node',$decoded)){
    throw new Exception('shop-node api not found');
}


$res = $client->request('GET', '/v1/catalog/service/shop-node' );
$decoded = json_decode($res->getBody()->getContents(), true);
$cnt = count($decoded);
//echo "found {$cnt} servive(s)" .PHP_EOL;

$service = $decoded[array_rand($decoded)];

$client = new Client([
    'base_uri' => 'http://'.$service['ServiceAddress'].':' .$service['ServicePort'],
    'timeout' => 10.0,
]);
$res = $client->request('GET', '/goods');
$decoded = json_decode($res->getBody()->getContents(), true);



/* Creates the span for getting the users list */
$childSpan = $tracer->newChild($span->getContext());
$childSpan->start();
$childSpan->setKind(CLIENT);
$childSpan->setName('users:get_list');
$headers = [];
/* Injects the context into the wire */
$injector = $tracing->getPropagation()->getInjector(new Map());
$injector($childSpan->getContext(), $headers);
/* HTTP Request to the backend */
$httpClient = new Client();
$request = new Request('POST', 'localhost:9411', $headers);
$childSpan->annotate('request_started', Timestamp\now());
$response = $httpClient->send($request);
$childSpan->annotate('request_finished', Timestamp\now());
$childSpan->finish();
$span->finish();
/* Sends the trace to zipkin once the response is served */
register_shutdown_function(function () use ($tracer) {
    $tracer->flush();
});

?>

<!DOCTYPE html>
<html lang="zh-CN">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- 上述3个meta标签*必须*放在最前面，任何其他内容都*必须*跟随其后！ -->
    <title>Demo</title>

    <!-- Bootstrap -->
    <link href="https://cdn.bootcss.com/twitter-bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">

    <!-- HTML5 shim 和 Respond.js 是为了让 IE8 支持 HTML5 元素和媒体查询（media queries）功能 -->
    <!-- 警告：通过 file:// 协议（就是直接将 html 页面拖拽到浏览器中）访问页面时 Respond.js 不起作用 -->
    <!--[if lt IE 9]>
      <script src="https://cdn.jsdelivr.net/npm/html5shiv@3.7.3/dist/html5shiv.min.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/respond.js@1.4.2/dest/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
    <script src="https://cdn.bootcss.com/jquery/3.4.1/jquery.min.js"></script>
    <!-- 加载 Bootstrap 的所有 JavaScript 插件。你也可以根据需要只加载单个插件。 -->
    <script src="https://cdn.bootcss.com/twitter-bootstrap/4.3.1/js/bootstrap.min.js"></script>

    <table class="table">
    	<tr>
    		<td>name</td>
    		<td>stocks</td>
    		<td>price</td>
    	</tr>
    	<?php foreach($decoded as $row){ ?>
    	<tr>
    		<td><?php echo $row['goods'];?></td>
    		<td><?php echo $row['stocks'];?></td>
    		<td><?php echo $row['price'];?></td>
    	</tr>
    	<?php }?>
    </table>
  </body>
</html>
