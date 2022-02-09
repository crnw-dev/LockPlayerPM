<?php


declare(strict_types=1);

namespace Clouria\LockPlayerPM;

use Closure;
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
        $self->listener = new EventListener(
            fn(string $string) => $this->debug($string)
        );
        $self->debug("Initialized " . str_replace(
                __NAMESPACE__ . "\\",
                "",
                __CLASS__
            ));
        return $self;
    }

    private Plugin $plugin;

    private EventListener $listener;

    private function debug(string $string) : void
    {
        $this->plugin->getLogger()->debug($string);
    }

    /**
     * @param EventListener $listener
     */
    public function setListener(EventListener $listener) : void
    {
        $this->listener = $listener;
    }

    public function lockEverything(Player $player) : callable
    {
        return $this->listener->lock($player);
    }

    public function lockButCanRunCommands(
        Player $player,
        Closure $commandFilter
    ) : callable
    {

    }

    public function lockButCanRunSpecifiedCommands(
        Player $player,
        array  $commands
    ) : callable
    {

    }

    public function lockButCanMove(Player $player) : callable
    {

    }

    public function lockWithExceptions(
        Player $player,
        Closure $itemFilter,
        Closure $entityFilter,
        Closure $blockFilter,
        Closure $commandFilter
    ) : callable
    {

    }

    public function lockWithSpecifiedExceptions(
        Player $player,
        array  $items = [],
        array  $entityIds = [],
        array  $blockPositions = [],
        array  $commands = []
    ) : callable
    {

    }

}