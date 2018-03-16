<?php

namespace RPGGameUI;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;
use pocketmine\network\mcpe\protocol\ModalFormResponsePacket;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\utils\Config;
use onebone\economyapi\EconomyAPI;

class Main extends PluginBase implements Listener{

    public function onEnable(){
        @mkdir($this->getDataFolder());
        $this->langDB = new Config ( $this->getDataFolder () . "lang.yml", Config::YAML,[
         "lang" => "ko",
        ]);
        $this->langDB = $this->langDB->getAll ();
    }

    public function onDisable(){
        $this->saveConfig();
    }
/* 		}
		$lang = new Config($this->getDataFolder() . "/lang.yml", Config::YAML);
		if($lang->get("en")==null)
		{
			$messages = array();
			$messages["boss"] = "§b§l[ §fBoss§b ]§f";
			$messages["character"] = "§b§l[ §fCharacter§b ]§f";
			$messages["prefix"] = "§b§l[ §fRPGGameUI§b ]§f ";
			$messages["food"] = "§b§l[ §fFood§b ]§f";
			$messages["heart"] = "§b§l[ §fHeart : §b ]§f";
			$messages["yes"] = "§b§l[ §fYes§b ]§f";
			$messages["rpgshop"] = "§b§l[ §fRPGSHOP§b ]§f";
			$messages["no"] = "§b§l[ §fNo§b ]§f";
			$messages["changelang"] = "§aComplete Change Language§f";
			$messages["langhelp"] = "§b§lUsage : /lang <ko/en> §f";
			$messages["back"] = "§b§l[ §fBack§b ]§f";
			$messages["menu"] = "§b§l[ §fMenu§b ]§f";
			$messages["foodone"] = "§b§l[ §fSuper Meat§b ]§f";
			$messages["foodtwo"] = "§b§l[ §fRed Fish§b ]§f";
			$messages["foodthree"] = "§b§l[ §fChicken§b ]§f";
			$messages["settinglang"] = "§b§l[ §fLang§b ]§f";
			$messages["name"] = "§b§l[ §fMy Name§b ]§f";
			$lang->set("en",$messages);
		}
		$lang->save();
		if($lang->get("ko")==null)
		{
			$messages = array();
			$messages["boss"] = "§b§l[ §f보스§b ]§f";
			$messages["character"] = "§b§l[ §f캐릭터§b ]§f";
			$messages["prefix"] = "§b§l[ §fRPGGameUI§b ]§f ";
			$messages["food"] = "§b§l[ §f음식§b ]§f";
			$messages["heart"] = "§b§l[ §f체력 : §b ]§f";
			$messages["yes"] = "§b§l[ §f예§b ]§f";
			$messages["rpgshop"] = "§b§l[ §fRPGSHOP§b ]§f";
			$messages["no"] = "§b§l[ §f아니오§b ]§f";
			$messages["changelang"] = "§a언어를 변경하는데 성공 하였습니다.§f";
			$messages["langhelp"] = "§b§l사용법 : /lang <ko/en> §f";
			$messages["back"] = "§b§l[ §f뒤로가기§b ]§f";
			$messages["menu"] = "§b§l[ §f메뉴§b ]§f";
			$messages["foodone"] = "§b§l[ §f슈퍼 고기§b ]§f";
			$messages["foodtwo"] = "§b§l[ §f빨간 물고기§b ]§f";
			$messages["foodthree"] = "§b§l[ §f치킨§b ]§f";
			$messages["settinglang"] = "§b§l[ §f언어§b ]§f";
			$messages["name"] = "§b§l[ §f닉네임§b ]§f";
			$lang->set("ko",$messages);
			$lang->save();
		} */
		
	public function MainMenu(){ // §b§l[ §fRPGGameUI§b ]§f
	$main = [
    "type"    => "modal",
    "title"   => "§b§l[ §fRPGGameUI§b ]§f ",
    "content" => "§b§l[ §fRPGGameUI§b ]§f",
    "button1" => "§b§l[ §f언어§b ]§f",
    "button2" => "§b§l[ §f닉네임§b ]§f",
				];
			return json_encode ( $main );
		}
		
		public function UIData(DataPacketReceiveEvent $event) {
		$p = $event->getPacket ();
		$player = $event->getPlayer ();
		if ($p instanceof ModalFormResponsePacket and $p->formId == 3450 ) {
			$responseData = json_decode($p->formData);
        if ( is_null == null ) {
			return false;
		}
		
		if ($p->formId == 3450) {
			$event->setCancelled(true);
                if ($responseData) {
					// $this->SettingLang();
				} else {
					$this->Menu();
				}
			}
		}
	}
		
