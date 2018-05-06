<?php

namespace killer549\timerplus;

use pocketmine\utils\TextFormat as color;

class popup{
	
	private $plugin;
	
	public function __construct(timerplus $plugin) {
		$this->plugin = $plugin;
	}

	public function popup(int $hours, int $mins, int $secs){
		if($hours < 1){
			$Timer = color::GOLD . "Timer: " . color::YELLOW . $mins . " : " . $secs . color::WHITE . "\n";
		}else{
			$Timer = color::GOLD . "Timer: " . color::YELLOW . $hours . " : " . $mins . " : " . $secs . color::WHITE . "\n";
		}
		
		return $this->plugin->getServer()->broadcastPopup($Timer);
	}

}
