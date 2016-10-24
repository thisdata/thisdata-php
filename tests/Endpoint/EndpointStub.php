<?php

namespace ThisData\Api\Endpoint;

use GuzzleHttp\Client;
use ThisData\Api\RequestHandler\RequestHandlerInterface;

/**
 * Methods overridden to make public for testing.
 */
class EndpointStub extends AbstractEndpoint
{
    public function __construct(Client $client = null, RequestHandlerInterface $handler = null)
    {
        if ($client !== null || $handler !== null) {
            parent::__construct($client, $handler);
        }
    }


    public function findValue($key, $pool, $default = null)
    {
        return parent::findValue($key, $pool, $default);
    }

    public function serialize(array $data)
    {
        return parent::serialize($data);
    }

    public function execute($method, $verb, array $data)
    {
        parent::execute($method, $verb, $data);
    }

    public function synchronousExecute($method, $verb, array $data = array())
    {
        parent::execute($method, $verb, $data);
    }
}
