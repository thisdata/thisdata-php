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
            'ip' => '1.2.3.4',
            'user' => [
                'id' => '86',
                'name' => 'Maxwell Smart',
                'email' => 'max@control.com',
                'mobile' => '+64270000001'
            ],
            'user_agent' => 'useragent',
            'endpoint' => 'events',
            'method' => 'POST',
            'verb' => 'log-in',
        ];

        $this->assertTrackingWithHelper($expected, 'trackLogIn');
    }

    public function testTrackLogInDenied()
    {
        $expected = [
            'ip' => '1.2.3.4',
            'user' => [
                'id' => '86',
                'name' => 'Maxwell Smart',
                'email' => 'max@control.com',
                'mobile' => '+64270000001'
            ],
            'user_agent' => 'useragent',
            'endpoint' => 'events',
            'method' => 'POST',
            'verb' => 'log-in-denied',
        ];

        $this->assertTrackingWithHelper($expected, 'trackLogInDenied');
    }

    public function testTrackEvent()
    {
        $expected = [
            'ip' => '1.2.3.4',
            'user' => [
                'id' => '86',
                'name' => 'Maxwell Smart',
                'email' => 'max@control.com',
                'mobile' => '+64270000001'
            ],
            'user_agent' => 'useragent',
            'endpoint' => 'events',
            'method' => 'POST',
            'verb' => 'page-view',
        ];

        $this->assertTrack($expected);
    }

    public function testTrackEventSendsSourceWhenPresent()
    {
        $expected = [
            'ip' => '1.2.3.4',
            'user' => array(),
            'source' => [
              'name' => "Foo Bar",
              'logo_url' => "abcd"
            ],
            'session' => array(),
            'device' => array(),
            'user_agent' => 'useragent',
            'endpoint' => 'events',
            'method' => 'POST',
            'verb' => 'page-view',
        ];

        $this->endpoint->expects($this->once())
            ->method('execute')
            ->with(
                $expected['method'],
                $expected['endpoint'],
                $this->callback(function ($data) use ($expected) {
                    return
                         array_key_exists('source', $data)
                            && array_key_exists('name', $data['source']) && $data['source']['name'] == $expected['source']['name']
                            && array_key_exists('logo_url', $data['source']) && $data['source']['logo_url'] == $expected['source']['logo_url'];
                })
            );

        $this->endpoint->trackEvent($expected['verb'], $expected['ip'], $expected['user'], $expected['user_agent'], $expected['source'], $expected['session'], $expected['device']);
    }

    public function testTrackEventSendsSessionCookieWhenPresent()
    {
        $expected = [
            'ip' => '1.2.3.4',
            'user' => array(),
            'source' => array(),
            'session' => [
              "id" => "abcd1234"
            ],
            'device' => array(),
            'user_agent' => 'useragent',
            'endpoint' => 'events',
            'method' => 'POST',
            'verb' => 'page-view',
        ];

        $expectedCookieId = '11223344-abcd-998877';
        $_COOKIE['__tdli'] = $expectedCookieId;

        $this->endpoint->expects($this->once())
            ->method('execute')
            ->with(
                $expected['method'],
                $expected['endpoint'],
                $this->callback(function ($data) use ($expected, $expectedCookieId) {
                    return
                         array_key_exists('session', $data)
                            && array_key_exists('id', $data['session']) && $data['session']['id'] == $expected['session']['id']
                            && array_key_exists('td_cookie_id', $data['session']) && $data['session']['td_cookie_id'] == $expectedCookieId;
                })
            );

        $this->endpoint->trackEvent($expected['verb'], $expected['ip'], $expected['user'], $expected['user_agent'], $expected['source'], $expected['session'], $expected['device']);
    }

    public function testTrackEventSendsDeviceWhenPresent()
    {
        $expected = [
            'ip' => '1.2.3.4',
            'user' => array(),
            'source' => array(),
            'session' => array(),
            'device' => [
                'id' => 'abcd1234'
            ],
            'user_agent' => 'useragent',
            'endpoint' => 'events',
            'method' => 'POST',
            'verb' => 'page-view',
        ];

        $this->endpoint->expects($this->once())
            ->method('execute')
            ->with(
                $expected['method'],
                $expected['endpoint'],
                $this->callback(function ($data) use ($expected) {
                    return
                         array_key_exists('device', $data)
                            && array_key_exists('id', $data['device']) && $data['device']['id'] == $expected['device']['id'];
                })
            );

        $this->endpoint->trackEvent($expected['verb'], $expected['ip'], $expected['user'], $expected['user_agent'], $expected['source'], $expected['session'], $expected['device']);
    }

    // Helper methods

    protected function assertTrackingWithHelper($expected, $method)
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
                            && array_key_exists('mobile', $data['user']) && $data['user']['mobile'] == $expected['user']['mobile']
                        && array_key_exists('user_agent', $data) && $data['user_agent'] == $expected['user_agent']
                        ;
                })
            );

        $this->endpoint->$method($expected['ip'], $expected['user'], $expected['user_agent']);
    }

    protected function assertTrack($expected)
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
                            && array_key_exists('mobile', $data['user']) && $data['user']['mobile'] == $expected['user']['mobile']
                        && array_key_exists('user_agent', $data) && $data['user_agent'] == $expected['user_agent']
                        ;
                })
            );

        $this->endpoint->trackEvent($expected['verb'], $expected['ip'], $expected['user'], $expected['user_agent']);
    }
}
