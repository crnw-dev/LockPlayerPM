<?php


declare(strict_types=1);

namespace Clouria\LockPlayerPM\events;

use Clouria\LockPlayerPM\LockerInterface;
use pocketmine\event\Cancellable;
use pocketmine\event\player\PlayerEntityInteractEvent;
use pocketmine\player\Player;

class PlayerEntityInteract implements LockerInterface
{

    public function lock(
        Cancellable $event,
        Player      $player
    ) : void
    {
        if (
            (!$event instanceof PlayerEntityInteractEvent)
            or
            ($event->getPlayer() !== $player)
        ) {
            return;
        }
        $event->cancel();
    }
}