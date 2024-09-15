<?php
namespace bansystem;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\utils\Config;
use bansystem\BanSystem;
use pocketmine\Server;
use pocketmine\Player;

class GetApiCountry implements Listener{


    public function getCountryByIP(string $ip){
        $json = file_get_contents("http://ip-api.com/json/" . $ip);
        $data = json_decode($json, true);

        if ($data["status"] === "success") {
            return $data["country"];
        }

        return "§cNone";
    }
}