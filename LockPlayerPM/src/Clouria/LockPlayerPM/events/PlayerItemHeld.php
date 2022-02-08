<?php


declare(strict_types=1);

namespace Clouria\LockPlayerPM\events;

use Clouria\LockPlayerPM\LockerInterface;
use pocketmine\event\Cancellable;
use pocketmine\event\player\PlayerItemHeldEvent;

class PlayerItemHeld implements LockerInterface
{

    public function lock(Cancellable $event) : void
    {
        if (!$event instanceof PlayerItemHeldEvent) {
            return;
        }
        $event->cancel();
    }
}