<?php

namespace RPGUI;

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

class Main extends PluginBase implements Listener{

    public function onEnable(){
        @mkdir($this->getDataFolder());
        $this->saveDefaultConfig();
        $this->reloadConfig();
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onDisable(){
        $this->saveConfig();
    }

    public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args) : bool{
        if ($sender instanceof Player) {
            $p = new ModalFormRequestPacket ();
            $p->formId = 2225;
            $p->formData = [
              "type"    => "modal",
              "title"   => "§l§d[ §fRPGGameUI §d]§r§f",
              "content" => "§l§cRPGUI v1.0.0/n§d레벨 : {$this->getConfig()->get('레벨')}",
              "button1" => "§l§c[ §f보스전 §c]§r§f",
              "button2" => "§l§d[ §f메뉴 §d]§r§f",
            ];
            $sender->dataPacket($p);
        } else {
            $sender->sendMessage("게임 내에서만 사용 가능한 명령어 입니다");
        }
        return true;
    }

    public function setui(DataPacketReceiveEvent $event){
        $packet = $event->getPacket();
        $player = $event->getPlayer();
        $pname = $player->getName();
        if ($packet instanceof ModalFormResponsePacket and $packet->formId == 2225) {
            $name = json_decode($packet->formData, true);

            if ($this->BOSS) {
                return true;
            }

            if ($this->Menu) {
                return true;
            }
            public
            function AT(){
                while (true) {
                    if (mt_rand(0, 1)) {
                        $text = [
                          "type"    => "modal",
                          "title"   => "§l§d[ §fRPGGameUI §d]§r§f",
                          "content" => "§l§d[ §fRPG §d]§r§f 보스에게 공격을 게시 하셨습니다!/n§a현재 체력 : {$this->db ["체력"]}",
                        ];
                        $this->BOSS();
                    } else {
                        if (mt_rand(1, 100) <= 25) {
                            return $text = [
                              "type"    => "modal",
                              "title"   => "§l§d[ §fRPGGameUI §d]§r§f",
                              "content" => "§l§d[ §fRPG §d]§r§f 보스가 죽고 돈 1만원을 받았습니다!/n§a현재 체력 : {$this->db ["체력"]}",
                            ];
                            EconomyAPI::getInstance()->addmoney($player, 10000);
                            $this->db [strtolower($pname)] ["체력"] -= 1500;
                            $this->onSave();
                        } else {
                            if (mt_rand(1, 100) <= 30) {
                                return $text = [
                                  "type"    => "modal",
                                  "title"   => "§l§d[ §fRPGGameUI §d]§r§f",
                                  "content" => "§l§d[ §fRPG §d]§r§f 보스에게 협상을 하면서 몰래 죽여서 돈 5천원을 얻었습니다!/n§a현재 체력 : {$this->db ["체력"]}",
                                ];
                                EconomyAPI::getInstance()->addmoney($player, 5000);
                                $this->db [strtolower($pname)] ["체력"] -= 1500;
                                $this->onSave();
                            } else {
                                if (mt_rand(1, 100) <= 50) {
                                    return $text = [
                                      "type"    => "modal",
                                      "title"   => "§l§d[ §fRPGGameUI §d]§r§f",
                                      "content" => "§l§d[ §fRPG §d]§r§f 보스에게 피해를 입혔습니다!/n§a현재 체력 : {$this->db ["체력"]}",
                                    ];
                                    $this->db [strtolower($pname)] ["체력"] -= 150;
                                    $this->onSave();
                                    $this->BOSS();
                                } else {
                                    if (mt_rand(1, 100) <= 50) {
                                        return $text = [
                                          "type"    => "modal",
                                          "title"   => "§l§d[ §fRPGGameUI §d]§r§f",
                                          "content" => "§l§d[ §fRPG §d]§r§f 보스에게 사망 당하였습니다...!/n§a현재 체력 : {$this->db ["체력"]}",
                                        ];
                                        return false;
                                    } else {
                                        if (mt_rand(1, 100) <= 15) {
                                            return $text = [
                                              "type"    => "modal",
                                              "title"   => "§l§d[ §fRPGGameUI §d]§r§f",
                                              "content" => "§l§d[ §fRPG §d]§r§f 보스에게 협박을 하다가 보스가 귀찮아서 자살 하였습니다!",
                                            ];
                                            $this->db [strtolower($pname)] ["체력"] -= 1500;
                                            $this->db [strtolower($pname)] ["포인트"] += 55;
                                            $this->onSave();
                                        } else {
                                            if (mt_rand(1, 100) <= 35) {
                                                return $text = [
                                                  "type"    => "modal",
                                                  "title"   => "§l§d[ §fRPGGameUI §d]§r§f",
                                                  "content" => "§l§d[ §fRPG §d]§r§f 보스를 죽이고 150포인트를 받았습니다!/n§a현재 체력 : {$this->db ["체력"]}",
                                                ];
                                                $this->db [strtolower($pname)] ["체력"] -= 1500;
                                                $this->db [strtolower($pname)] ["포인트"] += 150;
                                                $this->onSave();
                                            } else {
                                                if (mt_rand(1, 100) <= 45) {
                                                    return $text = [
                                                      "type"    => "modal",
                                                      "title"   => "§l§d[ §fRPGGameUI §d]§r§f",
                                                      "content" => "§l§d[ §fRPG §d]§r§f 보스에게 큰 피해를 입혔습니다!/n§a현재 체력 : {$this->db ["체력"]}",
                                                    ];
                                                    $this->db [strtolower($pname)] ["체력"] -= 550;
                                                    $this->onSave();
                                                    $this->BOSS();

                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            public
            function LevelUP(){
                while (true) {
                    $text = [
                      "type"    => "modal",
                      "title"   => "§l§d[ §fRPGGameUI §d]§r§f",
                      "content" => "§l§d[ §fRPG §d]§r§f Level UP! §d현재 레벨 : {$this->db ["레벨"]}");
                $this->db [strtolower($pname)] ["레벨"] += 1;
                $this->onSave();
                				
                }

                public
                function Menu(){
                    while (true) {
                        $player->sendMessage("§l§d[ §fRPG §d]§r§f 메뉴로 이동하셨습니다.");
                        $text = [
                          "type"    => "modal",
                          "title"   => "§l§d[ §fRPGGameUI §d]§r§f",
                          "content" => "§l§c메뉴",
                          "button1" => "§l§c[ §fRPG상점 §c]§r§f",
                        ];

                    }
                }
            }

            public
            function BOSS(){
                while (true) {
                    $player->sendMessage("§l§d[ §fRPG §d]§r§f 보스전에 참가 하셨습니다.");
                    $text = [
                      "type"    => "modal",
                      "title"   => "§l§d[ §fRPGGameUI §d]§r§f",
                      "content" => "§l§c보스전",
                      "button1" => "§l§c[ §f공격 §c]§r§f",
                      "button2" => "§l§d[ §f방어 §d]§r§f",
                    ];
                }
            }
        }


    }
