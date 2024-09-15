<?php
namespace bansystem;

use pocketmine\event\Listener;
use DateTime;
use InvalidArgumentException;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\utils\Config;
use bansystem\Manager;
use bansystem\GetApiCountry;
use bansystem\util\date\Countdown;
use pocketmine\utils\TextFormat;

class managementBansC implements Listener {
    private $plugin;
    public $apiCountry;

    public function __construct(GetApiCountry $apiCountry) {
        $this->apiCountry = $apiCountry;
    }

    public function onPlayerPreLogin(PlayerPreLoginEvent $event) {
        $player = $event->getPlayer();
        $ip = $player->getAddress();
        $country = $this->apiCountry->getCountryByIP($ip);

        $blockList = Manager::getIPBlocks();

      
        if ($blockList->isBanned(strtolower($country))) {
            $banEntry = $blockList->getEntries()[strtolower($country)];
            $kickMessage = "";

            if ($banEntry->getExpires() == null) {
                $bannedBy = $banEntry->getSource();
                $reason = $banEntry->getReason();
                $kickMessage = $reason !== null ? 
                    TextFormat::RED . "§cTu pais fué baneado del servidor! \n §eMotivo: " . TextFormat::RED . $reason . TextFormat::YELLOW . "\n §ePor:§c {$bannedBy}" :
                    TextFormat::RED . "§cTu pais fué baneado del servidor! \n §ePor:§c {$bannedBy}";
            } else {
                $bannedBy = $banEntry->getSource();
                $expiry = Countdown::expirationTimerToString($banEntry->getExpires(), new DateTime());
                if ($banEntry->hasExpired()) {
                    $blockList->remove($banEntry->getName());
                    return;
                }
                $banReason = $banEntry->getReason();
                $kickMessage = $banReason !== null ? 
                    TextFormat::RED . "§cTu pais fué baneado del servidor! \n §eMotivo: " . TextFormat::RED . $banReason . TextFormat::RED . " \n§ePor: " . TextFormat::RED . $bannedBy . TextFormat::YELLOW . "\nDurante: " . TextFormat::RED . $expiry:
                    TextFormat::RED . "§cTu pais fué baneado del servidor! \n §ePor: " . TextFormat::RED . $bannedBy . "\n§eDurante: " .TextFormat::RED . $expiry . ".";
            }

            $player->close("", $kickMessage);
        }
    }
}
