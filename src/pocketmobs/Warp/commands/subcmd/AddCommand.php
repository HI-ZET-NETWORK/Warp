<?php

namespace pocketmobs\Warp\commands\subcmd;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use Exception;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmobs\Warp\Main;

class AddCommand extends BaseSubCommand
{

    /**
     * @throws ArgumentOrderException
     */
    protected function prepare(): void
    {
        $this->registerArgument(0, new RawStringArgument("name"));
        $this->registerArgument(1, new RawStringArgument("displayName"));
        $this->registerArgument(2, new RawStringArgument("icon", true));
        $this->registerArgument(3, new RawStringArgument("iconPath", true));
        $this->setPermission("warp.set");
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof Player) return;
        try {
            $name = $args["name"];
            Main::getInstance()->getWarpManager()->addWarp($name, $args["displayName"], $args["icon"], $sender->getPosition(), $args["iconPath"]);
            $sender->sendMessage(TextFormat::GREEN . "Success add warp with nane $name");
        } catch (Exception $e) {
            $sender->sendMessage(TextFormat::RED . $e->getMessage());
        }

    }
}