<?php

namespace pocketmobs\Warp\event;

use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\event\player\PlayerEvent;
use pocketmine\player\Player;
use pocketmobs\Warp\manager\Warp;

class WarpEvent extends PlayerEvent implements Cancellable
{

    use CancellableTrait;

    public function __construct(Player $player, private readonly Warp $warp)
    {
        $this->player = $player;
    }

    public function getWarp(): Warp
    {
        return $this->warp;
    }

}