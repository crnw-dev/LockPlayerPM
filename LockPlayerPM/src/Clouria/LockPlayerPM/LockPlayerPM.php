<?php


declare(strict_types=1);

namespace Clouria\LockPlayerPM;

use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use function str_replace;

final class LockPlayerPM
{

    private function __construct()
    {

    }

    public static function init(Plugin $plugin) : self
    {
        $self = new self;
        $self->plugin = $plugin;
        $self->debug("Initialized " . str_replace(
                __NAMESPACE__ . "\\",
                "",
                __CLASS__
            ));
        return $self;
    }

    private Plugin $plugin;

    private function debug(string $string) : void
    {
        $this->plugin->getLogger()->debug($string);
    }

    public function lock(
        Player     $player,
        LockerBase $locker
    ) : callable
    {

    }

    public function unlock(
        Player     $player,
        LockerBase $locker
    ) : void
    {

    }

}