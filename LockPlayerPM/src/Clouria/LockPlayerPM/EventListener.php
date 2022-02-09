<?php


declare(strict_types=1);

namespace Clouria\LockPlayerPM;

use Closure;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockItemPickupEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityItemPickupEvent;
use pocketmine\event\inventory\InventoryOpenEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemConsumeEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\player\Player;
use pocketmine\Server;
use function explode;
use function substr;
use function trim;

class EventListener implements Listener
{

    public function __construct(
        private Closure $debugCallable
    )
    {
    }

    private function debug(
        string $string
    ) : void
    {
        ($this->debugCallable)($string);
    }

    /**
     * @var Player[] Key = player UUID in 16 bytes.
     */
    private array $players = [];

    public function lock(
        Player  $player,
        bool    $canMove,
        Closure $commandFilter,
        Closure $entityFilter,
        Closure $interactionFilter
    ) : callable
    {
        $this->debug("Locking player {$player->getName()}");
        $key = $player->getUniqueId()->getBytes();
        if (isset($this->players[$key])) {
            $this->debug("Player is already locked, overriding previous lock");
        }
        $this->players[$key] = [
            $canMove,
            $commandFilter,
            $entityFilter,
            $interactionFilter
        ];
        return fn() => $this->unlock($player);
    }

    /** @noinspection PhpUnused */
    public function onPlayerQuitEvent(PlayerQuitEvent $event) : void
    {
        $this->unlock($event->getPlayer());
    }

    private function unlock(Player $player) : void
    {
        $this->debug("Unlocking player {$player->getName()}");
        unset(
            $this->players[$player->getUniqueId()->getBytes()]
        );
    }

    private function isNotLocked(Player $player) : bool
    {
        return !isset(
            $this->players[$player->getUniqueId()->getBytes()]
        );
    }

    /**
     * @param PlayerCommandPreprocessEvent $event
     *
     * @priority MONITOR
     */
    public function onPlayerCommandPreprocessEvent(PlayerCommandPreprocessEvent $event) : void
    {
        if ($this->isNotLocked($event->getPlayer())) {
            return;
        }
        $command = Server::getInstance()->getCommandMap()->getCommand(
            substr(
                explode(
                    " ",
                    trim($event->getMessage())
                )[0],
                1
            )
        );
        if ($command instanceof IgnoreAuthenticationCommandInterface) {
            return;
        }
        $event->cancel();
    }

    /**
     * @param PlayerMoveEvent $event
     *
     * @priority MONITOR
     */
    public function onPlayerMoveEvent(PlayerMoveEvent $event)
    {
        $event->getPlayer()->onGround = true;
    }

    /**
     * @param PlayerInteractEvent $event
     *
     * @priority MONITOR
     */
    public function onPlayerInteractEvent(PlayerInteractEvent $event)
    {
    }

    /**
     * @param PlayerDropItemEvent $event
     *
     * @priority MONITOR
     */
    public function onPlayerDropItemEvent(PlayerDropItemEvent $event)
    {

    }

    /**
     * @param PlayerItemConsumeEvent $event
     *
     * @priority MONITOR
     */
    public function onPlayerItemConsumeEvent(PlayerItemConsumeEvent $event)
    {

    }

    /**
     * @param EntityDamageEvent $event
     *
     * @priority MONITOR
     */
    public function onEntityDamageEvent(EntityDamageEvent $event)
    {
        if ($event instanceof EntityDamageByEntityEvent) {

        }
    }

    /**
     * @param BlockBreakEvent $event
     *
     * @priority MONITOR
     */
    public function onBlockBreakEvent(BlockBreakEvent $event)
    {

    }

    /**
     * @param BlockPlaceEvent $event
     *
     * @priority MONITOR
     */
    public function onBlockPlaceEvent(BlockPlaceEvent $event)
    {

    }

    /**
     * @param InventoryOpenEvent $event
     *
     * @priority MONITOR
     */
    public function onInventoryOpen(InventoryOpenEvent $event)
    {

    }

    /**
     * @param BlockItemPickupEvent $event
     *
     * @priority MONITOR
     */
    public function onBlockItemPickupEvent(BlockItemPickupEvent $event)
    {

    }

    /**
     * @param EntityItemPickupEvent $event
     *
     * @priority MONITOR
     */
    public function onEntityItemPickupEvent(EntityItemPickupEvent $event)
    {

    }

}