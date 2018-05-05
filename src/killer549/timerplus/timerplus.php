<?php

namespace killer549\timerplus;

use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\lang\TranslationContainer;
use pocketmine\utils\TextFormat as color;

class timerplus extends PluginBase {

	public $sender;
	public $timer;
	private $cmd = "";


	public function onEnable(){
	}

	public function getMessagefromArray($array){
		unset($array[0], $array[1]);
		return implode(' ', $array);
	}
	
	public function getCmdfromArray($array){
		unset($array[0]);
		return implode(' ', $array);
	}
	
	public function startTask(CommandSender $player, int $seconds){
		$this->timer = $seconds;
		$this->sender = $player;
		return $this->getServer()->getScheduler()->scheduleRepeatingTask(new task($this), 20);
	}
	
	public function stopTask($taskid){
		$this->getServer()->getScheduler()->cancelTask($taskid);
		if($this->timer == 0){
			$this->sender->sendMessage(color::GREEN. "Finished counting-down.");
			if($this->cmd !== ""){
				$this->getServer()->dispatchCommand($this->sender, $this->cmd);
			}
		}
		$this->cmd = "";
		$this->timer = 0;
	}

	public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
		if($command->getName() == "stimer") {			
			switch($args[0]){
				case "set":
					if(!$sender->isOp()){
						return $sender->sendMessage(color::RED."You do not have permission to use this command");
					}
					if(!isset($args[1])){
					$sender->sendMessage(color::RED."Usage: /stimer set <seconds> [reason]");
					}elseif(!is_numeric($args[1])) {
					$sender->sendMessage(color::RED . "Type numbers only!");
					}elseif($args[1] < 0){
					$sender->sendMessage("Timers cannot start using negative intgers!");
					}elseif($this->timer != 0){
					$sender->sendMessage(color::RED. "Timer is already running!");	
					}else{
					$this->startTask($sender, (int) $args[1]);
					$sender->sendMessage(color::GREEN . "Activated successfully.");
					$this->getServer()->broadcastMessage(color::RED . "Timer has been activated by " . $sender->getName());
					if(isset($args[2])){
					$this->getServer()->broadcastMessage(color::RED . "Reason: " . $this->getMessagefromArray($args));
					}
				}
				break;
				
				case "cmd":	
					if(!$sender->isOp()){
					return $sender->sendMessage(color::RED."You do not have permission to use this command");
					}
					if(!isset($args[1])){
					$sender->sendMessage(color::RED."Usage: /stimer cmd <command>");
					}elseif($this->timer == 0){
					$sender->sendMessage(color::RED."Start the timer first!");
					}else{
					if($sender != $this->sender){
						$sender->sendMessage(color::GOLD."You aren't the timer starter");
						return true;
					}
					$this->cmd = $this->getCmdfromArray($args);
					}
				break;
				
				case "cancel":
				case "stop":
					if(!$sender->isOp()){
					return $sender->sendMessage(color::RED."You do not have permission to use this command");
					}
					if($this->timer != 0){
					$this->timer = -1;
					$sender->sendMessage(color::GREEN . "Stopped successfully");
					$this->getServer()->broadcastMessage(color::RED . "Timer has been stopped/cancelled");
					}else{
					$sender->sendMessage(color::RED. "Timer was not running!");
				}
				break;
				
				case "help":
				default:
					$sender->sendMessage(color::GOLD."[TimerPlus] Showing all available commands");
					$sender->sendMessage(color::BLUE."/stimer cancel|stop : cancels the timer");
					$sender->sendMessage(color::BLUE."/stimer cmd : Executes a command when counting down finishes");
					$sender->sendMessage(color::BLUE."/stimer set : Sets amount of seconds and starts timer");
			}
			
		return true;
		}
	}

}
