<?php

declare(strict_types=1);

namespace skymin\InvLibTest\block;

use pocketmine\block\{
	Block,
	BlockTypeIds,
	BlockBreakInfo as BreakInfo,
	BlockIdentifier as BID,
	BlockTypeInfo as Info
};
use pocketmine\utils\CloningRegistryTrait;
use function mb_strtolower;

/**
 * @method static Dropper DROPPER()
 */
final class VanillaBlocks{
	use CloningRegistryTrait;

	private function __construct(){
		//NOOP
	}

	public static function register(string $name, Block $item) : void{
		self::_registryRegister($name, $item);
	}

	protected static function setup() : void{
		self::register('dropper', new Dropper(
			new BID(BlockTypeIds::newId()),
			'Dropper',
			new Info(BreakInfo::pickaxe(3.5))
		));
	}

	/**
	 * @return Block[]
	 */
	public static function getAll() : array{
		return self::_registryGetAll();
	}

}
