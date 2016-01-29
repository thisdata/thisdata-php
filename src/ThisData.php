<?php

namespace ThisData\Api;

use ThisData\Api\Endpoint\EventsEndpoint;
use ThisData\Api\RequestHandler\RequestHandlerInterface;

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
        return $this->getOrCreateEndpoint(self::ENDPOINT_EVENTS, function () {
            return new EventsEndpoint($this->client, $this->handler);
        });
    }

    /**
     * Create or return an cached instance of the requested endpoint.
     *
     * @param string $endpoint
     * @param callable $builder
     * @return object
     */
    private function getOrCreateEndpoint($endpoint, callable $builder)
    {
        if (!array_key_exists($endpoint, $this->endpoints)) {
            $this->endpoints[$endpoint] = call_user_func($builder);
        }

        return $this->endpoints[$endpoint];
    }
}
