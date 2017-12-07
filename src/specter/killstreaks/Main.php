<?php

namespace Infernus101;

use pocketmine\Player;
use pocketmine\Server;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\command\Command;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

class Main extends PluginBase implements Listener {
	
	public function onEnable(){
  
		$this->getServer()->getLogger()->notice("[KillStreak] Enabled! - By Infernus101 => SpecterTeam");
    
		$file = "config.yml";
    
		if(!file_exists($this->getDataFolder() . $file)){
		  @mkdir($this->getDataFolder());
		  file_put_contents($this->getDataFolder() . $file, $this->getResource($file));
		}
    
		$this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
    
		$this->players = [];
		
		$this->streaks = (new Config($this->getDataFolder() . "/streaks.json", Config::JSON));
		
		$this->getServer()->getPluginManager()->registerEvents(new PlayerEvents($this), $this);
    
	}
	
	public function onDisable(){
  
		$this->saveStreak();
		$this->getServer()->getLogger()->notice("[KillStreak] Disabled! - By Infernus101 => SpecterTeam");
    
	}
	
	public function saveStreak(){
		@unlink($this->getDataFolder() . "/streaks.json");
		$d = new Config($this->getDataFolder() . "/streaks.json", Config::JSON);
			foreach($this->players as $player => $streak){
			  $d->set($player, $streak);
			  $d->save();
			  $d->reload();
			}
	}
  
	public function getStreak(Player $player){
	  return isset($this->players[strtolower($player->getName())]) ? $this->players[strtolower($player->getName())] : 0;
	}
	
	public function addStreak(Player $player){
		$this->players[strtolower($player->getName())] = $this->getStreak($player) + 1;
	}
	
	public function removeStreak(Player $player){
		$this->players[strtolower($player->getName())] = 0;
	}
	
}
