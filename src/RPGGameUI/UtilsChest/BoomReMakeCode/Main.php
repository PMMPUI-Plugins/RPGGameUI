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
    "content" => "§b§l[ §f레벨 : 1§b ]§f",
    "button1" => "§b§l[ §f언어§b ]§f",
    "button2" => "§b§l[ §f메뉴§b ]§f",
				];
			return json_encode ( $main );
		}
		
		public function UIData(DataPacketReceiveEvent $event) {
		$p = $event->getPacket ();
		$player = $event->getPlayer ();
		if ($p instanceof ModalFormResponsePacket and $p->formId == 3450 ) {
			$name = json_decode($p->formData, true);
			if($name) {
				$name = "true";
                $p = new ModalFormRequestPacket ();
                $p->formId = 34503;
				$p->formData = $this->SettingLang();
				$sender->dataPacket ($p);
			} else {
				$name = "false";
                $p = new ModalFormRequestPacket ();
                $p->formId = 34501;
				$p->formData = $this->Menu();
				$sender->dataPacket ($p);
				}
			}
		}
	
	public function SettingLang(){ // §b§l[ §fRPGGameUI§b ]§f
	$langform = [
    "type"    => "modal",
    "title"   => "§b§l[ §fRPGGameUI§b ]§f ",
    "content" => "§b§l[ §f언어 : 한국어§b ]§f",
    "button1" => "§b§l[ §f뒤로가기§b ]§f",
    "button2" => "§b§l[ §f창닫기§b ]§f",
				];
			return json_encode ( $langform );
		}
		
		public function LangData(DataPacketReceiveEvent $event) {
		$p = $event->getPacket ();
		$player = $event->getPlayer ();
		if ($p instanceof ModalFormResponsePacket and $p->formId == 34503 ) {
			$name = json_decode($p->formData, true);
			if($name) {
				$name = "true";
                $p = new ModalFormRequestPacket ();
                $p->formId = 3450;
				$p->formData = $this->MainMenu();
				$sender->dataPacket ($p);
			} else {
				$name = "false";
					$player->sendMessage("§b§l[ §fRPGGameUI§b ]§f 창을 닫았습니다.");
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
			$name = json_decode($p->formData, true);
			if($name) {
				if($name == 0){
				$p = new ModalFormRequestPacket ();
                $p->formId = 34502;
				$p->formData = $this->FoodWindow();
				$sender->dataPacket ($p);
			}
				if($name == 1){
			}
				if($name == 2){
                $p = new ModalFormRequestPacket ();
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
		"text" => "§c§l[ §b팀 배틀§c ]§f",
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
			$name = json_decode($p->formData, true);
			if($name) {
				if($name == 0){
                $p = new ModalFormRequestPacket ();
                $p->formId = 34500;
				$p->formData = $this->RPGSHOP();
				$sender->dataPacket ($p);
			}
				if($name == 1){
                /* $p = new ModalFormRequestPacket ();
                $p->formId = 70000;
				$p->formData = $this->TeamBattle();
				$player->dataPacket ($p); */
			}
				if($name == 2){
                $p = new ModalFormRequestPacket ();
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
			$name = json_decode($p->formData, true);
			if($name) {
				if($name == 0){
			}
				if($name == 1){
			}
				if($name == 2){
			}
				if($name == 3){
                $p = new ModalFormRequestPacket ();
                $p->formId = 3450;
				$p->formData = $this->MainMenu();
				$player->dataPacket ($p);
					}
				}
			}
		}
		
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
					