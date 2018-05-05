<?php

namespace killer549\timerplus;

use pocketmine\scheduler\PluginTask;
use pocketmine\utils\TextFormat as color;
use killer549\timerplus\timerplus;

class task extends PluginTask {

	private $plugin;

	public function __construct(timerplus $plugin) {
		parent::__construct($plugin);
		$this->plugin = $plugin;
	}

	public function onRun(int $currentTick) {			
		$hours = floor($this->plugin->timer / 3600);
		$mins = floor(($this->plugin->timer / 60) % 60);
		$secs = $this->plugin->timer % 60;
		if($this->plugin->timer > 0) {
			$this->plugin->timer -= 1;
		}elseif($this->plugin->timer <= 0) {
			$this->plugin->stopTask($this->getTaskId());
		}
		if($hours < 1){
			$Timer = color::GOLD . "Timer: " . color::YELLOW. $mins . " : ". $secs. color::WHITE. "\n";
		}else{
			$Timer = color::GOLD . "Timer: " . color::YELLOW . $hours. " : ". $mins . " : ". $secs. color::WHITE. "\n";
		}
		
		$this->plugin->getServer()->broadcastPopup($Timer);
	}

}


