<?php

namespace ThisData\Api;

use ThisData\Api\Endpoint\EventsEndpoint;
use ThisData\Api\RequestHandler\RequestHandlerInterface;

/**
 * ThisData Client
 *
 * Provides an abstraction over the pure HTTP client specific to interacting
 * with the ThisData API.
 *
 * For advanced users only. All others, use the `Builder` class to create an
 * instance.
 */
class ThisData
{
    const ENDPOINT_EVENTS = 'events';

    /**
     * @var Client
     */
    private $client;

    /**
     * @var RequestHandlerInterface
     */
    private $handler;

    /**
     * @var array
     */
    private $endpoints = [];

    /**
     * @var array
     */
    private static $endpointClassMap = [
        self::ENDPOINT_EVENTS => EventsEndpoint::class,
    ];

    /**
     * @param Client $client
     * @param RequestHandlerInterface $handler
     */
    public function __construct(Client $client, RequestHandlerInterface $handler)
    {
        $this->client = $client;
        $this->handler = $handler;
    }

    /**
     * @return EventsEndpoint
     */
    public function getEventsEndpoint()
    {
        return $this->getOrCreateEndpoint(self::ENDPOINT_EVENTS);
    }

    /**
     * Create or return an cached instance of the requested endpoint.
     *
     * @param string $endpoint
     * @return object
     * @throws \Exception
     */
    private function getOrCreateEndpoint($endpoint)
    {
        if (!array_key_exists($endpoint, $this->endpoints)) {
            if (!array_key_exists($endpoint, self::$endpointClassMap)) {
                throw new \Exception(sprintf('Unknown endpoint "%s"', $endpoint));
            }

            $endpointClass = self::$endpointClassMap[$endpoint];
            $this->endpoints[$endpoint] = $this->createEndpoint($endpointClass);
        }

        return $this->endpoints[$endpoint];
    }

    /**
     * @param string $class
     * @return mixed
     */
    private function createEndpoint($class)
    {
        return new $class($this->client, $this->handler);
    }
}
