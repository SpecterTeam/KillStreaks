<?php

namespace specter\killstreaks;

use pocketmine\command\ConsoleCommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\{PlayerJoinEvent, PlayerDeathEvent};
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\Player;
use pocketmine\utils\Config;

class PlayerEvents implements Listener {

    public $plugin;

    public function __construct(Main $plugin) {
        $this->setPlugin($plugin);
    }

    /**
     * @param PlayerJoinEvent $ev
     */
    public function onJoin(PlayerJoinEvent $ev){
        if ($this->getPlugin()->streaks instanceof Config) {
            if ($this->getPlugin()->streaks->exists($ev->getPlayer()->getLowerCaseName(), true)) {
                $this->getPlugin()->players[$ev->getPlayer()->getLowerCaseName()] = $this->getPlugin()->streaks->get(strtolower($ev->getPlayer()->getName()));
            } else {
                $this->getPlugin()->players[$ev->getPlayer()->getLowerCaseName()] = 0;
            }
        }
    }

    /**
     * @param PlayerDeathEvent $event
     */
    public function onDeath(PlayerDeathEvent $event){
        $p = $event->getPlayer();
        if($p->getLastDamageCause() instanceof EntityDamageByEntityEvent){
            $killer = $p->getLastDamageCause()->getDamager();
            if($killer instanceof Player){
                if(strtolower($killer->getName()) != strtolower($p->getName())){
                    if($pstreak = $this->getPlugin()->getStreak($p) != 0){
                        $this->getPlugin()->resetStreak($p);
                        $p->sendMessage(str_replace("{streak}", "{$pstreak}", $this->getPlugin()->config->get("streak-lose-message")));
                    }
                    $this->getPlugin()->addStreak($killer);
                    if(($kstreak = $this->getPlugin()->getStreak($killer)) != 0){
                        $killer->sendMessage(str_replace("{streak}", "{$kstreak}", $this->getPlugin()->config->get("on-streak-message")));
                        if($command = $this->getPlugin()->config->get($kstreak)){
                            $this->getPlugin()->getServer()->dispatchCommand(new ConsoleCommandSender(), str_replace("{streak}", "{$kstreak}", str_replace("{player}", "{$killer->getName()}", $command)));
                        }
                    }
                }
            }
        }
    }

    /**
     * @return Main
     */
    public function getPlugin() : Main{
        return $this->plugin;
    }

    /**
     * @param Main $plugin
     */
    public function setPlugin(Main $plugin){
        $this->plugin = $plugin;
    }
}
