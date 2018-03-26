<?php
// GitHub @PresentKim @BuildLike
namespace RPGGameUI;
# Plugin Storge Use
use RPGGameUI\Sounds;
# PluginBase
use pocketmine\plugin\PluginBase;
use pocketmine\item\Item;
use pocketmine\block\Block;
# Listener
use pocketmine\event\Listener;
# Player
use pocketmine\Player;
# Command
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
# Config
use pocketmine\utils\Config;
# Utils
use pocketmine\utils;
# Event
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\event\player\PlayerInteractEvent;
# Sound
use pocketmine\level\sound\AnvilUseSound;
use pocketmine\level\sound\PopSound;
use pocketmine\level\sound\ClickSound;
# UI
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;
use pocketmine\network\mcpe\protocol\ModalFormResponsePacket;
use pocketmine\event\server\DataPacketReceiveEvent;
# API
use onebone\economyapi\EconomyAPI;
class Main extends PluginBase implements Listener{
	public $getOS;
	public $sender;
	public $levelrand = mt_rand("1, 100");
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
		$this->lbhDB = new Config ( $this->getDataFolder () . "liveboss.yml", Config::YAML, [
		"체력" => "0",
		]);
        $this->pointDB = $this->pointDB->getAll ();
		$this->roomDB = new Config ( $this->getDataFolder () . "rooms.yml", Config::YAML);
        $this->roomDB = $this->roomDB->getAll ();
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
		$OS = "Dev";
        if ($sender instanceof Player) {
            $formPacket = new ModalFormRequestPacket ();
            $formPacket->formId = 2225;
            $formPacket->formData = json_encode([
              "type"    => "modal",
              "title"   => "§l§d[ §fRPGGameUI §d]§r§f",
              "content" => "\n§l§cRPGUI \n§dvFinal_Beta\n§a랭크 ( Rank ) : No Rank...\n§bOS : {$OS}\n§e공지 ( Notice ) : 오류가 많습니다. \n( Too Many Errors )",
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
					  [
						'type' => "button",
						'text' => "§l§a[ §f코인 §a]§r§f ( Coin )",
					  ],
					  [
						'type' => "button",
						'text' => "§l§6[ §f팀 배틀 §6]§r§f ( Team Battle )",
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
                        EconomyAPI::getInstance()->addmoney($player, 5000);
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
						'text' => "§l§b[ §f레벨업 물약 §b]§r§f ( Level Up Potion )",
					],
					[
						'type' => "button",
						'text' => "§l§b[ §fNo More... §b]§r§f",
                      ],
                    ];
/* 				} else {
				$formPacket->formId = 2210;
                $formData["type"] = "form";
                $formData["content"] = "§l§bCoin";
                $formData["buttons"] = [
                      [
                        'type' => "button",
                        'text' => "§l§cTesting...",
                      ],
                    ];
						}
					} */
				} else {
				$formPacket->formId = 2211;
                $formData["type"] = "form";
                $formData["content"] = "§l§a팀 배틀 ( Team Battle )";
                $formData["buttons"] = [
                      [
                        'type' => "button",
                        'text' => "§l§a[ §f참가 §a]§r§f ( Join )",
					],
					[
						'type' => "button",
						'text' => "§l§b[ §f방 생성 §b]§r§f ( Add Room )",
					],
					[
						'type' => "button",
						'text' => "§l§b[ §f관전 모드 §b]§r§f ( Watch Mode )",
                      ],
                    ];
				}
			} elseif ($packet->formId == 2211) { // 팀 배틀 폼에 대한 응답
			$rooms = $Config->roomDB;
                $event->setCancelled(true);
				if ($responseData) {
                $formPacket->formId = 2212;
                $formData["type"] = "form";
                $formData["content"] = "§l§6Team Battle";
                $formData["buttons"] = [$rooms];
			} else {
				$formPacket->formId = 2213;
                $formData["type"] = "custom_form";
				$formData["title"] = "§l§d방 생성 ( Add Room )";
                $formData["content"] = [
				[
					'type' => "input",
					'text' => "§l§b방 이름 ( Room Name )"
					// 'placeholder' => "§o§c방 이름을 입력 하세요. ( Input the Room Name. )",
				],
				[
					'type' => "toggle",
					'text' => "§l§aTest",
					'default' => true,
				],
				[
					'type' => "toggle",
					'text' => "§l§c욕설 방지 모드 ( Anti-Speech Mode ) ( 오류 ( Error ) )",
					'default' => false,
				],
				[
					'type' => "dropdown",
					'options' => ["2 VS 2",(string) "3 VS 3", "4 VS 4"],
					'text' => "§l§b팀 배틀 모드 ( Team Battle Mode ) ( is Error )",
						],
					];
				}
			} elseif ($packet->formId == 2213) { // 방 생성 폼에 대한 응답
			foreach($Config->roomDB [strtolower($name)] [strtolower($roomname)]->getOnlinePlayers() as $inplayer);
			// $inplayer = foreach($Config->roomDB [strtolower($name)] [strtolower($roomname)]->getOnlinePlayers());
			$maxplayer = "?";
			$roomname = "".$responseData[0]."";
			$rooms = $Config->roomDB;
                $event->setCancelled(true);
				if ($responseData) {
					$player->sendMessage ("§b§l[ §fRPG§b ]§f 방 이름을 입력 하세요! ( Input the Room Name! )");
				}
				$Config->roomDB [strtolower($name)] += [strtolower($roomname)];
/* 			} else {
				if($responseData == true){
			}
				if($responseData == false){
			} else {
				if($responseData == true){
			}
				if($responseData == false){
			} else {
				return true;
				if($responseData == 1){
					$Config->roomDB [strtolower($name)] [strtolower($roomname)] += 4;
				if($responseData == 2){
					$Config->roomDB [strtolower($name)] [strtolower($roomname)] += 6;
				}
				if($responseData == 3){
					$Config->roomDB [strtolower($name)] [strtolower($roomname)] += 8;
				} else {
				$formPacket->formId = 2214;
                $formData["type"] = "form";
				$formData["title"] = "{$inplayer}/{$maxplayer}";
                $formData["buttons"] = [
				[
					'type' => "button",
					'text' => "§l§b준비 ( Preparations )",
						],
					]; */
						}
					}
				}
			public function liveboss(){
			$lbps = [$players];
			$LBH = $Config->lbhDB ["체력"];
			$formPacket = new ModalFormRequestPacket ();
            $formPacket->formId = 3000;
            $formPacket->formData = json_encode([
              "type"    => "modal",
              "title"   => "§l§d[ §fRPGGameUI §d]§r§f",
              "content" => "\n§l§c보스전 참가 인원 : §b{$players}명\n\n§l§cBoss Battle Join Players : §b{$players} Players\n\n\n§l§b보스 체력 : §b{$Config->lbhDB ["체력"]}\n\n§l§bBoss Hearth : §b{$Config->lbhDB ["체력"]}",
              "button1" => "§l§c[ §f공격 §c]§r§f ( Attack )",
              "button2" => "§l§d[ §f참가 인원 §d]§r§f ( Join Players )",
            ]);
			$sender->dataPacket($formPacket);
						
					}
					
		public function LiveData(DataPacketReceiveEvent $event){
        $packet = $event->getPacket();
        if ($packet instanceof ModalFormResponsePacket) { // 폼에 대한 응답
            $player = $event->getPlayer();
            $responseData = json_decode($packet->formData);
            if (is_null($responseData)) { // 선택없이 닫힌 경우
                return; // 아무 작동도 하지않고 중단합니다
            }
			
		} elseif ($packet->formId == 3000) 
{ // 공격 결과 폼에 대한 응답
			foreach($this->owner->getServer()->getOnlinePlayers() as $players){
			$AK = mt_rand(10, 100);
			$LBH = $Config->lbhDB ["체력"];
                $event->setCancelled(true);
				if($AK == 10){
					$LBH -= 10;
					$this->onSave();
					$this->liveboss();
				}
				if($AK == 20){
					$LBH -= 20;
					$this->onSave();
					$this->liveboss();
				}
				if($AK == 30){
					$LBH -= 30;
					$this->onSave();
					$this->liveboss();
				}
				if($AK == 40){
					$LBH -= 40;
					$this->onSave();
					$this->liveboss();
				}
				if($AK == 50){
					$LBH -= 50;
					$this->onSave();
					$this->liveboss();
				}
				if($AK == 60){
					$LBH -= 60;
					$this->onSave();
					$this->liveboss();
				}
				if($AK == 70){
					$LBH -= 70;
					$this->onSave();
					$this->liveboss();
				}
				if($AK == 80){
					$LBH -= 80;
					$this->onSave();
					$this->liveboss();
				}
				if($AK == 90){
					$LBH -= 90;
					$this->onSave();
					$this->liveboss();
				}
				if($AK == 100){
					$LBH -= 100;
					$this->onSave();
					$this->liveboss();
				}
				if($LBH == 0){
					$player->broadcastMessage("§l§c[ §0BOSS §c]§r§b >> 보스를 죽여서 참가 인원 전체 에게 30000원을 지급 하였습니다.");
					$player->addSound(new AnvilUseSound($players));
			} elseif ($responseData) {
				$formPacket->formId = 3001;
                $formData["type"] = "form";
                $formData["content"] = "§l§a참가자 ( Join Players )";
                $formData["buttons"] = [$players];
				}
			} elseif ($packet->formId == 2228) { // 공격 결과 폼에 대한 응답
                $event->setCancelled(true);
				if ($responseData) {
				$formPacket->formId = 2228;
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