<?php

namespace bansystem\command;

use bansystem\Manager;
use bansystem\translation\Translation;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class MuteIPCommand extends Command {
    
    public function __construct() {
        parent::__construct("mute-ip");
        $this->description = "§eMutea o silencia a un jugador mediante su IP";
        $this->usageMessage = "§e/mute-ip <jugador | IP> [motivo]";
        $this->setPermission("bansystem.command.muteip");
    }
    
    public function execute(CommandSender $sender, $commandLabel, array $args) {
        if ($this->testPermission($sender)) {
            if (count($args) <= 0) {
                $sender->sendMessage(Translation::translateParams("usage", array($this)));
                return false;
            }
            $ip = filter_var($args[0], FILTER_VALIDATE_IP);
            $player = $sender->getServer()->getPlayer($args[0]);
            $muteList = Manager::getIPMutes();
            if ($muteList->isBanned($args[0])) {
                $sender->sendMessage(Translation::translate("ipAlreadyMuted"));
                return false;
            }
            if (count($args) == 1) {
                if ($ip != null) {
                    $muteList->addBan($ip, null, null, $sender->getName());
                    foreach ($sender->getServer()->getOnlinePlayers() as $players) {
                        if ($player->getAddress() == $ip) {
                            $players->sendMessage(TextFormat::RED . "Fuiste silenciado mediante IP");
                        }
                    }
                    $sender->getServer()->broadcastMessage(TextFormat::RED . "La ip " . TextFormat::YELLOW . $ip . TextFormat::RED . " fué silenciada por §c{$sender->getName()}");
                } else {
                    if ($player != null) {
                        $muteList->addBan($player->getAddress(), null, null, $sender->getName());
                        $player->sendMessage(TextFormat::RED . "Fuiste silenciado mediante IP");
                        $sender->getServer()->broadcastMessage(TextFormat::RED . $player->getName() . TextFormat::RED . " fué silenciada por §c{$sender->getName()}");
                    } else {
                        $sender->sendMessage(Translation::translate("playerNotFound"));
                    }
                }
            } else if (count($args) >= 2) {
                $reason = "";
                for ($i = 1; $i < count($args); $i++) {
                    $reason .= $args[$i];
                    $reason .= " ";
                }
                $reason = substr($reason, 0, strlen($reason) - 1);
                if ($ip != null) {
                    $muteList->addBan($ip, $reason, null, $sender->getName());
                    foreach ($sender->getServer()->getOnlinePlayers() as $players) {
                        if ($players->getAddress() == $ip) {
                            $players->sendMessage(TextFormat::RED . "Fuiste silenciado por " . TextFormat::YELLOW . $reason . TextFormat::RED . ".");
                        }
                    }
                    $sender->getServer()->broadcastMessage(TextFormat::RED . "La IP " . TextFormat::YELLOW . $ip . TextFormat::RED . " fué silenciada por " . TextFormat::AQUA . $reason . TextFormat::RED . ".");
                } else {
                    if ($player != null) {
                        $muteList->addBan($player->getAddress(), $reason, null, $sender->getName());
                        $player->sendMessage(TextFormat::RED . "Fuiste silenciado mediante ip por " . TextFormat::AQUA . $reason . TextFormat::RED . ".");
                        $sender->getServer()->broadcastMessage(TextFormat::AQUA . $player->getName() . TextFormat::RED . " fué silenciado por " . TextFormat::AQUA . $reason . TextFormat::RED . ".");
                    } else {
                        $sender->sendMessage(Translation::translate("playerNotFound"));
                    }
                }
            }
        } else {
            $sender->sendMessage(Translation::translate("noPermission"));
        }
        return true;
    }
}