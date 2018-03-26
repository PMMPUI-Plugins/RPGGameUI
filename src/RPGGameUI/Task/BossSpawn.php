<?php
namespace RPGGameUI\Task;

use pocketmine\scheduler\PluginTask;
use RPGGameUI\Main;
use pocketmine\level\sound\AnvilUseSound;
use pocketmine\level\sound\PopSound;
use pocketmine\level\sound\ClickSound;

class BossSpawn extends PluginTask {
	
	public function __construct(RPGGameUI $owner){
		parent::__construct($owner);
		parent::__construct($player);
		parent::__construct($players);
		parent::__construct($LBH);
	}
	
	public function onRun($currentTick){
		$bosshearth = mt_rand(1000, 50000);
		$levelrand = mt_rand(1, 100);
		$this->getServer()->getScheduler()->scheduleRepeatingTask(new Task($this, $this) * 20 * 60 * 3);
		$player->broadcastMessage("§l§c[ §0BOSS §c]§r§b >> {$levelrand}§6레벨 §c{$bosshearth}체력 보스가 소환 됬습니다!");
		$player->broadcastMessage("§l§c[ §0BOSS §c]§r§b >> {$levelrand}§6Level §c{$bosshearth}Hearth Boss is Spawning!");
		$player->broadcastMessage("§l§c[ §0BOSS §c]§r§b >> 보스전 시작!");
		$player->broadcastMessage("§l§c[ §0BOSS §c]§r§b >> Boss Battle is Start!");
		$player->addSound(new PopSound($players));
		$player->addSound(new AnvilUseSound($players));
		$player->addSound(new ClickSound($players));
		$LBH += $bosshearth;
			$this->owner->liveboss;
	} 20 * 60 * 3);
	
}