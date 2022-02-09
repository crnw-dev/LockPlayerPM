<?php


declare(strict_types=1);

namespace Clouria\LockPlayerPM;

use Closure;
use pocketmine\command\Command;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\world\Position;
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
        return $this->lockWithSpecifiedExceptions($player);
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

    public function lockWithExceptions(
        Player  $player,
        bool    $canMove,
        Closure $commandFilter,
        Closure $entityFilter,
        Closure $interactionFilter
    ) : callable
    {

    }

    /**
     * @param Player $player
     * @param bool $canMove
     * @param Command[] $commands
     * @param int[] $entityIds
     * @param Position[] $blockPositions
     * @return callable
     */
    public function lockWithSpecifiedExceptions(
        Player $player,
        bool   $canMove = false,
        array  $commands = [],
        array  $entityIds = [],
        array  $blockPositions = []
    ) : callable
    {
        return $this->lockWithExceptions(
            $player,
            $canMove,
            fn(PlayerCommandPreprocessEvent $event) => $this->commandFilter(
                $commands,
                $event
            ),
            function (EntityDamageByEntityEvent $event) use
            (
                $entityIds
            ) : bool {
                foreach ($entityIds as $entityId) {
                    if ($entityId === $event->getEntity()->getId()) {
                        return true;
                    }
                }
                return false;
            },
            function (PlayerInteractEvent $event) use
            (
                $blockPositions
            ) : bool {
                foreach ($blockPositions as $position) {
                    if ($position->equals(
                        $event->getBlock()->getPosition()
                    )) {
                        return true;
                    }
                }
                return false;
            }
        );
    }

}