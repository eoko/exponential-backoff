<?php
/**
 * Created by PhpStorm.
 * User: merlin
 * Date: 05/08/15
 * Time: 15:05
 */

namespace Eoko\ExponentialBackoff\Utils;

use Exception;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerAwareTrait;

class ExponentialBackoff implements EventManagerAwareInterface
{
    const EVENT_PRE_CALL = 'event.exponential-backoff.pre';
    const EVENT_POST_CALL = 'event.exponential-backoff.post';
    const EVENT_EXCEPTION_CALL = 'event.exponential-backoff.exception';
    const EVENT_SLEEP_CALL = 'event.exponential-backoff.sleep';
    const EVENT_RETRY_CALL = 'event.exponential-backoff.retry';
    const EVENT_END_CALL = 'event.exponential-backoff.end';

    use EventManagerAwareTrait;

    /**
     * @param callable $closure number of retry and elapsed time passed as param
     * @param string $label
     * @param int $maxRetry
     * @return mixed Closure result
     * @throws Exception
     */
    public function call($closure, $label = 'default', $maxRetry = 5)
    {
        $status = new Status($label, $maxRetry);
        $em = $this->getEventManager();

        do {
            try {
                $em->trigger(self::EVENT_PRE_CALL, $this, $status);
                $result = $closure($status);
                $em->trigger(self::EVENT_POST_CALL, $this, $status);
                return $result;
            } catch (Exception $e) {
                $em->trigger(self::EVENT_EXCEPTION_CALL, $this, $status);
                $innerException = $e;
            }

            if ($status->isLoop()) {
                $em->trigger(self::EVENT_RETRY_CALL, $this, $status);
                $status->retry();
                $em->trigger(self::EVENT_SLEEP_CALL, $this, $status);
                $status->sleep();
            }
        } while ($status->isLoop());

        $em->trigger(self::EVENT_SLEEP_CALL, $this, $status);
        throw $innerException;
    }
}
