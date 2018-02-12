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
        if ($packet instanceof ModalFormResponsePacket) { // 폼에 대한 응답
            $responseData = json_decode($packet->formData);
            if (is_null($responseData)) { // 선택없이 닫힌 경우
                return; // 아무 작동도 하지않고 중단합니다
            }
            if ($packet->formId == 2225) { // 메인 폼에 대한 응답
                $formPacket = new ModalFormRequestPacket ();
                if ($responseData) { // button1: 보스전을 선택한 경우
                    $formPacket->formId = 2226;
                    $formPacket->formData = json_encode([
                      "type"    => "modal",
                      "title"   => "§l§d[ §fRPGGameUI §d]§r§f",
                      "content" => "§l§c보스전",
                      "button1" => "§l§c[ §f공격 §c]§r§f",
                      "button2" => "§l§d[ §f방어 §d]§r§f",
                    ]);
                } else { // button2: 메뉴를 선택한 경우
                    $formPacket->formId = 2227;
                    $formPacket->formData = json_encode([
                      "type"    => "form",
                      "title"   => "§l§d[ §fRPGGameUI §d]§r§f",
                      "content" => "§l§c메뉴",
                      "buttons" => [
                        [
                          'type' => "button",
                          'text' => "§l§c[ §fRPG상점 §c]§r§f",
                        ],
                      ],
                    ]);
                }
                $event->getPlayer()->dataPacket($formPacket);
            } elseif ($packet->formId == 2226) { // 보스전 폼에 대한 응답
                if ($responseData) { // button1: 공격을 선택한 경우
                    $config = $this->getConfig();
                    $health = $config->get('체력');
                    $point = $config->get('포인트');
                    $formPacket = new ModalFormRequestPacket ();
                    if (mt_rand(0, 1)) {
                        $formPacket->formId = 2228;
                        $formPacket->formData = json_encode([
                          "type"    => "modal",
                          "title"   => "§l§d[ §fRPGGameUI §d]§r§f",
                          "content" => "§l§d[ §fRPG §d]§r§f 보스에게 공격을 게시 하셨습니다!/n§a현재 체력 : {$health}",
                        ]);
                        // Todo : 다시 보스전 화면으로 돌아갑니다
                    } else {
                        $rand = mt_rand(1, 100);
                        if ($rand <= 25) {
                            $health -= 1500;
                            $formPacket->formId = 2228;
                            $formPacket->formData = json_encode([
                              "type"    => "modal",
                              "title"   => "§l§d[ §fRPGGameUI §d]§r§f",
                              "content" => "§l§d[ §fRPG §d]§r§f 보스가 죽고 돈 1만원을 받았습니다!/n§a현재 체력 : {$health}",
                            ]);
                            $config->set('체력', $health);
                            EconomyAPI::getInstance()->addmoney($event->getPlayer(), 10000);
                        } elseif ($rand <= 30) {
                            $health -= 1500;
                            $formPacket->formId = 2228;
                            $formPacket->formData = json_encode([
                              "type"    => "modal",
                              "title"   => "§l§d[ §fRPGGameUI §d]§r§f",
                              "content" => "§l§d[ §fRPG §d]§r§f 보스에게 협상을 하면서 몰래 죽여서 돈 5천원을 얻었습니다!/n§a현재 체력 : {$health}",
                            ]);
                            $config->set('체력', $health);
                            EconomyAPI::getInstance()->addmoney($event->getPlayer(), 5000);
                        } elseif ($rand <= 50) {
                            $health -= 1500;
                            $formPacket->formId = 2228;
                            $formPacket->formData = json_encode([
                              "type"    => "modal",
                              "title"   => "§l§d[ §fRPGGameUI §d]§r§f",
                              "content" => "§l§d[ §fRPG §d]§r§f 보스에게 피해를 입혔습니다!/n§a현재 체력 : {$health}",
                            ]);
                            $config->set('체력', $health);
                            // Todo : 다시 보스전 화면으로 돌아갑니다
                        } elseif ($rand <= 65) { // 이 후로는 이해가 불가능한 확률을 사용하셨기 때문에 유추되는 확률로 적겠습니다
                            $health -= 1500;
                            $formPacket->formId = 2228;
                            $formPacket->formData = json_encode([
                              "type"    => "modal",
                              "title"   => "§l§d[ §fRPGGameUI §d]§r§f",
                              "content" => "§l§d[ §fRPG §d]§r§f 보스에게 협박을 하다가 보스가 귀찮아서 자살 하였습니다!",
                            ]);
                            $config->set('체력', $health);
                            $config->set('포인트', $point + 55);
                        } elseif ($rand <= 100) {
                            $health -= 1500;
                            $formPacket->formId = 2228;
                            $formPacket->formData = json_encode([
                              "type"    => "modal",
                              "title"   => "§l§d[ §fRPGGameUI §d]§r§f",
                              "content" => "§l§d[ §fRPG §d]§r§f 보스를 죽이고 150포인트를 받았습니다!/n§a현재 체력 : {$health}",
                            ]);
                            $config->set('체력', $health);
                            $config->set('포인트', $point + 150);
                        }
                    }
                } else { // button2: 방어를 선택한 경우

                }
            } elseif ($packet->formId == 2227) { // 메뉴 폼에 대한 응답

            }
        }
    }
    /*
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
                    } else {
                        } else {
                            } else {
                                if (mt_rand(1, 100) <= 50) {
                                    return $text = [
                                      "type"    => "modal",
                                      "title"   => "§l§d[ §fRPGGameUI §d]§r§f",
                                      "content" => "§l§d[ §fRPG §d]§r§f 보스에게 사망 당하였습니다...!/n§a현재 체력 : {$this->db ["체력"]}",
                                    ];
                                    return false;
                                } else {
                                    } else {
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
    }*/
}