<?php

namespace Eoko\ExponentialBackoff\Test;

use Eoko\ExponentialBackoff\Utils\ExponentialBackoff;
use Eoko\ExponentialBackoff\Utils\Status;
use PHPUnit_Framework_TestCase;
use Zend\EventManager\Event;
use Zend\EventManager\EventManager;

class ExponentialBackoffTest extends PHPUnit_Framework_TestCase
{
    public static function example($status)
    {
        return $status;
    }

    public function testSuccessClosure()
    {
//        $method = new ExponentialBackoff();
//        $eventManager = new EventManager();
//
//        $callback = function(Event $e) {
//            ob_end_clean();
//            echo $e->getParams()->__toString();
//            ob_start();
//        };
//
//        $eventManager->attach(ExponentialBackoff::EVENT_PRE_CALL, $callback);
//        $eventManager->attach(ExponentialBackoff::EVENT_POST_CALL, $callback);
//        $eventManager->attach(ExponentialBackoff::EVENT_POST_CALL, $callback);
//
//        $method->setEventManager($eventManager);
//        $callable = function ($status) {
//            return $status;
//        };
//
//        $callable_with_params = function ($status) {
//            return $status;
//        };
//
//        $callable_with_params_and_one_exception = function ($status) {
//            if ($status->getRetry() < 1) {
//                throw new \Exception();
//            }
//            return $status;
//        };
//
//        $this->assertInstanceOf('Eoko\ExponentialBackoff\Utils\Status', $method->exponentialBackoff($callable), __FUNCTION__, 5);
//
//        /** @var Status $result */
//        $result = $method->exponentialBackoff($callable_with_params, __FUNCTION__, 5);
//        $this->assertEquals(0, $result->getRetry());
//        $this->assertTrue($result->getSleep() < (2 * 1000000));
//
//        $result = $method->exponentialBackoff($callable_with_params_and_one_exception, __FUNCTION__, 5);
//        $this->assertEquals(1, $result->getRetry());
//        $this->assertTrue($result->getSleep() > (2*1000000) && $result->getSleep() < (3*1000000));
//
//        $result = $method->exponentialBackoff([$this, 'example'], __FUNCTION__, 5);
//        $this->assertEquals(0, $result->getRetry());
//        $this->assertTrue($result->getSleep() < (2*1000000));

    }

    /**
     * @expectedException \Exception
     */
    public function testExceptionClosure()
    {
        $callable_with_params_and_one_exception = function ($status) {
            throw new \Exception();
        };

        $eventManager = new EventManager();

        $callback = function(Event $e) {
            ob_end_clean();
            echo $e->getParams()->__toString();
            ob_start();
        };

        $eventManager->attach(ExponentialBackoff::EVENT_PRE_CALL, $callback);
        $eventManager->attach(ExponentialBackoff::EVENT_POST_CALL, $callback);
        $eventManager->attach(ExponentialBackoff::EVENT_EXCEPTION_CALL, $callback);
        $eventManager->attach(ExponentialBackoff::EVENT_SLEEP_CALL, $callback);
        $eventManager->attach(ExponentialBackoff::EVENT_RETRY_CALL, $callback);
        $eventManager->attach(ExponentialBackoff::EVENT_END_CALL, $callback);


        $method = new ExponentialBackoff();
        $method->setEventManager($eventManager);
        $method->exponentialBackoff($callable_with_params_and_one_exception, __FUNCTION__, 2);
    }
}
