<?php

namespace ThisData\Api\ResponseManager;

use GuzzleHttp\Promise\Promise;

class AssuredResponseManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testManageResponse()
    {
        // Needs to be mocked to avoid the register_shutdown_function callback being called
        $manager = $this->getMockBuilder(AssuredResponseManager::class)
            ->setMethods(['onShutdown'])
            ->getMock();

        $this->assertAttributeCount(0, 'promises', $manager);

        $manager->manageResponse(new Promise());

        $this->assertAttributeCount(1, 'promises', $manager);
    }

    /**
     * Ensure pending promises are waited upon before shutdown.
     */
    public function testHandlePromise()
    {
        $ref = new \ReflectionClass(AssuredResponseManager::class);
        $method = $ref->getMethod('handlePromise');
        $method->setAccessible(true);

        $manager = $ref->newInstanceWithoutConstructor();

        $fulFilledPromise = $this->getMock(Promise::class);
        $fulFilledPromise->expects($this->once())
            ->method('getState')
            ->will($this->returnValue(Promise::FULFILLED));
                $fulFilledPromise->expects($this->never())
            ->method('wait');

        $rejectedPromise = $this->getMock(Promise::class);
        $rejectedPromise->expects($this->once())
            ->method('getState')
            ->will($this->returnValue(Promise::REJECTED));
        $rejectedPromise->expects($this->never())
            ->method('wait');

        $unfulfilledPromise = $this->getMock(Promise::class);
        $unfulfilledPromise->expects($this->once())
            ->method('getState')
            ->will($this->returnValue(Promise::PENDING));
        $unfulfilledPromise->expects($this->once())
            ->method('wait');

        $method->invoke($manager, $fulFilledPromise);
        $method->invoke($manager, $rejectedPromise);
        $method->invoke($manager, $unfulfilledPromise);
    }

    public function testOnShutdown()
    {
        $manager = $this->getMockBuilder(AssuredResponseManager::class)
            ->setMethods(['handlePromise'])
            ->disableOriginalConstructor()
            ->getMock();

        $manager->expects($this->exactly(3))
            ->method('handlePromise');

        $promise = new Promise();

        $manager->manageResponse($promise);
        $manager->manageResponse($promise);
        $manager->manageResponse($promise);

        $manager->onShutdown();
    }
}
