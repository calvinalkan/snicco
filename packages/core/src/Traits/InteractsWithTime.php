<?php

declare(strict_types=1);

namespace Snicco\Core\Traits;

use DateInterval;
use DateTimeInterface;
use Snicco\Core\Support\Carbon;

trait InteractsWithTime
{
    
    /**
     * Get the number of seconds until the given DateTime.
     *
     * @param  DateTimeInterface|DateInterval|int  $delay
     *
     * @return int
     */
    protected function secondsUntil($delay) :int
    {
        $delay = $this->parseDateInterval($delay);
        
        return $delay instanceof DateTimeInterface
            ? max(0, $delay->getTimestamp() - $this->currentTime())
            : (int) $delay;
    }
    
    /**
     * Get the "available at" UNIX timestamp.
     *
     * @param  DateTimeInterface|DateInterval|int  $delay
     *
     * @return int
     */
    protected function availableAt($delay = 0) :int
    {
        $delay = $this->parseDateInterval($delay);
        
        return $delay instanceof DateTimeInterface
            ? $delay->getTimestamp()
            : Carbon::now()->addRealSeconds($delay)->getTimestamp();
    }
    
    /**
     * If the given value is an interval, convert it to a DateTime instance.
     *
     * @param  DateTimeInterface|DateInterval|int  $delay
     *
     * @return DateTimeInterface|int
     */
    protected function parseDateInterval($delay)
    {
        if ($delay instanceof DateInterval) {
            $delay = Carbon::now()->add($delay);
        }
        
        return $delay;
    }
    
    /**
     * Get the current system time as a UNIX timestamp.
     *
     * @return int
     */
    protected function currentTime()
    {
        return Carbon::now()->getTimestamp();
    }
    
}