<?php

namespace killer549\timerplus;

use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat as color;

class timerplus extends PluginBase {

	public $player;
	public $timer;


	public function onEnable(){
	}

	public function getMessagefromArray($array){
		unset($array[0]);
		return implode(' ', $array);
	}

	public function getTask(){
		$this->getServer()->getScheduler()->scheduleRepeatingTask(new task($this), 20);
	}
	
	public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
		if($command->getName() == "stimer") {
			if(!isset($args[0])) {
				$sender->sendMessage(color::RED . "Usage: /stimer <seconds> [reason]");
			}elseif($args[0] === "cancel") {
				if($this->timer != 0){
					$this->timer = 0;
					$sender->sendMessage(color::GREEN . "Cancelled successfully");
					$this->getServer()->broadcastMessage(color::RED . "Timer has stopped/cancelled");
				}else{
					$sender->sendMessage(color::RED. "Timer was not running!");
				}
			}elseif(!is_numeric($args[0])) {
				$sender->sendMessage(color::RED . "Please enter numbers or cancel");
			}elseif($args[0] < 0){
				$sender->sendMessage("Timers cannot start using negative intgers");
			}elseif($this->timer != 0){
				$sender->sendMessage(color::RED. "Timer is already running");
			}else{
				$this->timer = (int) $args[0];
				$this->getTask();
				$this->p = $sender;
				$sender->sendMessage(color::GREEN . "Activated successfully");
				$this->getServer()->broadcastMessage(color::RED . "Timer has been activated by " . $sender->getName());
				if(isset($args[1])) {
					$this->getServer()->broadcastMessage(color::RED . "Reason: " . $this->getMessagefromArray($args));
				}
			}
		}
		return true;
	}

}
