<?php

namespace Doma\LoginPM5;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;

class EventListener implements Listener {

    private $main;

    public function __construct(Main $main) {
        $this->main = $main;
    }

    public function onPlayerJoin(PlayerJoinEvent $event): void {
        $player = $event->getPlayer();
        $this->main->onPlayerJoin($player);
    }
}
