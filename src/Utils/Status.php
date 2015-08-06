<?php
/**
 * Created by PhpStorm.
 * User: merlin
 * Date: 06/08/15
 * Time: 15:17
 */

namespace Eoko\ExponentialBackoff\Utils;

class Status
{
    protected $start;
    protected $maxRetry;
    protected $label;
    protected $retry;
    protected $sleep;

    public function __construct($label = 'default', $maxRetry = 5)
    {
        $this->reset($label, $maxRetry);
    }

    /**
     * Reset all params, can be used to init/re-init
     *
     * @param string $label
     * @param int $maxRetry
     */
    public function reset($label = 'default', $maxRetry = 5)
    {
        $this->start = microtime(true);
        $this->maxRetry = $maxRetry;
        $this->label = $label;
        $this->retry = 0;
        $this->sleep = 0;
    }

    /**
     * @return bool
     */
    public function retry()
    {
        $this->retry++;
        return true;
    }

    public function sleep()
    {
        usleep($this->getSleep());
    }

    public function isLoop()
    {
        if ($this->retry < $this->maxRetry) {
            return true;
        }
        return false;
    }

    public function stopLoop()
    {
        $this->retry = $this->maxRetry + 1;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     * @return $this
     */
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @return string
     */
    public function getDuration()
    {
        return (microtime(true) - $this->start);
    }

    /**
     * @return string
     */
    public function getPrintableDuration()
    {
        return $this->secondsToTime($this->getDuration());
    }

    /**
     * @return int
     */
    public function getRetry()
    {
        return $this->retry;
    }

    /**
     * @return int
     */
    public function getSleep()
    {
        return (1 << $this->retry) * 1000000 + rand(0, 1000000);
    }

    /**
     * @return int
     */
    public function getMaxRetry()
    {
        return $this->maxRetry;
    }

    /**
     * Nice time formatting from seconds
     * @param $seconds
     * @return string
     */
    private function secondsToTime($seconds)
    {
        $hours = floor($seconds / 3600);
        $seconds -= $hours * 3600;
        $minutes = floor($seconds / 60);
        $seconds -= $minutes * 60;
        return $hours . ':' . sprintf('%02d', $minutes) . ':' . sprintf('%02d', $seconds);
    }

    public function __toString()
    {
        $return = 'label : ' . $this->getLabel() . "\n";
        $return .= 'retry : ' . $this->getRetry() . '/' . $this->getMaxRetry() . "\n";
        $return .= 'sleep : ' . $this->getSleep() . "\n";
        $return .= 'duration : ' . $this->secondsToTime($this->getDuration()) . "\n";
        return $return;
    }
}
