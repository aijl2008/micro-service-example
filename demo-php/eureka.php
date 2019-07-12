<?php

use Eureka\EurekaClient;

require "vendor/autoload.php";
require "src/Artron/Zipkin.php";

$client = new EurekaClient([
    'eurekaDefaultUrl' => 'http://eureka:8761/eureka'
]);
$services = $client->fetchInstances("member-java");
$services = $services[array_rand($services)];

echo $services->homePageUrl . PHP_EOL;
