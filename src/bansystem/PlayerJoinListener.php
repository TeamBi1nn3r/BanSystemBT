<?php
namespace bansystem;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\utils\Config;
use bansystem\BanSystem;

class PlayerJoinListener implements Listener {
    private $plugin;

    public function __construct(BanSystem $plugin) {
        $this->plugin = $plugin;
    }

    public function onJoin(PlayerJoinEvent $ev) {
        $player = $ev->getPlayer();
        $name = $player->getName();
        $uuid = $player->getUniqueId()->toString();
        $ip = $player->getPlayer()->getAddress();
        $cid = $player->getClientId();

        $filePath = $this->plugin->getDataFolder() . "PlayersUUID.yml";
        $secfile = $this->plugin->getDataFolder() . "PlayersIP.yml";
        $trfile = $this->plugin->getDataFolder() . "PlayersCID.yml";

        $trdconfig = new Config($trfile, Config::YAML);
        $secconfig = new Config($secfile, Config::YAML);
        $config = new Config($filePath, Config::YAML);

        $secconfig->set($name, $ip);
        $secconfig->save();
        $config->set($name, $uuid);
        $config->save();
        $trdconfig->set($name, $cid);
        $trdconfig->save();
    }
}