    public function RPGSHOP(){ // §b§l[ §fRPGGameUI§b ]§f
	$shop = [
    "type" => "form",
    "title" => "§b§l[ §fRPGGameUI§b ]§f ",
    "content" => [
	[
	
		"text" => "§b§l[ §f음식§b ]§f",
	],
	[
		"text" => "§b§l[ §f캐릭터§b ]§f",
	],
	[
		"text" => "§b§l[ §f뒤로가기§b ]§f"
					]
					]
				];
			return json_encode ( $shop );
		}
		
		public function RPGSHOPData(DataPacketReceiveEvent $event) {
		$p = $event->getPacket ();
		$player = $event->getPlayer ();
		if ($p instanceof ModalFormResponsePacket and $p->formId == 34500 ) {
			$responseData = json_decode($p->formData);
        if ( is_null == null ) {
			return false;
		}
		
		if ($p->formId == 34500) {
			$event->setCancelled(true);
                if ($responseData) {
					$this->FoodWindow();
					$p->formId = 34501;
					$p->formData = $this->FoodWindow();
					$sender->dataPacket ($p);
				} else {
					// $this->Menu();
				// } else {
					$this->MainMenu();
					$p->formId = 3450;
					$p->formData = $this->MainMenu();
					$sender->dataPacket ($p);
				}
			}
		}
	}
	
	 public function Menu(){ // §b§l[ §fRPGGameUI§b ]§f
	$menu = [
    "type" => "form",
    "title" => "§b§l[ §fRPGGameUI§b ]§f ",
    "content" => [
	[
	
		"text" => "§b§l[ §fRPG상점§b ]§f",
	],
	[
		"text" => "§b§l[ §fTest§b ]§f",
	],
	[
		"text" => "§b§l[ §f뒤로가기§b ]§f"
					]
					]
				];
			return json_encode ( $menu );
		}
		
		public function MenuData(DataPacketReceiveEvent $event) {
		$p = $event->getPacket ();
		$player = $event->getPlayer ();
		if ($p instanceof ModalFormResponsePacket and $p->formId == 34501 ) {
			$responseData = json_decode($p->formData);
        if ( is_null == null ) {
			return false;
		}
		
		if ($p->formId == 34501) {
			$event->setCancelled(true);
                if ($responseData) {
					$this->RPGSHOP();
					$p->formId = 34500;
					$p->formData = $this->RPGSHOP();
					$sender->dataPacket ($p);
				} else {
					$this->MainMenu();
					$p->formId = 3450;
					$p->formData = $this->MainMenu();
					$sender->dataPacket ($p);
				}
			}
		}
	}
		
	public function FoodWindow(){ // §b§l[ §fRPGGameUI§b ]§f
	$food = [
    "type" => "form",
    "title" => "§b§l[ §fRPGGameUI§b ]§f ",
    "content" => [
	[
	
		"text" => "§b§l[ §f슈퍼 고기§b ]§f",
	],
	[
		"text" => "§b§l[ §f빨간 물고기§b ]§f",
	],
	[
		"text" => "§b§l[ §f치킨§b ]§f",
	],
	[
		"text" => "§b§l[ §f뒤로가기§b ]§f"
					]
					]
				];
			return json_encode ( $food );
		}
		
		public function FoodData(DataPacketReceiveEvent $event) {
		$p = $event->getPacket ();
		$player = $event->getPlayer ();
		if ($p instanceof ModalFormResponsePacket and $p->formId == 34502 ) {
			$responseData = json_decode($p->formData);
        if ( is_null == null ) {
			return false;
		}
		
		if ($p->formId == 34502) {
			$event->setCancelled(true);
                if ($responseData) {
					// $this->SuperMeatWindow();
				// } else {
					// $this->RedFishWindow();
				// } else {
					// $this->ChickenWindow();
				} else {
					$this->MainMenu();
					$p->formId = 3450;
					$p->formData = $this->MainMenu();
					$sender->dataPacket ($p);
					}
				}
			}
		}
		
		/* public function onInteract(PlayerInteractEvent $ev) {
		if ($ev->getItem()->getId() === '339') {
			$this->MainMenu();
		}
	} */
		
		public function onCommand(CommandSender $sender, Command $cmd, string $label,array $args) : bool {
			
		switch($cmd->getName()){
		
			case "rpgui":			    
				if($sender instanceof Player) {
                $p = new ModalFormRequestPacket ();
				$p->formId = 3450;
				$p->formData = $this->MainMenu();
				$sender->dataPacket ($p);
				return true;
                break;
					}
				}
			}
		}
					