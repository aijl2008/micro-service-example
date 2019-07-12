<?php

use GuzzleHttp\Client;

require "vendor/autoload.php";
$client = new Client([
    'base_uri' => 'http://consul:8500',
    'timeout' => 10.0,
]);
$res = $client->request('GET', '/v1/catalog/services');
$decoded = json_decode($res->getBody()->getContents(), true);
print_r($decoded);