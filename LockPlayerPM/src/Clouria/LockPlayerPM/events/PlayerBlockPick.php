<?php


declare(strict_types=1);

namespace Clouria\LockPlayerPM\events;

use Clouria\LockPlayerPM\LockerInterface;
use pocketmine\event\Cancellable;
use pocketmine\event\player\PlayerBlockPickEvent;
use pocketmine\player\Player;

class PlayerBlockPick implements LockerInterface
{

    public function lock(
        Cancellable $event,
        Player      $player
    ) : void
    {
        if (
            (!$event instanceof PlayerBlockPickEvent)
            or
            ($event->getPlayer() !== $player)
        ) {
            return;
        }
        $event->cancel();
    }
}