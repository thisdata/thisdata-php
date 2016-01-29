<?php

namespace ThisData\Api\Endpoint;

class EventsEndpointTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $endpoint;

    public function setUp()
    {
        $this->endpoint = $this->getMockBuilder(EventsEndpoint::class)
            ->disableOriginalConstructor()
            ->setMethods(['execute'])
            ->getMock();
    }

    public function testTrackLogIn()
    {
        $expected = [
            'ip' => '127.0.0.1',
            'user' => [
                'id' => '86',
                'name' => 'Maxwell Smart',
                'email' => 'max@control.com'
            ],
            'user_agent' => 'useragent',
            'endpoint' => 'events',
            'method' => 'POST',
            'verb' => 'log-in',
        ];

        $this->assertTracking($expected, 'trackLogIn');
    }

    public function testTrackLogInDenied()
    {
        $expected = [
            'ip' => '127.0.0.1',
            'user' => [
                'id' => '86',
                'name' => 'Maxwell Smart',
                'email' => 'max@control.com'
            ],
            'user_agent' => 'useragent',
            'endpoint' => 'events',
            'method' => 'POST',
            'verb' => 'log-in-denied',
        ];

        $this->assertTracking($expected, 'trackLogInDenied');
    }

    protected function assertTracking($expected, $method)
    {
        $this->endpoint->expects($this->once())
            ->method('execute')
            ->with(
                $expected['method'],
                $expected['endpoint'],
                $this->callback(function ($data) use ($expected) {
                    return
                        array_key_exists('verb', $data) && $data['verb'] == $expected['verb']
                        && array_key_exists('ip', $data) && $data['ip'] == $expected['ip']
                        && array_key_exists('user', $data)
                            && array_key_exists('id', $data['user']) && $data['user']['id'] == $expected['user']['id']
                            && array_key_exists('name', $data['user']) && $data['user']['name'] == $expected['user']['name']
                            && array_key_exists('email', $data['user']) && $data['user']['email'] == $expected['user']['email']
                        && array_key_exists('user_agent', $data) && $data['user_agent'] == $expected['user_agent']
                        ;
                })
            );

        $this->endpoint->$method($expected['ip'], $expected['user'], $expected['user_agent']);
    }
}
