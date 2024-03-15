<?php

namespace pocketmobs\Warp;

use pocketmine\plugin\PluginBase;
use pocketmobs\Warp\commands\CommandExecutor;
use pocketmobs\Warp\manager\WarpManager;

class Main extends PluginBase
{

    private WarpManager $warpManager;
    public static $instance;

    protected function onEnable(): void
    {
        self::$instance = $this;
        $this->warpManager = new WarpManager($this);
        $this->warpManager->loadWarps();
        $this->getServer()->getCommandMap()->register("Warp", new CommandExecutor($this, "warp"));
    }
    
    public static function getInstance() : Main {
        return self::$instance;
    }

    public function getWarpManager(): WarpManager
    {
        return $this->warpManager;
    }

}