<?php

namespace specter\killstreaks;

use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

class Main extends PluginBase {

    const CONFIG_FILE = "config.yml";
    const STREAKS_FILE = "streaks.json";

    public $players = [];

    public $streaks, $config;

    public function onEnable(){
        $this->getServer()->getLogger()->notice("[KillStreaks] Enabled! Made by " . TextFormat::UNDERLINE ."github.com/SpecterTeam");

        if(!is_dir($this->getDataFolder())) @mkdir($this->getDataFolder());

		if(!file_exists($this->getDataFolder() . self::CONFIG_FILE)) {
			@mkdir($this->getDataFolder());
			file_put_contents($this->getDataFolder() . self::CONFIG_FILE, $this->getResource(self::CONFIG_FILE));
		}
		
		$this->config = new Config($this->getDataFolder() . self::CONFIG_FILE, Config::YAML);
        $this->streaks = (new Config($this->getDataFolder() . DIRECTORY_SEPARATOR . self::STREAKS_FILE, Config::JSON));

        $this->getServer()->getPluginManager()->registerEvents(new PlayerEvents($this), $this);
    }

    public function onDisable(){
        $this->saveStreak();
        $this->getServer()->getLogger()->notice("[KillStreaks] Disabled! Made by " . TextFormat::UNDERLINE ."github.com/SpecterTeam");

    }

    public function saveStreak(){
        foreach($this->players as $player => $streak){
            if($this->streaks instanceof Config) {
                $this->streaks->set($player, $streak);
                $this->streaks->save();
            }
        }
    }

    /**
     * @param Player $player
     * @return int|mixed
     */
    public function getStreak(Player $player){
        return $this->players[strtolower($player->getName())];
    }

    /**
     * @param Player $player
     * @param int $amount
     */
    public function addStreak(Player $player, int $amount = 1){
        $this->players[strtolower($player->getName())] = $this->getStreak($player) + $amount;
    }

    /**
     * @param Player $player
     */
    public function resetStreak(Player $player){
        $this->players[strtolower($player->getName())] = 0;
    }

    /**
     * @return Player[]
     */
    public function getPlayers() : array{
        return $this->players;
    }

    /**
     * @param Player[] $players
     */
    public function setPlayers(array $players){
        $this->players = $players;
    }

}
