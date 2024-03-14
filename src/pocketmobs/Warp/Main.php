<?php

namespace pocketmobs\Warp;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use pocketmobs\Warp\commands\CommandExecutor;
use pocketmobs\Warp\manager\WarpManager;

class Main extends PluginBase
{

    use SingletonTrait;

    private WarpManager $warpManager;

    protected function onEnable(): void
    {
        $this->warpManager = new WarpManager($this);
        $this->warpManager->loadWarps();
        $this->getServer()->getCommandMap()->register("Warp", new CommandExecutor($this, "warp"));
    }

    public function getWarpManager(): WarpManager
    {
        return $this->warpManager;
    }

}