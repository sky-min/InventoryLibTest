<?php
/**
 * @name test
 * @main test\InvTest
 * @author skymin
 * @version SKY
 * @api 4.0.0
 */
declare(strict_types = 1);

namespace test;

use pocketmine\plugin\PluginBase;

use pocketmine\player\Player;
use pocketmine\item\ItemFactory;

use pocketmine\command\{Command, CommandSender, PluginCommand};

use skymin\InventoryLib\{InvLibManager, LibInvType, InvLibAction, LibInventory};

class InvTest extends PluginBase{
	
	public function onEnable() :void{
		InvLibManager::register($this);
		$cmd = new PluginCommand('inv', $this, $this);
		$cmd->setDescription('InventoryLib test plugin');
		$this->getServer()->getCommandMap()->register('inv', $cmd);
	}
	
	public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
		if(!isset($args[0])) return false;
		$type = match($args[0]){
			'doublechest','dc' => LibInvType::DOUBLE_CHEST(),
			'chest', 'c' => LibInvType::CHEST(),
			'hopper', 'h' => LibInvType::HOPPER(),
			'dropper', 'd' => LibInvType::DROPPER(),
			default => LibInvType::DOUBLE_CHEST()
		};
		$inv = InvLibManager::create($type, $sender->getPosition(), 'test');
		$inv->setListener(function(InvLibAction $action) use ($inv): void{
			$item = $action->getSourceItem();
			$player = $action->getPlayer();
			if($item->getId() === 1){
				$inv->close($player, function() use ($player) : void{
					$player->sendMessage('test');
				});
			}else{
				$action->setCancelled();
				$inv->close($player, function() use ($player) : void{
					$player->sendMessage(':(');
				});
			}
		});
		$inv->setCloseListener(function(Player $player) : void{
			$player->sendMessage('closed inv');
		});
		$inv->setItem(4, ItemFactory::getInstance()->get(5));
		$inv->send($sender, function() use ($inv) : void{
			$inv->addItem(ItemFactory::getInstance()->get(1));
		});
		return true;
	}
	
}