<?php

namespace Artron;

use SensioLabs\Consul\Services\Health;
use SensioLabs\Consul\ConsulResponse;
use SensioLabs\Consul\ServiceFactory;

class Consul
{
    protected $serviceBaseUri = 'http://localhost:8500';

    function __construct($args = [])
    {
        if (array_key_exists('serviceBaseUri', $args)) {
            $this->serviceBaseUri = $args['serviceBaseUri'];
        }
    }

    function hostAndPort($service_name)
    {
        $service = $this->find($service_name);
        return $service->Address . ':' . $service->Port;

    }

    function baseUri($service_name)
    {
        $service = $this->find($service_name);
        return 'http://' . $service->Address . ':' . $service->Port;

    }

    function find($service_name)
    {
        $options = [
            'base_uri' => $this->serviceBaseUri,
            'connect_timeout' => 3,
            'timeout' => 3,
        ];

        /**
         * @var Health $health
         * @var ConsulResponse $consulResponse
         */
        $health = (new ServiceFactory($options))->get('health');
        $consulResponse = $health->service($service_name);
        $services = json_decode($consulResponse->getBody());
        if (!$services) {
            throw new Exception('service ' . $service_name . ' not exists');
        }

        foreach ($services as $service) {
            if ($service->Service->Service != $service_name) {
                continue;
            }

            $del = false;
            foreach ($service->Checks as $check) {
                if ($check->Status == 'critical') {
                    $del = true;
                    break;
                }
            }
            if ($del) {
                continue;
            }
            return $service->Service;
        }
        throw new \Exception('service ' . $service_name . ' not available');
    }
}