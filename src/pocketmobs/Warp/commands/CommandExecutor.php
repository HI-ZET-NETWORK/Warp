<?php

namespace pocketmobs\Warp\commands;

use CortexPE\Commando\BaseCommand;
use dktapps\pmforms\FormIcon;
use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmobs\Warp\commands\subcmd\AddCommand;
use pocketmobs\Warp\commands\subcmd\DeleteCommand;
use pocketmobs\Warp\Main;
use pocketmobs\Warp\manager\Warp;

class CommandExecutor extends BaseCommand
{

    protected function prepare(): void
    {
        $this->registerSubCommand(new AddCommand("add"));
        $this->registerSubCommand(new DeleteCommand("delete"));
        $this->setPermission("warps");
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if ($sender instanceof Player) {
            $warps = Main::getInstance()->getWarpManager()->getAll()->map(function (Warp $warp) : MenuOption{
                return new MenuOption($warp->getDisplayName(), $warp->getIcon() !== null ? new FormIcon($warp->getIcon(), $warp->getIconPath()) : null);
            });
            $menuForm = new MenuForm("Warps", "", $warps->toArray(), function(Player $player, int $selectedOption) : void{
                $warp = Main::getInstance()->getWarpManager()->getAll()->get($selectedOption);
                if ($warp instanceof Warp) {
                    $warp->teleport($player);
                }
            });
            $sender->sendForm($menuForm);
        }
    }

    public function getPermission()
    {
        return "warps";
    }
}