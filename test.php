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

use skymin\InventoryLib\InvLibHandler;
use skymin\InventoryLib\action\InventoryAction;
use skymin\InventoryLib\inventory\{InvType, BaseInventory, SimpleInv};

class InvTest extends PluginBase{
	
	public function onEnable() :void{
		InvLibHandler::register($this);
		$cmd = new PluginCommand('inv', $this, $this);
		$cmd->setDescription('InventoryLib test plugin');
		$this->getServer()->getCommandMap()->register('inv', $cmd);
	}
	
	public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
		if(!isset($args[0])) return false;
		$type = match($args[0]){
			'doublechest','dc' => InvType::DOUBLE_CHEST(),
			'chest', 'c' => InvType::CHEST(),
			'hopper', 'h' => InvType::HOPPER(),
			'dropper', 'd' => InvType::DROPPER(),
			default => InvType::DOUBLE_CHEST()
		};
		$inv = SimpleInv::create($type, 'test');
		$inv->setActionHandler(function(SimpleInv $inv, InventoryAction $action) : bool{
			$item = $action->getSourceItem();
			$player = $action->getPlayer();
			if($item->getId() === 1){
				$inv->close($player);
				$player->sendMessage('test');
				return true;
			}
			$inv->close($player);
			$player->sendMessage(':(');
			return false;
		});
		$inv->setCloseHandler(function(SimpleInv $inv, Player $player) : void{
			$player->sendMessage('closed inv');
		});
		$inv->setItem(4, ItemFactory::getInstance()->get(5));
		$inv->addItem(ItemFactory::getInstance()->get(1));
		$inv->send($sender);
		return true;
	}
	
}
