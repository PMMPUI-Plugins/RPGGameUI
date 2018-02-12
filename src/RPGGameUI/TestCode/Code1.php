<?php

				public function AT() { //다음 버전에 업그레이드 할거임 ㅎㅎ
		        while (true) {
			if (mt_rand(0, 1)) {
				$text = [
                    "type" => "modal",
                    "title" => "§l§d[ §fRPGGameUI §d]§r§f",
                    "content" => "§l§d[ §fRPG §d]§r§f 보스에게 공격을 게시 하셨습니다!/n§a현재 체력 : {$this->db ["체력"]}",
			    ];
			$this->BOSS();
			} else if (mt_rand(1, 100) <= 25) {
				return $text = [
                    "type" => "modal",
                    "title" => "§l§d[ §fRPGGameUI §d]§r§f",
                    "content" => "§l§d[ §fRPG §d]§r§f 보스가 죽고 돈 1만원을 받았습니다!/n§a현재 체력 : {$this->db ["체력"]}",
			    ];
				EconomyAPI::getInstance()->addmoney($player, 10000);
				$this->db [strtolower($pname)] ["체력"] -= 1500;
				$this->onSave();
			} else if (mt_rand(1, 100) <= 30) {
				return $text = [
                    "type" => "modal",
                    "title" => "§l§d[ §fRPGGameUI §d]§r§f",
                    "content" => "§l§d[ §fRPG §d]§r§f 보스에게 협상을 하면서 몰래 죽여서 돈 5천원을 얻었습니다!/n§a현재 체력 : {$this->db ["체력"]}",
			    ];
				EconomyAPI::getInstance()->addmoney($player, 5000);
				$this->db [strtolower($pname)] ["체력"] -= 1500;
				$this->onSave();
			} else if (mt_rand(1, 100) <= 50) {
				return $text = [
                    "type" => "modal",
                    "title" => "§l§d[ §fRPGGameUI §d]§r§f",
                    "content" => "§l§d[ §fRPG §d]§r§f 보스에게 피해를 입혔습니다!/n§a현재 체력 : {$this->db ["체력"]}",
			    ];
				$this->db [strtolower($pname)] ["체력"] -= 150;
                $this->onSave();
				$this->BOSS();
			} else if (mt_rand(1, 100) <= 50) {
				return $text = [
                    "type" => "modal",
                    "title" => "§l§d[ §fRPGGameUI §d]§r§f",
                    "content" => "§l§d[ §fRPG §d]§r§f 보스에게 사망 당하였습니다...!/n§a현재 체력 : {$this->db ["체력"]}",
			    ];
				return false;
			} else if (mt_rand(1, 100) <= 15) {
				return $text = [
                    "type" => "modal",
                    "title" => "§l§d[ §fRPGGameUI §d]§r§f",
                    "content" => "§l§d[ §fRPG §d]§r§f 보스에게 협박을 하다가 보스가 귀찮아서 자살 하였습니다!",
			    ];
				$this->db [strtolower($pname)] ["체력"] -= 1500;
				$this->db [strtolower($pname)] ["포인트"] += 55;
                $this->onSave();
			} else if (mt_rand(1, 100) <= 35) {
				return $text = [
                    "type" => "modal",
                    "title" => "§l§d[ §fRPGGameUI §d]§r§f",
                    "content" => "§l§d[ §fRPG §d]§r§f 보스를 죽이고 150포인트를 받았습니다!/n§a현재 체력 : {$this->db ["체력"]}",
			    ];
				$this->db [strtolower($pname)] ["체력"] -= 1500;
				$this->db [strtolower($pname)] ["포인트"] += 150;
                $this->onSave();
			} else if (mt_rand(1, 100) <= 45) {
				return $text = [
                    "type" => "modal",
                    "title" => "§l§d[ §fRPGGameUI §d]§r§f",
                    "content" => "§l§d[ §fRPG §d]§r§f 보스에게 큰 피해를 입혔습니다!/n§a현재 체력 : {$this->db ["체력"]}",
			    ];
				$this->db [strtolower($pname)] ["체력"] -= 550;
                $this->onSave();
				$this->BOSS();
				
			            }
			        }
                }
				
				public function LevelUP() {
		        while (true) {
                $text = [
                    "type" => "modal",
                    "title" => "§l§d[ §fRPGGameUI §d]§r§f",
                    "content" => "§l§d[ §fRPG §d]§r§f Level UP! §d현재 레벨 : {$this->db ["레벨"]}");
                $this->db [strtolower($pname)] ["레벨"] += 1;
                $this->onSave();
                				
                }
				
				public function Menu() {
		        while (true) {
                	$player->sendMessage("§l§d[ §fRPG §d]§r§f 메뉴로 이동하셨습니다.");
					$text = [
                    "type" => "modal",
                    "title" => "§l§d[ §fRPGGameUI §d]§r§f",
                    "content" => "§l§c메뉴",
                    "button1" => "§l§c[ §fRPG상점 §c]§r§f",
                    ];
					
			            }
			        }
                }
					
					public function BOSS() {
		        while (true) {
                	$player->sendMessage("§l§d[ §fRPG §d]§r§f 보스전에 참가 하셨습니다.");
					$text = [
                    "type" => "modal",
                    "title" => "§l§d[ §fRPGGameUI §d]§r§f",
                    "content" => "§l§c보스전",
                    "button1" => "§l§c[ §f공격 §c]§r§f",
                    "button2" => "§l§d[ §f방어 §d]§r§f",		
                    ];
			            }
			        }
                }
     