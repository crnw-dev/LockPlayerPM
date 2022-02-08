<?php


declare(strict_types=1);

namespace Clouria\LockPlayerPM;

use pocketmine\event\Cancellable;

interface LockerInterface
{

    public function lock(Cancellable $event) : void;

}