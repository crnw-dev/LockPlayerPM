<?php


declare(strict_types=1);

namespace Clouria\LockPlayerPM;

use pocketmine\event\Cancellable;
use pocketmine\player\Player;

interface LockerInterface
{

    public function lock(
        Cancellable $event,
        Player      $player
    ) : void;

}