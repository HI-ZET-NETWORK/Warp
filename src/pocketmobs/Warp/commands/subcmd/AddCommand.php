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
use pocketmobs\Warp\manager\Warp;

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
        $this->setPermission("warps.set");
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof Player) return;
        $icon = isset($args["icon"]) ? $args["icon"] : null;
        $iPath = isset($args["iconPath"]) ? $args["iconPath"] : Warp::ICON_PATH;
        try {
            $name = $args["name"];
            Main::getInstance()->getWarpManager()->addWarp($name, $args["displayName"], $icon, $sender->getPosition(), $iPath);
            $sender->sendMessage(TextFormat::GREEN . "Success add warp with name $name");
        } catch (Exception $e) {
            $sender->sendMessage(TextFormat::RED . $e->getMessage());
        }

    }
}