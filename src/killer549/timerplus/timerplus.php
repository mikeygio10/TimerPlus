<?php

/* _______  _                         _____   _             
 *|__   __|(_)                       |  __ \ | |            
 *   | |    _  _ __ ___    ___  _ __ | |__) || | _   _  ___ 
 *   | |   | || '_ ` _ \  / _ \| '__||  ___/ | || | | |/ __|
 *   | |   | || | | | | ||  __/| |   | |     | || |_| |\__ \
 *   |_|   |_||_| |_| |_| \___||_|   |_|     |_| \__,_||___/
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 */
namespace killer549\timerplus;

use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\utils\TextFormat as color;

class timerplus extends PluginBase {

	public $sender;
	public $timer;
	private $cmd = "";


	public function onEnable(){
		@mkdir($this->getDataFolder());
		$this->saveDefaultConfig();
	}

	public function getMessagefromArray($array){
		unset($array[0], $array[1]);
		return implode(' ', $array);
	}
	
	public function getCmdfromArray($array){
		unset($array[0]);
		$this->cmd = implode(' ', $array);
		if(substr($this->cmd, 0, 1) === '/'){
			$this->cmd = substr($this->cmd, 1);
		}
		return $this->cmd;
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
	
	public function popup(){
		return new popup($this);
	}
	
	public function Title(int $seconds){
		if($seconds > $this->getConfig()->get("finishing_titles_time")){
			$this->getServer()->broadcastTitle(color::GOLD."Timer stopping in...");
		}else{
			$this->getServer()->broadcastTitle(color::YELLOW. $seconds);
		}
	}
	
	public function Sound(int $seconds){
		foreach($this->getServer()->getOnlinePlayers() as $pos){
			$this->getServer()->getLevel($pos->getLevel()->getId())->broadcastLevelEvent($pos, LevelEventPacket::EVENT_SOUND_CLICK);
		}
	}

	public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
		if($command->getName() == "stimer") {
			if(!isset($args[0])){
				$sender->sendMessage(color::RED. "Usage: /stimer help");
				return true;
			}
			switch($args[0]){
				case "set":
					if(!$sender->isOp()){
					return $sender->sendMessage(color::RED. "You do not have permission to use this command");
					}
					if(!isset($args[1])){
					$sender->sendMessage(color::RED. "Usage: /stimer set <seconds> [reason]");
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
					return $sender->sendMessage(color::RED. "You do not have permission to use this command");
					}
					if(!isset($args[1])){
					$sender->sendMessage(color::RED. "Usage: /stimer cmd <command>");
					}elseif($this->timer == 0){
					$sender->sendMessage(color::RED. "Start the timer first!");
					}elseif($sender != $this->sender){
					$sender->sendMessage(color::GOLD. "You aren't the timer activator!");
					}else{
					$cmd = $this->getCmdfromArray($args);
					$sender->sendMessage(color::RED. "Command: < ".$cmd." > has been set");
					}
				break;
				
				case "cancel":
				case "stop":
					if(!$sender->isOp()){
					return $sender->sendMessage(color::RED."You do not have permission to use this command");
					}
					if($this->timer != 0){
					$this->timer = -1; //To differentiate between time-out and cancelled
					$sender->sendMessage(color::GREEN . "Stopped successfully");
					$this->getServer()->broadcastMessage(color::RED . "Timer has been stopped/cancelled");
					}else{
					$sender->sendMessage(color::RED. "Timer was not running!");
				}
				break;
				
				case "help":
					$sender->sendMessage(color::GOLD."[TimerPlus] Showing all available commands");
					$sender->sendMessage(color::BLUE."/stimer cancel|stop : cancels the timer");
					$sender->sendMessage(color::BLUE."/stimer cmd : Executes a command when counting down finishes");
					$sender->sendMessage(color::BLUE."/stimer set : Sets amount of seconds and starts timer");
				break;
			        default :
					$sender->sendMessage(color::RED."Usage: /stimer help");
			}
			
		return true;
		}
	}

}
