<?php

namespace RPGGameUI;

use pocketmine\Server;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\CommandExecutor;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;
use pocketmine\network\mcpe\protocol\ModalFormResponsePacket;
use pocketmine\event\server\DataPacketReceiveEvent;
use onebone\economyapi\EconomyAPI;
class Main extends PluginBase implements Listener {
	
	
    public function onEnable() {
        @mkdir($this->getDataFolder());
        $this->data = new Config ( $this->getDataFolder () . "config.yml", Config::YAML,[
        	"포인트" => $pname {$this->db ["포인트"]},
        	"레벨" => $pname {$this->db ["레벨"]},
			"보스체력" => 1500,
			"체력" => $pname {$this->db ["보스체력"]},
        ]);
        $this->db = $this->data->getAll ();
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
		
        $this->getLogger()->info("§aRPGUI 로드!");
    }
	
    public function onDisable() {
        $this->getLogger()->info("§cRPGUI 언로드!");
    }
   
	public function ui() {
        $text = [
            "type" => "modal",
            "title" => "§l§d[ §fRPGGameUI §d]§r§f",
            "content" => "§l§cRPGUI v1.0.0/n§d레벨 : {$this->db ["레벨"]}/n§a닉네임 : ".$pname."",
            "button1" => "§l§c[ §f보스전 §c]§r§f",
            "button2" => "§l§d[ §f메뉴 §d]§r§f",		
            ];
		return json_encode ( $text );
    }

  public function setui (DataPacketReceiveEvent $event) {
		$p = $event->getPacket ();
        $player = $event->getPlayer();
		$pname = $player->getName();
		if ($p instanceof ModalFormResponsePacket and $p->formId == 2225 ) {
			$name = json_decode ( $p->formData, true );

			if(name[1])
            $player->sendMessage("§l§d[ §fRPG §d]§r§f 보스전에 참가 하셨습니다.");
            $text = [
                "type" => "modal",
                "title" => "§l§d[ §fRPGGameUI §d]§r§f",
                "content" => "§l§c보스전",
                "button1" => "§l§c[ §f공격 §c]§r§f",
                "button2" => "§l§d[ §f방어 §d]§r§f",
                	return true;
                }
      if($name[1])
      $text = [
          "type" => "modal",
          "title" => "§l§d[ §fRPGGameUI §d]§r§f",
          "content" => "§l§d[ §fRPG §d]§r§f 보스에게 피해를 입혔습니다!/n§a현재 체력 : {$this->db ["체력"]}",
      ];
      $this->db [strtolower($pname)] ["체력"] -= 150;
      $this->onSave();
      $this->BOSS();
                	return true;
					
				}
    public function onCommand(CommandSender $sender, Command $cmd, string $label,array $args) : bool {
		
		switch($cmd->getName()){
		
			case "rpgui":			    
				if($sender instanceof Player) {
                $p = new ModalFormRequestPacket ();
				$p->formId = 2225;
				$p->formData = $this->ui();
				$sender->dataPacket ($p);
				return true;					 					      
						
						}
				}
				return false;
    }
	
	
	
}
