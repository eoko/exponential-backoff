<?php

namespace Eoko\ExponentialBackoff\Test;

use Eoko\ExponentialBackoff\Utils\ExponentialBackoff;
use PHPUnit_Framework_TestCase;
use Zend\EventManager\Event;
use Zend\EventManager\EventManager;

class ExponentialBackoffTest extends PHPUnit_Framework_TestCase
{

    private $count = 0;

    public static function staticMethod($status)
    {
        return $status;
    }

    private function getCallable()
    {
        return function ($status) {
            return $status;
        };
    }

    private function getCallableWithException()
    {
        return function ($status) {
            if ($status->getRetry() < 1) {
                throw new \Exception();
            }
            return $status;
        };
    }

    public function getClass()
    {
        $method = new ExponentialBackoff();
        $eventManager = new EventManager();

        $callback = function (Event $e) {
            $this->count++;
        };

        $eventManager->attach(ExponentialBackoff::EVENT_PRE_CALL, $callback);
        $eventManager->attach(ExponentialBackoff::EVENT_POST_CALL, $callback);
        $eventManager->attach(ExponentialBackoff::EVENT_EXCEPTION_CALL, $callback);
        $eventManager->attach(ExponentialBackoff::EVENT_SLEEP_CALL, $callback);
        $eventManager->attach(ExponentialBackoff::EVENT_RETRY_CALL, $callback);
        $eventManager->attach(ExponentialBackoff::EVENT_END_CALL, $callback);

        $method->setEventManager($eventManager);
        return $method;
    }

    public function testSuccessClosure()
    {
        $test1 = $this->getClass()->exponentialBackoff($this->getCallable(), __FUNCTION__, 5);
        $this->assertInstanceOf('Eoko\ExponentialBackoff\Utils\Status', $test1);
        $this->assertTrue($test1->getSleep() == 0);

        $test2 = $this->getClass()->exponentialBackoff($this->getCallableWithException(), __FUNCTION__, 5);
        $this->assertEquals(1, $test2->getRetry());
        $this->assertTrue($test2->getSleep() > (2 * 1000000) && $test2->getSleep() < (3 * 1000000));

        $test3 = $this->getClass()->exponentialBackoff([$this, 'staticMethod'], __FUNCTION__, 5);
        $this->assertEquals(0, $test3->getRetry());
        $this->assertTrue($test3->getSleep() < (2 * 1000000));

        // Check that event are all triggered
        $this->assertEquals(10, $this->count);
    }

    /**
     * @expectedException \Exception
     */
    public function testExceptionClosure()
    {
        $callable_with_params_and_one_exception = function ($status) {
            throw new \Exception();
        };

        $this->count = 0;

        try {
            $this->getClass()->exponentialBackoff($callable_with_params_and_one_exception, __FUNCTION__, 2);
        } catch (\Exception $e) {
            // Check that event are all triggered
            $this->assertEquals(9, $this->count);

            // re-throw for test
            throw $e;
        }
    }
}
