<?php

declare(strict_types = 1);

namespace skymin\InvLibTest;

use skymin\InvLibTest\block\{
	BlockManager,
	VanillaBlocks
};

use pocketmine\command\{
	Command,
	CommandSender,
	PluginCommand
};
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\AsyncTask;

use skymin\InventoryLib\InvLibHandler;
use skymin\InventoryLib\action\InventoryAction;
use skymin\InventoryLib\inventory\SimpleInv;
use skymin\InventoryLib\type\{InvType, InvTypeIds};
use pocketmine\network\mcpe\protocol\types\inventory\WindowTypes;

final class Main extends PluginBase{

	public const DROPPER = 'InvLibTest:dropper';

	protected function onEnable() : void{
		BlockManager::init();
		$this->getServer()->getAsyncPool()->addWorkerStartHook(function(int $worker) : void{
			$this->getServer()->getAsyncPool()->submitTaskToWorker(new class extends AsyncTask{
				public function onRun() : void{
					BlockManager::init();
				}
			}, $worker);
		});
		InvLibHandler::register($this);
		InvLibHandler::getRegistry()->register(
			self::DROPPER,
			new InvType(
				9,
				WindowTypes::DROPPER,
				VanillaBlocks::DROPPER()
			)
		);
		$cmd = new PluginCommand('inv', $this, $this);
		$cmd->setDescription('InventoryLib test plugin');
		$this->getServer()->getCommandMap()->register('inv', $cmd);
	}

	public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
		$type = match($args[0] ?? null){
			'chest', 'c' => InvTypeIds::CHEST,
			'hopper', 'h' => InvTypeIds::HOPPER,
			'dropper' ,'d' => self::DROPPER,
			default => InvTypeIds::DOUBLE_CHEST
		};
		$inv = SimpleInv::create($type, 'test');
		$inv->setActionHandler(function(SimpleInv $inv, InventoryAction $action) : bool{
			$player = $action->getPlayer();
			$inv->close($player);
			$player->sendMessage(':(');
			return false;
		});
		$inv->setCloseHandler(function(SimpleInv $inv, Player $player) : void{
			$player->sendMessage('closed inv');
		});
		$inv->send($sender);
		return true;
	}

}