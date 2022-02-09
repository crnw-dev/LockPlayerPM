<?php


declare(strict_types=1);

namespace Clouria\LockPlayerPM;

use Closure;
use pocketmine\command\Command;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use function explode;
use function str_replace;
use function substr;
use function trim;

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
        Player  $player,
        Closure $commandFilter
    ) : callable
    {

    }

    public function lockButCanRunSpecifiedCommands(
        Player $player,
        array  $commands
    ) : callable
    {
        return $this->lockButCanRunCommands(
            $player,
            fn(PlayerCommandPreprocessEvent $event) => $this->commandFilter(
                $commands,
                $event
            )
        );
    }

    /**
     * @param Command[] $commands
     * @param PlayerCommandPreprocessEvent $event
     * @return bool
     */
    private function commandFilter(
        array                        $commands,
        PlayerCommandPreprocessEvent $event
    ) : bool
    {
        $label = substr(
            explode(
                " ",
                trim($event->getMessage())
            )[0],
            1
        );
        foreach ($commands as $command) {
            if ($command->getName() === $label) {
                return true;
            }
        }
        return false;
    }

    public function lockButCanMove(Player $player) : callable
    {
        return $this->lockWithSpecifiedExceptions($player);
    }

    public function lockWithExceptions(
        Player  $player,
        Closure $itemFilter,
        Closure $entityFilter,
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
        return $this->lockWithExceptions(
        );
    }

}