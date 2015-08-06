<?php

namespace Eoko\ExponentialBackoff\Test;

use Eoko\ExponentialBackoff\Utils\ExponentialBackoff;
use PHPUnit_Framework_TestCase;

class ExponentialBackoffTest extends PHPUnit_Framework_TestCase
{
    public static function example($retry, $duration)
    {
        return ['retry' => $retry, 'duration' => $duration];
    }


    public function testSuccessClosure()
    {
        $method = new ExponentialBackoff();
        $callable = function () {
            return true;
        };
        $callable_with_params = function ($retry, $duration) {
            return ['retry' => $retry, 'duration' => $duration];
        };

        $callable_with_params_and_one_exception = function ($retry, $duration) {
            if ($retry < 1) {
                throw new \Exception();
            }
            return ['retry' => $retry, 'duration' => $duration];
        };

        $this->assertTrue($method->exponentialBackoff($callable), 5);

        $result = $method->exponentialBackoff($callable_with_params);
        $this->assertArrayHasKey('retry', $result);
        $this->assertArrayHasKey('duration', $result);
        $this->assertEquals(0, $result['retry']);

        $result = $method->exponentialBackoff($callable_with_params_and_one_exception, 1);
        $this->assertEquals(1, $result['retry']);
        $this->assertTrue(explode(':', $result['duration'])[2] <= 2);

        $result = $method->exponentialBackoff([$this, 'example'], 1);
        $this->assertEquals(0, $result['retry']);
    }

    /**
     * @expectedException \Exception
     */
    public function testExceptionClosure()
    {
        $callable_with_params_and_one_exception = function ($retry, $duration) {
            throw new \Exception();
        };

        $method = new ExponentialBackoff();
        $method->exponentialBackoff($callable_with_params_and_one_exception, 1);
    }
}
