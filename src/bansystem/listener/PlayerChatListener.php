<?php

namespace bansystem\listener;

use bansystem\Manager;
use bansystem\util\date\Countdown;
use DateTime;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\utils\TextFormat;

class PlayerChatListener implements Listener {
    
    public function onPlayerChat(PlayerChatEvent $event) {
        $player = $event->getPlayer();
        $muteList = Manager::getNameMutes();
        if ($muteList->isBanned($player->getName())) {
            $entries = $muteList->getEntries();
            $entry = $entries[strtolower($player->getName())];
            $muteMessage = "";
            if ($entry->getExpires() == null) {
                $reason = $entry->getReason();
                if ($reason != null || $reason != "") {
                    $muteMessage = TextFormat::RED . "Te encuentras silenciado por " . TextFormat::AQUA . $reason . TextFormat::RED . ".";
                } else {
                    $muteMessage = TextFormat::RED . "Estás silenciado.";
                }
            } else {
                $expiry = Countdown::expirationTimerToString($entry->getExpires(), new DateTime());
                if ($entry->hasExpired()) {
                    $muteList->remove($entry->getName());
                    return;
                }
                $muteReason = $entry->getReason();
                if ($muteReason != null || $muteReason != "") {
                    $muteMessage = TextFormat::RED . "Te encuentras silenciado por " . TextFormat::AQUA . $muteReason . TextFormat::RED . " Durante " . TextFormat::AQUA . $expiry . TextFormat::RED . ".";
                } else {
                    $muteMessage = TextFormat::RED . "Te encuentras silenciado durante " . TextFormat::AQUA . $expiry . TextFormat::RED . ".";
                }
            }
            $event->setCancelled(true);
            $player->sendMessage($muteMessage);
        }
    }
    
    public function onPlayerChat2(PlayerChatEvent $event) {
        $player = $event->getPlayer();
        $muteList = Manager::getIPMutes();
        if ($muteList->isBanned($player->getAddress())) {
            $entries = $muteList->getEntries();
            $entry = $entries[strtolower($player->getAddress())];
            $muteMessage = "";
            if ($entry->getExpires() == null) {
                $reason = $entry->getReason();
                if ($reason != null || $reason != "") {
                    $muteMessage = TextFormat::RED . "Tu IP se encuentra silenciada por " . TextFormat::AQUA . $reason . TextFormat::RED . ".";
                } else {
                    $muteMessage = TextFormat::RED . "Tu IP está silenciada";
                }
            } else {
                $expiry = Countdown::expirationTimerToString($entry->getExpires(), new DateTime());
                if ($entry->hasExpired()) {
                    $muteList->remove($entry->getName());
                    return;
                }
                $muteReason = $entry->getReason();
                if ($muteReason != null || $muteReason != "") {
                    $muteMessage = TextFormat::RED . "Tu IP se encuentra silenciada por " . TextFormat::AQUA . $muteReason . TextFormat::RED . " durante " . TextFormat::AQUA . $expiry . TextFormat::RED . ".";
                } else {
                    $muteMessage = TextFormat::RED . "Tu IP se encuentra silenciada durante " . TextFormat::AQUA . $expiry . TextFormat::RED . ".";
                }
            }
            $event->setCancelled(true);
            $player->sendMessage($muteMessage);
        }
    }
}