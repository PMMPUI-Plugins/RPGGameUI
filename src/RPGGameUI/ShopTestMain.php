<?php
// GitHub @PresentKim @BuildLike
namespace RPGGameUI;
# Plugin Storge Use
use RPGGameUI\Sounds;
# Plugin
use pocketmine\plugin\PluginBase;
# Listener
use pocketmine\event\Listener;
# Player
use pocketmine\Player;
# Command
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
# Config
use pocketmine\utils\Config;
# Event
use pocketmine\event\player\PlayerJoinEvent;
# UI
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;
use pocketmine\network\mcpe\protocol\ModalFormResponsePacket;
use pocketmine\event\server\DataPacketReceiveEvent;
# API
use onebone\economyapi\EconomyAPI;
class Main extends PluginBase implements Listener{
    public function onEnable(){
        @mkdir($this->getDataFolder());
        $this->bossDB = new Config ( $this->getDataFolder () . "boss.yml", Config::YAML, [
		"기본체력" => "1500",
		]);
        $this->bossDB = $this->bossDB->getAll ();
		$this->levelDB = new Config ( $this->getDataFolder () . "level.yml", Config::YAML, [
		"기본레벨" => "1",
		]);
        $this->levelDB = $this->levelDB->getAll ();
		$this->pointDB = new Config ( $this->getDataFolder () . "point.yml", Config::YAML, [
		"기본포인트" => "1500",
		]);
        $this->pointDB = $this->pointDB->getAll ();
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }
    public function onDisable(){
        $this->saveConfig();
    }
	public function onJoin (PlayerJoinEvent $event){
    	$player = $event->getPlayer();
		$name = $player->getName();
		if(!isset( $this->levelDB [strtolower($name)] ) ){
			$this->levelDB [strtolower($name)] ["레벨"] = 1;
			$this->pointDB [strtolower($name)] ["포인트"] = 1500;
			$this->onSave();
		} else {
			$this->pointDB [strtolower($name)] ["보스체력"] = 1500;
			$this->onSave();
		}
	}
    public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args) : bool{
        if ($sender instanceof Player) {
            $formPacket = new ModalFormRequestPacket ();
            $formPacket->formId = 2225;
            $formPacket->formData = json_encode([
              "type"    => "modal",
              "title"   => "§l§d[ §fRPGGameUI §d]§r§f",
              "content" => "§l§cRPGUI \n§dvFinal_Beta",
              "button1" => "§l§c[ §f보스전 §c]§r§f ( Boss Battle )",
              "button2" => "§l§d[ §f메뉴 §d]§r§f ( Menu )",
            ]);
            $sender->dataPacket($formPacket);
        } else {
            $sender->sendMessage("게임 내에서만 사용 가능한 명령어 입니다");
        }
        return true;
    }
    public function RPGData(DataPacketReceiveEvent $event){
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
                    $formData["content"] = "§l§c보스전 ( Boss Battle )";
                    $formData["button1"] = "§l§c[ §f공격 §c]§r§f ( Attack )";
                    $formData["button2"] = "§l§d[ §f방어 §d]§r§f ( Defense )";
                } else { // button2: 메뉴를 선택한 경우
                    $formPacket->formId = 2227;
                    $formData["type"] = "form";
                    $formData["content"] = "§l§c메뉴 ( Menu )";
                    $formData["buttons"] = [
                      [
                        'type' => "button",
                        'text' => "§l§c[ §fRPG상점 §c]§r§f ( RPGShop )",
                      ],
                    ];
                }
            } elseif ($packet->formId == 2226) { // 보스전 폼에 대한 응답
                $event->setCancelled(true);
                if ($responseData) { // button1: 공격을 선택한 경우
                    $formPacket->formId = 2228;
                    $formData["button1"] = "§l§a[ §f다음 §a]§r§f";
                    $formData["button2"] = "§l§a[ §f포기 §a]§r§f";
					$name = $player->getName();
                    $config = $this->getConfig();
                    $health = $config->bossDB [strtolower($name)] ["보스체력"];
                    $point = $config->pointDB [strtolower($name)] ["포인트"];
                    $rand = rand(1, 500); // 원작자분께서 확률을 알려주시지 않기 때문에 빈도수로 처리합니다 : (25+30+50+50+15+35+45) * 2
                    if ($rand <= 250) { // 500 / 250
                        $formData["content"] = "§l§d[ §fRPG §d]§r§f 보스에게 공격을 게시 하셨습니다!\n§a현재 체력 : {$health}";
                    } elseif ($rand <= 275) { // 500 / 25
                        $health -= 1500;
                        EconomyAPI::getInstance()->addmoney($player, 10000);
                        $formData["content"] = "§l§d[ §fRPG §d]§r§f 보스가 죽고 돈 1만원을 받았습니다!\n§a현재 체력 : {$health}";
						return false;
                    } elseif ($rand <= 305) { // 500 / 30
                        $health -= 1500;
                        EconomyAPI::getInstance()->addmoney($$player, 5000);
                        $formData["content"] = "§l§d[ §fRPG §d]§r§f 보스에게 협상을 하면서 몰래 죽여서 돈 5천원을 얻었습니다!\n§a현재 체력 : {$health}";
						return false;
                    } elseif ($rand <= 355) { // 500 / 50
                        $health -= 100;
                        $formData["content"] = "§l§d[ §fRPG §d]§r§f 보스에게 피해를 입혔습니다!\n§a현재 체력 : {$health}";
                    } elseif ($rand <= 405) { // 500 / 50
                        $formPacket->formId = 2228;
                        $formData["content"] = "§l§d[ §fRPG §d]§r§f 보스에게 사망 당하였습니다...!\n§a현재 체력 : {$health}";
						return false;
                    } elseif ($rand <= 420) { // 500 / 15
                        $health -= 1500;
                        $point += 55;
                        $formData["content"] = "§l§d[ §fRPG §d]§r§f 보스에게 협박을 하다가 보스가 귀찮아서 자살 하였습니다!";
						return false;
                    } elseif ($rand <= 455) { // 500 / 35
                        $health -= 1500;
                        $point += 150;
                        $formData["content"] = "§l§d[ §fRPG §d]§r§f 보스를 죽이고 150포인트를 받았습니다!\n§a현재 체력 : {$health}";
						return false;
                    } else { // 500 / 45
                        $health -= 550;
                        $formPacket->formId = 2228;
                        $formData["content"] = "§l§d[ §fRPG §d]§r§f 보스에게 큰 피해를 입혔습니다!\n§a현재 체력 : {$health}";
                    }
                } else { // button2: 방어를 선택한 경우
                    $formData["button1"] = "§l§a[ §f다음 §a]§r§f";
                    $formData["button2"] = "§l§a[ §f포기 §a]§r§f";
					$name = $player->getName();
                    $config = $this->getConfig();
                    $health = $config->bossDB [strtolower($name)] ["보스체력"];
                    $point = $config->pointDB [strtolower($name)] ["포인트"];
                    $rand = rand(1, 300);
                    if ($rand <= 10) { // 500 / 10
                        $health -= 1500;
                        $point += 200;
                        $formData["content"] = "§l§d[ §fRPG §d]§r§f 방패에 맞아서 보스가 사망하여 포인트 200포인트를 받았습니다.\n§a현재 체력 : {$health}";
						return false;
                    } else { // 300 / 165
                        $formPacket->formId = 2228;
                        $formData["content"] = "§l§d[ §fRPG §d]§r§f 보스에게 방어를 하였습니다!\n§a현재 체력 : {$health}";
                    }
                }
            } elseif ($packet->formId == 2227) { // 메뉴 폼에 대한 응답
                $event->setCancelled(true);
				if ($responseData) {
                $formPacket->formId = 2229;
                $formData["type"] = "form";
                $formData["content"] = "§l§cRPGShop";
                $formData["buttons"] = [
                      [
                        'type' => "button",
                        'text' => "§l§a[ §f마인 음료수 §a]§r§f ( Mine Water )",
					],
					[
						'type' => "button",
						'text' => "§l§b[ §f그외 §b]§r§f ( More )",
                      ],
                    ];
				}
			} elseif ($packet->formId == 2229) { // 아이템 구매,판매폼에 대한 응답
                $event->setCancelled(true);
				if ($responseData) {
                $formPacket->formId = 2210;
                $formData["type"] = "form";
                $formData["content"] = "§l§cRPGShop";
                $formData["buttons"] = [
                      [
                        'type' => "button",
                        'text' => "§l§a[ §f구매 §a]§r§f ( Buy )",
					],
					[
						'type' => "button",
						'text' => "§l§b[ §f판매 §b]§r§f ( Sell )",
                      ],
                    ];
			} else {
				if ($responseData) {
				$formPacket->formId = 2211;
                $formData["type"] = "form";
                $formData["content"] = "§l§cRPGShop";
                $formData["buttons"] = [
                      [
                        'type' => "button",
                        'text' => "§l§a[ §f스킬 §a]§r§f ( Skill )",
					],
					[
						'type' => "button",
						'text' => "§l§b[ §f음식 §b]§r§f ( Food )",
                      ],
                    ];
					}
				}
			} elseif ($packet->formId == 2210) { // 마인 음료수 구매,판매 폼에 대한 응답
                $event->setCancelled(true);
				if ($responseData) {
                $formPacket->formId = 2213;
                $formData["type"] = "custom_form";
                $formData["content"] = "§l§cRPGShop";
                $formData["buttons"] = [
                      [
                        'type' => "silder",
						'min' => "0",
						'max' => "64",
                        'text' => "§l§a갯수 ( Amount )",
					],
					[
						'type' => "button",
						'text' => "§l§b[ §f구매 §b]§r§f ( Buy )",
                      ],
                    ];
			} else {
				if ($responseData) {
				$formPacket->formId = 2214;
				$formData["type"] = "custom_form";
                $formData["content"] = "§l§cRPGShop";
                $formData["buttons"] = [
                [
                        'type' => "silder",
						'min' => "0",
						'max' => "64",
                        'text' => "§l§a갯수 ( Amount )",
					],
					[
						'type' => "button",
						'text' => "§l§b[ §f판매 §b]§r§f ( Sell )",
                      ],
                    ];
					}
				}
            } elseif ($packet->formId == 2228) { // 공격 결과 폼에 대한 응답
                $event->setCancelled(true);
				if ($responseData) {
				$formPacket->formId = 2226;
                    $formData["content"] = "§l§c보스전 ( Boss Battle )";
                    $formData["button1"] = "§l§c[ §f공격 §c]§r§f ( Attack )";
                    $formData["button2"] = "§l§d[ §f방어 §d]§r§f ( Defense )";
            } else { // 이 플러그인에서 부르지 않은 폼에 대한 응답
                return; // 응답하지 않음
            }
            $formPacket->formData = json_encode($formData);
            $player->dataPacket($formPacket);
			}
        }
    }
}