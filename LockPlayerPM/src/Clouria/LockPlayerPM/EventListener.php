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

    public function lock(
        Player $player
    ) : callable
    {

    }

    /**
     * @param PlayerCommandPreprocessEvent $event
     *
     * @priority MONITOR
     */
    public function onPlayerCommandPreprocessEvent(PlayerCommandPreprocessEvent $event) : void
    {
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