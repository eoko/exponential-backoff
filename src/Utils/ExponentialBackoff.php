<?php
/**
 * Created by PhpStorm.
 * User: merlin
 * Date: 05/08/15
 * Time: 15:05
 */

namespace Eoko\ExponentialBackoff\Utils;

use Exception;

class ExponentialBackoff
{
    /**
     * @param callable $closure number of retry and elapsed time passed as param
     * @param int $maxRetry
     * @return mixed Closure result
     * @throws Exception
     */
    public function exponentialBackoff($closure, $maxRetry = 5)
    {
        $start = microtime(true);
        $retry = 0;

        do {
            $retry++;
            try {
                $elapsedTime = microtime(true) - $start;
                $duration = $this->secondsToTime($elapsedTime);
                return $closure(($retry - 1), $duration);
            } catch (Exception $e) {
                usleep((1 << $retry) * 1000000 + rand(0, 1000000));
                $innerException = $e;
            }
        } while ($retry <= $maxRetry);

        throw $innerException;
    }

    private function secondsToTime($s)
    {
        $h = floor($s / 3600);
        $s -= $h * 3600;
        $m = floor($s / 60);
        $s -= $m * 60;
        return $h . ':' . sprintf('%02d', $m) . ':' . sprintf('%02d', $s);
    }
}
