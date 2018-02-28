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
            $formPacket = new ModalFormRequestPacket ();
            $formPacket->formId = 2225;
            $formPacket->formData = json_encode([
              "type"    => "modal",
              "title"   => "§l§d[ §fRPGGameUI §d]§r§f",
              "content" => "§l§cRPGUI Beta2\n§d레벨 : {$this->getConfig()->get('레벨')}",
              "button1" => "§l§c[ §f보스전 §c]§r§f",
              "button2" => "§l§d[ §f메뉴 §d]§r§f",
            ]);
            $sender->dataPacket($formPacket);
        } else {
            $sender->sendMessage("게임 내에서만 사용 가능한 명령어 입니다");
        }
        return true;
    }

    public function setui(DataPacketReceiveEvent $event){
        $packet = $event->getPacket();
        if ($packet instanceof ModalFormResponsePacket) { // 폼에 대한 응답
            $player = $event->getPlayer()
            $responseData = json_decode($packet->formData);
            if (is_null($responseData)) { // 선택없이 닫힌 경우
                return; // 아무 작동도 하지않고 중단합니다
            }
            $formPacket = new ModalFormRequestPacket();
            $formData = [
              "type"  => "modal",
              "title" => "§l§d[ §fRPGGameUI §d]§r§f",
            ];
            if ($packet->formId == 2225) { // 메인 폼에 대한 응답
                $event->setCancelled(true);
                if ($responseData) { // button1: 보스전을 선택한 경우
                    $formPacket->formId = 2226;
                    $formData["content"] = "§l§c보스전";
                    $formData["button1"] = "§l§c[ §f공격 §c]§r§f";
                    $formData["button2"] = "§l§d[ §f방어 §d]§r§f";
                } else { // button2: 메뉴를 선택한 경우
                    $formPacket->formId = 2227;
                    $formData["type"] = "form";
                    $formData["content"] = "§l§aMenu"
                    $formData["buttons"] = [
                      [
                        'type' => "button",
                        'text' => "§l§c[ §fRPG상점 §c]§r§f",
                      ],
                      [
                        'type' => "button",
                        'text' => "§d[ §f일반전§d ] §f",
                      ],
                      [
                        'type' => "button",
                        'text' => "§d[ §f중급전§d ] §f",
                      ],
                      [
                        'type' => "button",
                        'text' => "§d[ §f고급전§d ] §f",
                      ],
                      [
                        'type' => "button",
                        'text' => "§d[ §f돌아가기§d ] §f",
                      ],
                    ];
                }
            } elseif ($packet->formId == 2226) { // 보스전 폼에 대한 응답
                $event->setCancelled(true);
                if ($responseData) { // button1: 공격을 선택한 경우
                    $formPacket->formId = 2228;
                    $formData["button1"] = "§l§c[ §f확인 §c]§r§f";
                    $formData["button2"] = "§l§d[ §f확인 §d]§r§f";
                    $config = $this->getConfig();
                    $health = $config->get('체력');
                    $point = $config->get('포인트');
                    $rand = rand(1, 500); // 원작자분께서 확률을 알려주시지 않기 때문에 빈도수로 처리합니다 : (25+30+50+50+15+35+45) * 2
                    if ($rand <= 250) { // 500 / 250
                        $formData["content"] = "§l§d[ §fRPG §d]§r§f 보스에게 공격을 게시 하셨습니다!\n§a현재 체력 : {$health}";
                    } elseif ($rand <= 275) { // 500 / 25
                        $health -= 1500;
                        EconomyAPI::getInstance()->addmoney($player, 10000);
                        $formData["content"] = "§l§d[ §fRPG §d]§r§f 보스가 죽고 돈 1만원을 받았습니다!\n§a현재 체력 : {$health}";
                    } elseif ($rand <= 305) { // 500 / 30
                        $health -= 1500;
                        EconomyAPI::getInstance()->addmoney($$player, 5000);
                        $formData["content"] = "§l§d[ §fRPG §d]§r§f 보스에게 협상을 하면서 몰래 죽여서 돈 5천원을 얻었습니다!\n§a현재 체력 : {$health}";
                    } elseif ($rand <= 355) { // 500 / 50
                        $health -= 100;
                        $formData["content"] = "§l§d[ §fRPG §d]§r§f 보스에게 피해를 입혔습니다!\n§a현재 체력 : {$health}";
                        // Todo : 다시 보스전 화면으로 돌아갑니다
                    } elseif ($rand <= 405) { // 500 / 50
                        $formPacket->formId = 2228;
                        $formData["content"] = "§l§d[ §fRPG §d]§r§f 보스에게 사망 당하였습니다...!\n§a현재 체력 : {$health}";
                    } elseif ($rand <= 420) { // 500 / 15
                        $health -= 1500;
                        $point += 55;
                        $formData["content"] = "§l§d[ §fRPG §d]§r§f 보스에게 협박을 하다가 보스가 귀찮아서 사망 하였습니다!";
                    } elseif ($rand <= 455) { // 500 / 35
                        $health -= 1500;
                        $point += 150;
                        $formData["content"] = "§l§d[ §fRPG §d]§r§f 보스를 죽이고 150포인트를 받았습니다!\n§a현재 체력 : {$health}";
                    } else { // 500 / 45
                        $health -= 550;
                        $formPacket->formId = 2228;
                        $formData["content"] = "§l§d[ §fRPG §d]§r§f 보스에게 큰 피해를 입혔습니다!\n§a현재 체력 : {$health}";
                    }
                    $config->set('체력', $health);
                    $config->set('포인트', $point);
                } else { // button2: 방어를 선택한 경우
                    } elseif ($packet->formId == 2226) { // 보스전 폼에 대한 응답
                $event->setCancelled(true);
                if ($responseData) { // button1: 방어을 선택한 경우
                    $formPacket->formId = 2228;
                    $formData["button1"] = "§l§c[ §f확인 §c]§r§f";
                    $formData["button2"] = "§l§d[ §f확인 §d]§r§f";
                    $config = $this->getConfig();
                    $health = $config->get('체력');
                    $point = $config->get('포인트');
                    $rand = rand(1, 500); // 원작자분께서 확률을 알려주시지 않기 때문에 빈도수로 처리합니다 : (25+30+50+50+15+35+45) * 2
                    if ($rand <= 250) { // 500 / 250
                        $formData["content"] = "§l§d[ §fRPG §d]§r§f 보스에게 방어을 게시 하셨습니다!\n§a현재 체력 : {$health}";
                    } elseif ($rand <= 275) { // 500 / 25
                        $health += 1500;
                        EconomyAPI::getInstance()->addmoney($player, 5500);
                        $formData["content"] = "§l§d[ §fRPG §d]§r§f 방어에 성공 하여 5500원을 얻었습니다!\n§a현재 체력 : {$health}";
                    } elseif ($rand <= 305) { // 500 / 30
                        $health -= 1500;
                        EconomyAPI::getInstance()->addmoney($$player, 5000);
                        $formData["content"] = "§l§d[ §fRPG §d]§r§f 보스가 보호막에 부딪혀서 사망하고 돈 5천원을 얻었습니다!\n§a현재 체력 : {$health}";
                    } elseif ($rand <= 355) { // 500 / 50
                        $health += 1500;
                        $formData["content"] = "§l§d[ §fRPG §d]§r§f 보스에게 방어를 하였습니다!\n§a현재 체력 : {$health}";
                    } elseif ($rand <= 405) { // 500 / 50
                        $formPacket->formId = 2228;
                        $formData["content"] = "§l§d[ §fRPG §d]§r§f 방어에 실패하여 보스에게 사망 당하였습니다...!\n§a현재 체력 : {$health}";
                    } elseif ($rand <= 420) { // 500 / 15
                        $health += 1500;
                        $point += 55;
                        $formData["content"] = "§l§d[ §fRPG §d]§r§f 보스에게 방어를 하다가 보스가 병으로 인해 사망 하였습니다!";
                    } elseif ($rand <= 455) { // 500 / 35
                        $health += 1500;
                        $point += 150;
                        $formData["content"] = "§l§d[ §fRPG §d]§r§f 방어에 성공하고 150포인트를 받았습니다!\n§a현재 체력 : {$health}";
                    } else { // 500 / 45
                        $health += 550;
                        $formPacket->formId = 2228;
                        $formData["content"] = "§l§d[ §fRPG §d]§r§f 보스에게 방어에 살짝 성공 했습니다!\n§a현재 체력 : {$health}";
                    }
            } elseif ($packet->formId == 2227) { // 메뉴 폼에 대한 응답
                $event->setCancelled(true);
                if ($responseData) { // button1: RPG상점을 선택한 경우
                    $formPacket->formId = 2229;
                    $formData["type"] = "button";
                    $formData["text"] = "§l§c[ §f레벨업 물약 §c]§r§f";
                    $formData["type"] = "button";
                    $formData["text"] = "§l§d[ §f레벨 다운 물약 §d]§r§f";
                    $formData["type"] = "button";
                    $formData["text"] = "§l§c[ §f돌아가기 §c]§r§f";
                    }
                } elseif ($packet->formId == 2229) { // 메뉴 폼에 대한 응답
                $event->setCancelled(true);
                if ($responseData) { // button1: 레벨업 물약을 선택한 경우
                    $formPacket->formId = 2210;
                    $formData["type"] = "modal";
                    $formData["title"] = "§l§a[ §aRPGShop §c]§r§f";
                    $formData["content"] = "§l§a레벨업 물약§e (10000원)§r§f";
                    $formData["button1"] = "§l§a[ §f구매 §a]§r§f";
                    $formData["button2"] = "§l§c[ §f판매 §c]§r§f";
                } else {
                    $formPacket->formId = 2211;
                    $formData["type"] = "modal";
                    $formData["title"] = "§l§a[ §aRPGShop §c]§r§f";
                    $formData["content"] = "§l§a레벨다운 물약§e (10000원)§r§f";
                    $formData["button1"] = "§l§a[ §f구매 §a]§r§f";
                    $formData["button2"] = "§l§c[ §f판매 §c]§r§f";
                    }
                } else {
                    return;
                }
            } elseif ($packet->formId == 2228) { // 공격,방어 결과 폼에 대한 응답
                $event->setCancelled(true);
                if ($responseData) { // button1: 확인을 선택한 경우
                    $formPacket->formId = 2226;
                } else {
                    $formPacket->formId = 2226;
                }
            } elseif ($packet->formId == 2210) { // 메뉴 폼에 대한 응답
                $event->setCancelled(true);
                if ($responseData) { // button1: 구매,판매를 선택한 경우
                    $formPacket->formId = 10050;
                    $formData["type"] = "custom_form";
                    $formData["title"] = "§l§a[ §f구매 §a]§r§f";
                    $formData["type"] = "slider";
                    $formData["text"] = "§l§a구매§r§f";
                    $formData["min"] = "1";
                    $formData["min"] = "128";
                } else {
                    $formPacket->formId = 10051;
                    $formData["type"] = "custom_form";
                    $formData["title"] = "§l§c[ §f판매 §c]§r§f";
                    $formData["type"] = "slider";
                    $formData["text"] = "§l§c판매§r§f";
                    $formData["min"] = "1";
                    $formData["min"] = "128";
                    }
               } elseif ($packet->formId == 10050) { // 구매 폼에 대한 응답
                $event->setCancelled(true);
                if ($responseData) { // button: 구매를 선택한 경우
                    $formPacket->formId = 10052;
                    $formData["content"] = "§l§a성공적으로 구매 하였습니다!§r§f";
                    }
                } elseif ($packet->formId == 10051) { // 판매 폼에 대한 응답
                $event->setCancelled(true);
                if ($responseData) { // button: 판매를 선택한 경우
                    $formPacket->formId = 10053;
                    $formData["content"] = "§l§a성공적으로 판매 하였습니다!§r§f";
               }
            } else { // 이 플러그인에서 부르지 않은 폼에 대한 응답
                return; // 응답하지 않음
            }
            $formPacket->formData = json_encode($formData);
            $player->dataPacket($formPacket);
     
}
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
            $formPacket = new ModalFormRequestPacket ();
            $formPacket->formId = 2225;
            $formPacket->formData = json_encode([
              "type"    => "modal",
              "title"   => "§l§d[ §fRPGGameUI §d]§r§f",
              "content" => "§l§cRPGUI Beta2\n§d레벨 : {$this->getConfig()->get('레벨')}",
              "button1" => "§l§c[ §f보스전 §c]§r§f",
              "button2" => "§l§d[ §f메뉴 §d]§r§f",
            ]);
            $sender->dataPacket($formPacket);
        } else {
            $sender->sendMessage("게임 내에서만 사용 가능한 명령어 입니다");
        }
        return true;
    }

    public function setui(DataPacketReceiveEvent $event){
        $packet = $event->getPacket();
        if ($packet instanceof ModalFormResponsePacket) { // 폼에 대한 응답
            $player = $event->getPlayer();
            $responseData = json_decode($packet->formData);
            if (is_null($responseData)) { // 선택없이 닫힌 경우
                return; // 아무 작동도 하지않고 중단합니다
            }
            $formPacket = new ModalFormRequestPacket();
            $formData = [
              "type"  => "modal",
              "title" => "§l§d[ §fRPGGameUI §d]§r§f",
            ];
            if ($packet->formId == 2225) { // 메인 폼에 대한 응답
                $event->setCancelled(true);
                if ($responseData) { // button1: 보스전을 선택한 경우
                    $formPacket->formId = 2226;
                    $formData["content"] = "§l§c보스전";
                    $formData["button1"] = "§l§c[ §f공격 §c]§r§f";
                    $formData["button2"] = "§l§d[ §f방어 §d]§r§f";
                } else { // button2: 메뉴를 선택한 경우
                    $formPacket->formId = 2227;
                    $formData["type"] = "form";
                    $formData["content"] = "§l§aMenu";
                    $formData["buttons"] = [
                      [
                        'type' => "button",
                        'text' => "§l§c[ §fRPG상점 §c]§r§f",
                      ],
                      [
                        'type' => "button",
                        'text' => "§d[ §f일반전§d ] §f",
                      ],
                      [
                        'type' => "button",
                        'text' => "§d[ §f중급전§d ] §f",
                      ],
                      [
                        'type' => "button",
                        'text' => "§d[ §f고급전§d ] §f",
                      ],
                      [
                        'type' => "button",
                        'text' => "§d[ §f돌아가기§d ] §f",
                      ],
                    ];
                }
            } elseif ($packet->formId == 2226) { // 보스전 폼에 대한 응답
                $event->setCancelled(true);
                if ($responseData) { // button1: 공격을 선택한 경우
                    $formPacket->formId = 2228;
                    $formData["button1"] = "§l§c[ §f공격 §c]§r§f";
                    $formData["button2"] = "§l§d[ §f방어 §d]§r§f";
                    $config = $this->getConfig();
                    $health = $config->get('체력');
                    $point = $config->get('포인트');
                    $rand = rand(1, 500); // 원작자분께서 확률을 알려주시지 않기 때문에 빈도수로 처리합니다 : (25+30+50+50+15+35+45) * 2
                    if ($rand <= 250) { // 500 / 250
                        $formData["content"] = "§l§d[ §fRPG §d]§r§f 보스에게 공격을 게시 하셨습니다!\n§a현재 체력 : {$health}";
                    } elseif ($rand <= 275) { // 500 / 25
                        $health -= 1500;
                        EconomyAPI::getInstance()->addmoney($player, 10000);
                        $formData["content"] = "§l§d[ §fRPG §d]§r§f 보스가 죽고 돈 1만원을 받았습니다!\n§a현재 체력 : {$health}";
                    } elseif ($rand <= 305) { // 500 / 30
                        $health -= 1500;
                        EconomyAPI::getInstance()->addmoney($player, 5000);
                        $formData["content"] = "§l§d[ §fRPG §d]§r§f 보스에게 협상을 하면서 몰래 죽여서 돈 5천원을 얻었습니다!\n§a현재 체력 : {$health}";
                    } elseif ($rand <= 355) { // 500 / 50
                        $health -= 100;
                        $formData["content"] = "§l§d[ §fRPG §d]§r§f 보스에게 피해를 입혔습니다!\n§a현재 체력 : {$health}";
                        // Todo : 다시 보스전 화면으로 돌아갑니다
                    } elseif ($rand <= 405) { // 500 / 50
                        $formPacket->formId = 2228;
                        $formData["content"] = "§l§d[ §fRPG §d]§r§f 보스에게 사망 당하였습니다...!\n§a현재 체력 : {$health}";
						return false;
                    } elseif ($rand <= 420) { // 500 / 15
                        $health -= 1500;
                        $point += 55;
                        $formData["content"] = "§l§d[ §fRPG §d]§r§f 보스에게 협박을 하다가 보스가 귀찮아서 사망 하였습니다!";
                    } elseif ($rand <= 455) { // 500 / 35
                        $health -= 1500;
                        $point += 150;
                        $formData["content"] = "§l§d[ §fRPG §d]§r§f 보스를 죽이고 150포인트를 받았습니다!\n§a현재 체력 : {$health}";
                    } else { // 500 / 45
                        $health -= 550;
                        $formPacket->formId = 2228;
                        $formData["content"] = "§l§d[ §fRPG §d]§r§f 보스에게 큰 피해를 입혔습니다!\n§a현재 체력 : {$health}";
                    }
                    $config->set('체력', $health);
                    $config->set('포인트', $point);
				    }
                } else { // button2: 방어를 선택한 경우
                    $formPacket->formId = 2555;
                    $formData["button1"] = "§l§c[ §f공격 §c]§r§f";
                    $formData["button2"] = "§l§d[ §f방어 §d]§r§f";
                    $config = $this->getConfig();
                    $health = $config->get('체력');
                    $point = $config->get('포인트');
                    $rand = rand(1, 500); // 원작자분께서 확률을 알려주시지 않기 때문에 빈도수로 처리합니다 : (25+30+50+50+15+35+45) * 2
                    if ($rand <= 250) { // 500 / 250
                        $formData["content"] = "§l§d[ §fRPG §d]§r§f 보스에게 방어을 게시 하셨습니다!\n§a현재 체력 : {$health}";
                    } elseif ($rand <= 275) { // 500 / 25
                        $health += 1500;
                        EconomyAPI::getInstance()->addmoney($player, 5500);
                        $formData["content"] = "§l§d[ §fRPG §d]§r§f 방어에 성공 하여 5500원을 얻었습니다!\n§a현재 체력 : {$health}";
                    } elseif ($rand <= 305) { // 500 / 30
                        $health -= 1500;
                        EconomyAPI::getInstance()->addmoney($player, 5000);
                        $formData["content"] = "§l§d[ §fRPG §d]§r§f 보스가 보호막에 부딪혀서 사망하고 돈 5천원을 얻었습니다!\n§a현재 체력 : {$health}";
						return false;
                    } elseif ($rand <= 355) { // 500 / 50
                        $health += 1500;
                        $formData["content"] = "§l§d[ §fRPG §d]§r§f 보스에게 방어를 하였습니다!\n§a현재 체력 : {$health}";
                    } elseif ($rand <= 405) { // 500 / 50
                        $formPacket->formId = 2228;
                        $formData["content"] = "§l§d[ §fRPG §d]§r§f 방어에 실패하여 보스에게 사망 당하였습니다...!\n§a현재 체력 : {$health}";
						return false;
                    } elseif ($rand <= 420) { // 500 / 15
                        $health += 1500;
                        $point += 55;
                        $formData["content"] = "§l§d[ §fRPG §d]§r§f 보스에게 방어를 하다가 보스가 병으로 인해 사망 하였습니다!";
						return false;
                    } elseif ($rand <= 455) { // 500 / 35
                        $health += 1500;
                        $point += 150;
                        $formData["content"] = "§l§d[ §fRPG §d]§r§f 방어에 성공하고 150포인트를 받았습니다!\n§a현재 체력 : {$health}";
                    } else { // 500 / 45
                        $health += 550;
                        $formPacket->formId = 2228;
                        $formData["content"] = "§l§d[ §fRPG §d]§r§f 보스에게 방어에 살짝 성공 했습니다!\n§a현재 체력 : {$health}";
					    }
					}
            } elseif ($packet->formId == 2227) { // 메뉴 폼에 대한 응답
                $event->setCancelled(true);
                if ($responseData) { // button1: RPG상점을 선택한 경우
                    $formPacket->formId = 2229;
                    $formData["type"] = "button";
                    $formData["text"] = "§l§c[ §f레벨업 물약 §c]§r§f";
				}
				if(0 > 0){
					$formPacket->formId = 2210;
                    $formData["type"] = "button";
                    $formData["text"] = "§l§d[ §f레벨 다운 물약 §d]§r§f";
				}
				if(0 > 0){
					$formPacket->formId = 2211;
                    $formData["type"] = "button";
                    $formData["text"] = "§l§c[ §f돌아가기 §c]§r§f";
				} else {
					
                    }
                } elseif ($packet->formId == 2229) { // 메뉴 폼에 대한 응답
                $event->setCancelled(true);
                if ($responseData) { // button1: 레벨업 물약을 선택한 경우
                    $formPacket->formId = 2212;
                    $formData["type"] = "modal";
                    $formData["title"] = "§l§a[ §aRPGShop §c]§r§f";
                    $formData["content"] = "§l§a레벨업 물약§e (10000원)§r§f";
                    $formData["button1"] = "§l§a[ §f구매 §a]§r§f";
                    $formData["button2"] = "§l§c[ §f판매 §c]§r§f";
                } else {
                    $formPacket->formId = 2212;
                    $formData["type"] = "modal";
                    $formData["title"] = "§l§a[ §aRPGShop §c]§r§f";
                    $formData["content"] = "§l§a레벨다운 물약§e (10000원)§r§f";
                    $formData["button1"] = "§l§a[ §f구매 §a]§r§f";
                    $formData["button2"] = "§l§c[ §f판매 §c]§r§f";
                    }
            } elseif ($packet->formId == 2228) { // 공격,방어 결과 폼에 대한 응답
                $event->setCancelled(true);
                if ($responseData) { // button1: 확인을 선택한 경우
                    $formPacket->formId = 2226;
                } else {
                    $formPacket->formId = 2226;
                }
            } elseif ($packet->formId == 2210) { // 메뉴 폼에 대한 응답
                $event->setCancelled(true);
                if ($responseData) { // button1: 구매,판매를 선택한 경우
                    $formPacket->formId = 10050;
                    $formData["type"] = "custom_form";
                    $formData["title"] = "§l§a[ §f구매 §a]§r§f";
                    $formData["type"] = "slider";
                    $formData["text"] = "§l§a구매§r§f";
                    $formData["min"] = "1";
                    $formData["min"] = "128";
                } else {
                    $formPacket->formId = 10051;
                    $formData["type"] = "custom_form";
                    $formData["title"] = "§l§c[ §f판매 §c]§r§f";
                    $formData["type"] = "slider";
                    $formData["text"] = "§l§c판매§r§f";
                    $formData["min"] = "1";
                    $formData["min"] = "128";
                    }
               } elseif ($packet->formId == 10050) { // 구매 폼에 대한 응답
                $event->setCancelled(true);
                if ($responseData) { // button: 구매를 선택한 경우
                    $formPacket->formId = 10052;
                    $formData["content"] = "§l§a성공적으로 구매 하였습니다!§r§f";
                    }
                } elseif ($packet->formId == 10051) { // 판매 폼에 대한 응답
                $event->setCancelled(true);
                if ($responseData) { // button: 판매를 선택한 경우
                    $formPacket->formId = 10053;
                    $formData["content"] = "§l§a성공적으로 판매 하였습니다!§r§f";
               }
            } else { // 이 플러그인에서 부르지 않은 폼에 대한 응답
                return; // 응답하지 않음
            }
	        $formPacket->formData = json_encode($formData); // } 45개 { 45개
            $player->dataPacket($formPacket);
			}
}
