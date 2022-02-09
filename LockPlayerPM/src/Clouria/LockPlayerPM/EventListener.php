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

    private function getFilters(Player $player) : ?array
    {
        return $this->players[$player->getUniqueId()->getBytes()] ?? null;
    }

    /**
     * @param PlayerCommandPreprocessEvent $event
     *
     * @priority MONITOR
     */
    public function onPlayerCommandPreprocessEvent(PlayerCommandPreprocessEvent $event) : void
    {
        $filters = $this->getFilters($event->getPlayer());
        if ($filters === null) {
            return;
        }
        if ($filters[1]($event)) {
            $this->debug("Ignored (filtered out) command execution for locked player {$event->getPlayer()->getName()}");
            return;
        }
        $this->debug("Cancelled command execution for locked player {$event->getPlayer()->getName()}");
        $event->cancel();
    }

    /**
     * @param PlayerMoveEvent $event
     *
     * @priority MONITOR
     */
    public function onPlayerMoveEvent(PlayerMoveEvent $event) : void
    {
        $filters = $this->getFilters($event->getPlayer());
        if ($filters === null) {
            return;
        }
        if ($filters[0]) {
            $this->debug("Ignored (filtered out) movement for locked player {$event->getPlayer()->getName()}");
            return;
        }
        $this->debug("Cancelled movement for locked player {$event->getPlayer()->getName()}");
        $event->cancel();
        $event->getPlayer()->onGround = true;
    }

    /**
     * @param PlayerInteractEvent $event
     *
     * @priority MONITOR
     */
    public function onPlayerInteractEvent(PlayerInteractEvent $event) : void
    {
        $filters = $this->getFilters($event->getPlayer());
        if ($filters === null) {
            return;
        }
        if ($filters[3]($event)) {
            $this->debug("Ignored (filtered out) interaction for locked player {$event->getPlayer()->getName()}");
            return;
        }
        $this->debug("Cancelled interaction for locked player {$event->getPlayer()->getName()}");
        $event->cancel();
    }

    /**
     * @param PlayerDropItemEvent $event
     *
     * @priority MONITOR
     */
    public function onPlayerDropItemEvent(PlayerDropItemEvent $event) : void
    {
        $filters = $this->getFilters($event->getPlayer());
        if ($filters === null) {
            return;
        }
        $this->debug("Cancelled item drop for locked player {$event->getPlayer()->getName()}");
        $event->cancel();
    }

    /**
     * @param PlayerItemConsumeEvent $event
     *
     * @priority MONITOR
     */
    public function onPlayerItemConsumeEvent(PlayerItemConsumeEvent $event) : void
    {
        $filters = $this->getFilters($event->getPlayer());
        if ($filters === null) {
            return;
        }
        $this->debug("Cancelled item consumption for locked player {$event->getPlayer()->getName()}");
        $event->cancel();
    }

    /**
     * @param EntityDamageEvent $event
     *
     * @priority MONITOR
     */
    public function onEntityDamageEvent(EntityDamageEvent $event) : void
    {
        $players = [$event->getEntity()];
        if ($event instanceof EntityDamageByEntityEvent) {
            $players[] = $event->getDamager();
        }
        foreach ($players as $index => $player) {
            if (!$player instanceof Player) {
                continue;
            }
            $filters = $this->getFilters($player);
            if ($filters === null) {
                continue;
            }
            if ($index === 0) {
                $this->debug("Cancelled damage for locked player {$player->getName()}");
            } elseif ($filters[2]($event)) {
                $this->debug("Ignored (filtered out) attack from locked player {$player->getName()}");
            } else {
                $this->debug("Cancelled attack from locked player {$player->getName()}");
                $event->cancel();
            }
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