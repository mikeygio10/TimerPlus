<?php

namespace killer549\timerplus;

use pocketmine\scheduler\PluginTask;
use pocketmine\utils\TextFormat as color;
use killer549\timerplus\timerplus;
use killer549\timerplus\popup;

class task extends PluginTask{

	private $plugin;

	public function __construct(timerplus $plugin){
		parent::__construct($plugin);
		$this->plugin = $plugin;
	}

	public function onRun(int $currentTick){
		if($this->plugin->timer > 0){
			$this->plugin->timer -= 1;
		}elseif($this->plugin->timer <= 0){
			$this->plugin->stopTask($this->getTaskId());
			return true;
		}
		$hours = floor($this->plugin->timer / 3600);
		$mins = floor(($this->plugin->timer / 60) % 60);
		$secs = $this->plugin->timer % 60;
		
		if($this->plugin->timer >= 0){
			if($this->plugin->getConfig()->get("finishing_titles") === true and $this->plugin->timer <= $this->plugin->getConfig()->get("finishing_titles_time") + 3){
				$this->plugin->Title($secs);
			}
			
			if($this->plugin->getConfig()->get("finishing_sounds") === true and $this->plugin->timer <= $this->plugin->getConfig()->get("finishing_sounds_time")){
				$this->plugin->Sound($secs);
			}
		$this->plugin->popup()->popup($hours, $mins, $secs);
		}
	}

}
