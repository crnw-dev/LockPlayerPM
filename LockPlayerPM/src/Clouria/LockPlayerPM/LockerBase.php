<?php


declare(strict_types=1);

namespace Clouria\LockPlayerPM;

use function array_merge;

abstract class LockerBase
{

    /**
     * @var LockerBase[]
     */
    protected array $lockers = [];

    public function addLocker(LockerBase $locker) : void {
        $this->lockers[] = $locker;
    }

    public function getEvents() : array {
        $events = [];
        foreach ($this->lockers as $locker) {
            $events = array_merge($events, $locker->getEvents());
        }
        return $events;
    }

}