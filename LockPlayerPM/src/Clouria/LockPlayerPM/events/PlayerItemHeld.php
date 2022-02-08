<?php


declare(strict_types=1);

namespace Clouria\LockPlayerPM\events;

use Clouria\LockPlayerPM\LockerInterface;
use pocketmine\event\Cancellable;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\player\Player;

class PlayerItemHeld implements LockerInterface
{

    public function lock(
        Cancellable $event,
        Player      $player
    ) : void
    {
        if (
            (!$event instanceof PlayerItemHeldEvent)
            or
            ($event->getPlayer() !== $player)
        ) {
            return;
        }
        $event->cancel();
    }
}