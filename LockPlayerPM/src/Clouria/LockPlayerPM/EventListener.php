<?php


declare(strict_types=1);

namespace Clouria\LockPlayerPM;

use Closure;
use pocketmine\command\Command;
use pocketmine\entity\Entity;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockItemPickupEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityItemPickupEvent;
use pocketmine\event\inventory\InventoryOpenEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemConsumeEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\world\Position;

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
        Player  $player
    ) : callable
    {
        $this->debug("Locking player {$player->getName()}");
        $key = $player->getUniqueId()->getBytes();
        if (isset($this->players[$key])) {
            $this->debug("Player is already locked, overriding previous lock");
        }
        $this->players[$key] = [
        ];
        return fn() => $this->unlock($player);
    }

    public function setCanMove(
        bool $value = true
    ) : void {

    }

    public function setCanChat(
        bool $value = true
    ) : void {

    }

    public function setCommandFilter(
        ?Closure $filter
    ) : void {

    }

    public function setEntityFilter(
        ?Closure $filter
    ) : void {

    }

    public function setInteractionFilter(
        ?Closure $filter
    ) : void {

    }

    public function addIgnoreCommand(
        Command $command
    ) : void {

    }

    public function addIgnoreEntity(
        Entity $entity
    ) : void {

    }

    public function addIgnoreBlockPosition(
        Position $position
    ) : void {

    }

    /**
     * @param PlayerQuitEvent $event
     * @return void
     *
     * @priority MONITOR
     */
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
     * @return void
     *
     * @priority HIGH
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
     * @return void
     *
     * @priority HIGH
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
    }

    /**
     * @param PlayerInteractEvent $event
     * @return void
     *
     * @priority HIGH
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
     * @return void
     *
     * @priority HIGH
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
     * @return void
     *
     * @priority HIGH
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
     * @return void
     *
     * @priority HIGH
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
     * @return void
     *
     * @priority HIGH
     */
    public function onBlockBreakEvent(BlockBreakEvent $event) : void
    {
        $filters = $this->getFilters($event->getPlayer());
        if ($filters === null) {
            return;
        }
        $this->debug("Cancelled a block from being broken by locked player {$event->getPlayer()->getName()}");
        $event->cancel();
    }

    /**
     * @param BlockPlaceEvent $event
     * @return void
     *
     * @priority HIGH
     */
    public function onBlockPlaceEvent(BlockPlaceEvent $event) : void
    {
        $filters = $this->getFilters($event->getPlayer());
        if ($filters === null) {
            return;
        }
        $this->debug("Cancelled a block from being placed by locked player {$event->getPlayer()->getName()}");
        $event->cancel();
    }

    /**
     * @param InventoryOpenEvent $event
     * @return void
     *
     * @priority HIGH
     */
    public function onInventoryOpen(InventoryOpenEvent $event) : void
    {
        $filters = $this->getFilters($event->getPlayer());
        if ($filters === null) {
            return;
        }
        $this->debug("Cancelled an inventory from being opened by locked player {$event->getPlayer()->getName()}");
        $event->cancel();
    }

    /**
     * @param BlockItemPickupEvent $event
     * @return void
     *
     * @priority HIGH
     */
    public function onBlockItemPickupEvent(BlockItemPickupEvent $event) : void
    {
        foreach ($this->players as $uuid => $filters) {
            $player = Server::getInstance()->getPlayerByRawUUID($uuid);
            if ($player?->getInventory() === $event->getInventory()) {
                $this->debug("Cancelled a block item from being picked up by locked player {$player->getName()}");
                $event->cancel();
            }
        }
    }

    /**
     * @param EntityItemPickupEvent $event
     * @return void
     *
     * @priority HIGH
     */
    public function onEntityItemPickupEvent(EntityItemPickupEvent $event)
    {
        foreach ($this->players as $uuid => $filters) {
            $player = Server::getInstance()->getPlayerByRawUUID($uuid);
            if ($player?->getInventory() === $event->getInventory()) {
                $this->debug("Cancelled an entity item from being picked up by locked player {$player->getName()}");
                $event->cancel();
            }
        }
    }

    /**
     * @param PlayerChatEvent $event
     * @return void
     *
     * @priority HIGH
     */
    public function onPlayerChatEvent(PlayerChatEvent $event) : void {
        // TODO
    }

}