<?php

namespace Infernus101;

use pocketmine\command\ConsoleCommandSender;
use pocketmine\event\Listener;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\Player;

class PlayerEvents implements Listener {
	
	public $pl;
	
	public function __construct(Main $pg) {
		$this->pl = $pg;
	}
	
	public function onJoin(PlayerJoinEvent $ev){
	if(!$this->pl->streaks->get(strtolower($ev->getPlayer()->getName()))){
	  $this->pl->players[strtolower($ev->getPlayer()->getName())] = 0;
	}else{
	$this->pl->players[strtolower($ev->getPlayer()->getName())] = $this->pl->streaks->get(strtolower($ev->getPlayer()->getName()));
	}
	
	public function onDeath(PlayerDeathEvent $event){
	$p = $event->getEntity();
	if($p->getLastDamageCause() instanceof EntityDamageByEntityEvent){
		$killer = $p->getLastDamageCause()->getDamager();
		if($killer instanceof Player){
			if(strtolower($killer->getName()) != strtolower($p->getName())){
			    if($pstreak = $this->pl->getStreak($p) != 0){
			      	$this->pl->removeStreak($p);
				$message = $this->pl->config->get("streak-lose-message");
				$msg = str_replace("{streak}", "{$pstreak}", $message);
			      	$p->sendMessage($msg);
			    }
				$this->pl->addStreak($killer);
			    if($kstreak = $this->pl->getStreak($killer) != 0){
				if($comm = $this->pl->config->get($kstreak)){
				$command = str_replace("{player}", "{$killer->getName()}", $comm);
				$command = str_replace("{streak}", "{$kstreak}", $command);
				$this->pl->getServer()->dispatchCommand(new ConsoleCommandSender(), "$command");
			      	}
			    }
			}
		}
	}
	}
}
