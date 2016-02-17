<?php

namespace ThisData\Api\RequestHandler;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;
use ThisData\Api\Event\Event;
use ThisData\Api\Event\EventDispatcherInterface;

abstract class AbstractRequestHandler implements RequestHandlerInterface
{
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @return EventDispatcherInterface
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     * @param ResponseInterface $response
     */
    public function handleSuccess(ResponseInterface $response)
    {
        $event = Event::success($response);

        $this->getDispatcher()->dispatch(
            EventDispatcherInterface::EVENT_REQUEST_SUCCESS,
            $event
        );
    }

    /**
     * @param RequestException $e
     */
    public function handleError(RequestException $e)
    {
        $event = Event::error($e);

        $this->getDispatcher()->dispatch(
            EventDispatcherInterface::EVENT_REQUEST_ERROR,
            $event
        );
    }

    /**
     * @param Client $client
     * @param Request $request
     * @return PromiseInterface
     */
    protected function send(Client $client, Request $request)
    {
        $promise = $client->sendAsync($request)
            ->then(
                [$this, 'handleSuccess'],
                [$this, 'handleError']
            );

        return $promise;
    }
}
