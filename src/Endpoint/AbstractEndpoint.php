<?php

namespace ThisData\Api\Endpoint;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use ThisData\Api\RequestHandler\RequestHandlerInterface;

abstract class AbstractEndpoint
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var RequestHandlerInterface
     */
    private $handler;

    /**
     * @var Array
     */
    protected $configuration;

    /**
     * @param Client $client
     * @param RequestHandlerInterface $handler
     * @param array|null $configuration
     */
    public function __construct(Client $client, RequestHandlerInterface $handler, array $configuration = null)
    {
        $this->client  = $client;
        $this->handler = $handler;
        $this->configuration = $configuration;
    }

    /**
     * Utility method to retrieve a value from an array, or a default if not found.
     *
     * @param string $key
     * @param array $pool
     * @param mixed|null $default
     * @return mixed|null
     */
    protected function findValue($key, $pool, $default = null)
    {
        if (is_null($pool)) {
            return $default;
        } else {
            return array_key_exists($key, $pool) ? $pool[$key] : $default;
        }
    }

    /**
     * @param array $data
     * @return string
     */
    protected function serialize(array $data)
    {
        return json_encode($data);
    }

    /**
     * @param string $method HTTP method to use for the request
     * @param string $verb   ThisData verb to be used on this endpoint
     * @param array  $data   Request body data containing event metadata
     */
    protected function execute($method, $verb, array $data)
    {
        $request = new Request($method, $verb, [], $this->serialize($data));

        $this->handler->handle($this->client, $request);
    }

    /**
     * Return the Guzzle HTTP client.
     *
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }
}
