<?php


declare(strict_types=1);

namespace Clouria\LockPlayerPM\events;

use Clouria\LockPlayerPM\LockerBase;
use pocketmine\event\player\PlayerItemHeldEvent;

class PlayerItemHeld extends LockerBase
{

    protected array $events = [PlayerItemHeldEvent::class];

}