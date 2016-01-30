<?php

namespace ThisData\Api;

use ThisData\Api\Endpoint\EventsEndpoint;
use ThisData\Api\RequestHandler\SynchronousRequestHandler;

class ThisDataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $client;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $requestHandler;

    /**
     * @var ThisData
     */
    private $thisData;

    public function setUp()
    {
        $this->client = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->requestHandler =  $this->getMockBuilder(SynchronousRequestHandler::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->thisData = new ThisData($this->client, $this->requestHandler);
    }

    public function testGetEventsEndpoint()
    {
        $endpoint = $this->thisData->getEventsEndpoint();
        $this->assertInstanceOf(EventsEndpoint::class, $endpoint);

        $endpoint2 = $this->thisData->getEventsEndpoint();
        $this->assertSame($endpoint, $endpoint2);
    }
}
