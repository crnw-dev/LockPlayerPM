<?php


declare(strict_types=1);

namespace Clouria\LockPlayerPM;

use function array_merge;

abstract class LockerBase
{

    /**
     * @var string[]
     * @phpstan-param class-string<Event>
     */
    protected array $events = [];

    public function addLocker(LockerBase $locker) : void
    {
        $this->events = array_merge($this->events, $locker->getEvents());
    }

    /**
     * @return string[]
     */
    public function getEvents() : array
    {
        return $this->events;
    }

}