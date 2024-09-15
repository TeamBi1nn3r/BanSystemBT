<?php

namespace bansystem\listener;

use bansystem\util\date\Countdown;
use DateTime;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\utils\TextFormat;
use bansystem\Manager;

class PlayerPreLoginListener implements Listener {
    
    public function onPlayerPreLogin(PlayerPreLoginEvent $event) {
        $player = $event->getPlayer();
        $banList = $player->getServer()->getNameBans();
        if ($banList->isBanned(strtolower($player->getName()))) {
            $kickMessage = "";
            $banEntry = $banList->getEntries();
            $entry = $banEntry[strtolower($player->getName())];
            if ($entry->getExpires() == null) {
                $bannedBy = $entry->getSource();
                $reason = $entry->getReason();
                if ($reason != null || $reason != "") {
                    $kickMessage = TextFormat::RED . "¡Has sido baneado del servidor! \n §ePor: " . TextFormat::RED . $bannedBy . TextFormat::RED . "\n§eMotivo: §c{$reason}";
                } else {
                    $kickMessage = TextFormat::RED . "¡Estás baneado del servidor! \n §ePor: §c{$bannedBy}";
                }
            } else {
                $expiry = Countdown::expirationTimerToString($entry->getExpires(), new DateTime());
                if ($entry->hasExpired()) {
                    $banList->remove($entry->getName());
                    return;
                }
                $banReason = $entry->getReason();
                if ($banReason != null || $banReason != "") {
                    $bannedBy = $entry->getSource();
                    $kickMessage = TextFormat::RED . "¡Has sido baneado del servidor! \n §ePor: " . TextFormat::RED . $bannedBy . TextFormat::RED . "\n §eMotivo: " . $banReason . "\n §eDurante: " . TextFormat::RED . $expiry . TextFormat::RED . ".";
                } else {
                    $bannedBy = $entry->getSource();
                    $kickMessage = TextFormat::RED . "¡Has sido baneado del servidor! \n §ePor: §c{$bannedBy} \n §eDurante: §c" . TextFormat::RED . $expiry . TextFormat::RED . ".";
                }
            }
            $player->close("", $kickMessage);
        }
    }
    
    public function onPlayerPreLogin2(PlayerPreLoginEvent $event) {
        $player = $event->getPlayer();
        $banList = $player->getServer()->getIPBans();
        if ($banList->isBanned(strtolower($player->getAddress()))) {
            $kickMessage = "";
            $banEntry = $banList->getEntries();
            $entry = $banEntry[strtolower($player->getAddress())];
            $bannedBy = $entry->getSource();
            if ($entry->getExpires() == null) {
                $reason = $entry->getReason();
                if ($reason != null || $reason != "") {
                    $kickMessage = TextFormat::RED . "¡Has sido baneado mediante tu IP del servidor! \n §ePor: §c{$bannedBy} \n §eMotivo: " . TextFormat::RED . $reason . TextFormat::RED . ".";
                } else {
                    $kickMessage = TextFormat::RED . "¡Has sido baneado mediante tu IP del servidor! \n §ePor: §c{$bannedBy}";
                }
            } else {
                $expiry = Countdown::expirationTimerToString($entry->getExpires(), new DateTime());
                if ($entry->hasExpired()) {
                    $banList->remove($entry->getName());
                    return;
                }
                $banReason = $entry->getReason();
                if ($banReason != null || $banReason != "") {
                    $kickMessage = TextFormat::RED . "¡Has sido baneado mediante tu IP del servidor! \n §ePor: " . TextFormat::RED . $bannedBy . TextFormat::RED . "\n §eMotivo: §c" . $banReason . "\n §eDurante: " . TextFormat::RED . $expiry . TextFormat::RED . ".";
                } else {
                    $kickMessage = TextFormat::RED . "¡Has sido baneado mediante tu IP del servidor! \n §ePor: §c{$bannedBy} \n §eDurante: " . TextFormat::RED . $expiry . TextFormat::RED . ".";
                }
            }
            $player->close("", $kickMessage);
        }
    }
    public function onPlayerPreLogin3(PlayerPreLoginEvent $event) {
        $player = $event->getPlayer();
        $uuid = $player->getUniqueId()->toString(); 
        $blockList = Manager::getNameBlocks();
        $cid = $player->getClientId();

        $banList = $player->getServer()->getNameBans();
            $banListIP = $player->getServer()->getIPBans();
            $cidBans = $player->getServer()->getCIDBans();
            



        if ($blockList->isBanned($uuid) && $banList->isBanned(strtolower($player->getName())) && $banListIP->isBanned(strtolower($player->getAddress())) && $cidBans->isBanned($cid)) { 
            $kickMessage = "";
            $banEntry = $blockList->getEntries();
            $banEntrya = $banList->getEntries();
            $banIPEntry = $banListIP->getEntries();
            $banCIDEntry = $cidBans->getEntries();


            $entrya = $banEntrya[strtolower($player->getName())];
            $entryIP = $banIPEntry[strtolower($player->getAddress())];
            $entryCID = $banCIDEntry[strtolower($player->getClientId())];
            
            $entry = $banEntry[$uuid]; 
            if ($entry->getExpires() == null) {
                $bannedBy = $entry->getSource();
                $reason = $entry->getReason();
                if ($reason != null || $reason != "") {
                    $kickMessage = TextFormat::RED . "¡Has sido bloqueado del servidor! \n §ePor: §c{$bannedBy} \n §eMotivo: " . TextFormat::RED . $reason;
                } else {
                    $kickMessage = TextFormat::RED . "¡Has sido bloqueado del servidor! \n §ePor: §c{$bannedBy}";
                }
            } else {
                $expiry = Countdown::expirationTimerToString($entry->getExpires(), new DateTime());
                if ($entry->hasExpired()) {
                    $blockList->remove($uuid);
                    $banList->remove($entrya->getName());
                    $banListIP->remove($entryIP->getName());
                    $cidBans->remove($entryCID->getName());
                    return;
                }
                $banReason = $entry->getReason();
                if ($banReason != null || $banReason != "") {
                    $bannedBy = $entry->getSource();
                    $kickMessage = TextFormat::RED . "¡Has sido bloqueado del servidor! \n §ePor: §c{$bannedBy} \n §eMotivo:" . TextFormat::AQUA . $banReason . TextFormat::RED . "\n §eDurante: " . TextFormat::RED . $expiry . TextFormat::RED . ".";
                } else {
                    $bannedBy = $entry->getSource();
                    $kickMessage = TextFormat::RED . "¡Has sido bloqueado del servidor! \n §ePor: §c{$bannedBy} \n §eDurante: " . TextFormat::RED . $expiry . TextFormat::RED . ".";
                }
            }
            $player->close("", $kickMessage);
        }
    }

}