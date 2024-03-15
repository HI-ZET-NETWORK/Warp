<?php

namespace pocketmobs\Warp\commands\subcmd;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use Exception;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use pocketmobs\Warp\Main;

class DeleteCommand extends BaseSubCommand
{

    /**
     * @throws ArgumentOrderException
     */
    protected function prepare(): void
    {
        $this->registerArgument(0, new RawStringArgument("name"));
        $this->setPermission("warp.set");
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        try {
            $name = $args["name"];
            Main::getInstance()->getWarpManager()->removeWarp($name);
            $sender->sendMessage(TextFormat::GREEN . "Success delete warp with name $name");
        } catch (Exception $e) {
            $sender->sendMessage(TextFormat::RED . $e->getMessage());
        }
    }
}