<?php

namespace pocketmobs\Warp\manager;

use ArrayIterator;
use Exception;
use Illuminate\Support\Collection;
use IteratorAggregate;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use pocketmine\world\Position;
use pocketmobs\Warp\event\WarpEvent;
use pocketmobs\Warp\Main;
use Traversable;

class Warp implements IteratorAggregate
{

    const ICON_PATH = "path";
    const ICON_URL = "url";

    public function __construct(private readonly string $name, private readonly string $displayName, private readonly ?string $icon, private readonly Position $position, private readonly string $iconPath = self::ICON_PATH)
    {
    }

    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function getIconPath(): string
    {
        return $this->iconPath;
    }

    public function getPosition(): Position
    {
        return $this->position;
    }

    public function teleport(Player $player): void
    {
        $ev = new WarpEvent($player, $this);
        $ev->call();
        if (!$ev->isCancelled()) {
            $player->teleport($this->getPosition());
        }
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator([
            "name" => $this->getName(),
            "displayName" => $this->getDisplayName(),
            "icon" => $this->getIcon(),
            "iconPath" => $this->getIconPath(),
            "position" => [
                "x" => $this->getPosition()->getX(),
                "y" => $this->getPosition()->getY(),
                "z" => $this->getPosition()->getZ(),
                "world" => $this->getPosition()->getWorld()->getFolderName(),
            ]
        ]);
    }
}

class WarpManager
{

    /** @var Collection<Warp> $warps */
    private Collection $warps;

    public function __construct(private readonly Main $plugin)
    {
        $this->warps = new Collection();
    }

    public function getAll(): Collection
    {
        return $this->warps;
    }

    public function getWarpByName(string $name): ?Warp
    {
        return $this->warps->first(function (Warp $warp) use ($name) {
            return $warp->getName() == $name;
        });
    }

    public function loadWarps(): void
    {
        try {
            $config = new Config($this->plugin->getDataFolder() . "warps.yml", Config::YAML);
            foreach ($config->getAll() as $warp) {
                $folderName = $warp["position"]["world"];
                $world = Server::getInstance()->getWorldManager()->getWorldByName($folderName);
                if ($world == null) {
                    throw new Exception("World with folder name $folderName");
                }

                $position = new Position($warp["position"]["x"], $warp["position"]["y"], $warp["position"]["z"], $world);
                $this->addWarp($warp["name"], $warp["displayName"], $warp["icon"], $position, $warp["world"]);
            }
        } catch (Exception $e) {
            $this->plugin->getLogger()->error(TextFormat::RED . $e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public function addWarp(string $name, string $displayName, ?string $icon, Position $position, string $iconPath = Warp::ICON_PATH): void
    {
        $warp = $this->getWarpByName($name);
        if ($warp != null) {
            throw new Exception("Warp with name $name already exists!");
        }

        if (!in_array($iconPath, [Warp::ICON_URL, Warp::ICON_PATH])) {
            throw new Exception("Warp icon path must be " . Warp::ICON_URL . "/" . Warp::ICON_PATH);
        }

        $warp = new Warp($name, $displayName, $icon, $position, $iconPath);
        $this->warps->add($warp);
        $config = new Config($this->plugin->getDataFolder() . "warps.yml", Config::YAML);
        $config->set($name, (array) $warp->getIterator());
    }

    /**
     * @throws Exception
     */
    public function removeWarp(string $name): void
    {
        $warp = $this->getWarpByName($name);
        if ($warp == null) {
            throw new Exception("Warp with name $name not exists!");
        }

        foreach ($this->warps as $key => $warp) {
            if ($warp->getName() == $name) {
                $this->warps->forget($key);
                break;
            }
        }

        $config = new Config($this->plugin->getDataFolder() . "warps.yml", Config::YAML);
        $config->remove($name);

    }

}