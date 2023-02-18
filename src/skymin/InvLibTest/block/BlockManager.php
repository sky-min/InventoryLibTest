<?php

namespace skymin\InvLibTest\block;

use pocketmine\block\{
	Block,
	RuntimeBlockStateRegistry,
	VanillaBlocks as Blocks,
	BlockTypeInfo as Info, 
	BlockBreakInfo as BreakInfo,
	BlockIdentifier as BID, 
	BlockToolType as ToolType
};
use pocketmine\data\bedrock\block\{
	BlockStateNames as StateNames,
	BlockTypeNames as TypeNames,
	BlockStateSerializeException
};
use pocketmine\data\bedrock\block\convert\{
	BlockStateReader as Reader,
	BlockStateWriter as Writer
};
use pocketmine\utils\SingletonTrait;
use pocketmine\world\format\io\GlobalBlockStateHandlers;
use pocketmine\item\StringToItemParser;

final class BlockManager{
	
	public function __construct(){
		//NONE
	}
	
	private static function register(Block $block, string $namespace = "", ?array $stringToItemParserNames = null, bool $force = true, ?\Closure $serializeCallback = null, ?\Closure $deserializeCallback = null){
		$namespace = $namespace === "" ? "minecraft:" . strtolower(str_replace(" ", "_", $block->getName())) : $namespace;

		RuntimeBlockStateRegistry::getInstance()->register($block, $force);
		
		GlobalBlockStateHandlers::getDeserializer()->map($namespace, $deserializeCallback !== null ? $deserializeCallback : fn() => clone $block);
		GlobalBlockStateHandlers::getSerializer()->map($block, $serializeCallback !== null ? $serializeCallback : fn() => Writer::create($namespace));

		if($stringToItemParserNames !== null){
			foreach($stringToItemParserNames as $stringName){
				StringToItemParser::getInstance()->registerBlock(strtolower($stringName), fn() => clone $block);
			}
		}
	}
	
	
	public static function init() : void{
		self::register(VanillaBlocks::DROPPER(), "minecraft:dropper", ["dropper"], true, 
			fn(Dropper $block) => Writer::create(TypeNames::DROPPER)
				->writeFacingDirection($block->getFacing())
				->writeBool(StateNames::TRIGGERED_BIT, $block->isTriggeredBit()),
			function(Reader $in) : Block{
				return VanillaBlocks::DROPPER()
					->setFacing($in->readFacingDirection())
					->setTriggeredBit($in->readBool(StateNames::TRIGGERED_BIT));
			}
		);
	}
}
