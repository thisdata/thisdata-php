<?php

namespace ThisData\Api\Endpoint;

use GuzzleHttp\Psr7\Response;

class VerifyEndpointTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $endpoint;

    public function setUp()
    {
        $this->endpoint = $this->getMockBuilder(VerifyEndpoint::class)
            ->disableOriginalConstructor()
            ->setMethods(['synchronousExecute'])
            ->getMock();
    }

    public function testVerifyParameters()
    {
        $expected = [
            'ip' => '1.2.3.4',
            'user' => [
              'id' => '112233'
            ],
            'user_agent' => 'useragent',
            'endpoint' => 'verify',
            'method' => 'POST'
        ];

        $this->endpoint->expects($this->once())
            ->method('synchronousExecute')
            ->with(
                $expected['method'],
                $expected['endpoint'],
                $this->callback(function ($data) use ($expected) {
                    return
                        array_key_exists('ip', $data) && $data['ip'] == $expected['ip']
                        && array_key_exists('user', $data)
                            && array_key_exists('id', $data['user']) && $data['user']['id'] == $expected['user']['id']
                        ;
                })
            )
            ->willReturn(new Response(200));

        $this->endpoint->verify($expected['ip'], $expected['user']);
    }

    public function testVerifyReturnsJSON()
    {
        $expected = [
            'ip' => '1.2.3.4',
            'user' => [
              'id' => '112233'
            ],
            'user_agent' => 'useragent',
            'endpoint' => 'verify',
            'method' => 'POST'
        ];

        $body = '{"score" : 0.75, "risk_level" : "red", "triggers" : ["Device seen in previous attacks"]}';

        $this->endpoint->expects($this->once())
            ->method('synchronousExecute')
            ->willReturn(new Response(200, array(), $body, '1.1'));
        $response = $this->endpoint->verify($expected['ip'], $expected['user']);

        $this->assertSame($response['score'], 0.75);
        $this->assertSame($response['risk_level'], "red");
        $this->assertSame($response['triggers'], ["Device seen in previous attacks"]);
    }


}
