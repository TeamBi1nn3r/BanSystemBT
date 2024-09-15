<?php

namespace bansystem\command;

use bansystem\translation\Translation;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class BanIPCommand extends Command {
    
    public function __construct() {
        parent::__construct("ban-ip");
        $this->description = "§eBanea a un jugador por IP";
        $this->usageMessage  = "§a/ban-ip§c <jugador | IP> [motivo]";
        $this->setPermission("bansystem.command.banip");
    }
    
    public function execute(CommandSender $sender, $commandLabel, array $args) {
        if ($this->testPermissionSilent($sender)) {
            if (count($args) <= 0) {
                $sender->sendMessage(Translation::translateParams("usage", array($this)));
                return false;
            }
            
            $banList = $sender->getServer()->getIPBans();
            if ($banList->isBanned($args[0])) {
                $sender->sendMessage(Translation::translate("ipAlreadyBanned"));
                return false;
            }
            $ip = filter_var($args[0], FILTER_VALIDATE_IP);
            $player = $sender->getServer()->getPlayer($args[0]);
            if (count($args) == 1) {
                if ($ip != null) {
                    $banList->addBan($ip, null, null, $sender->getName());
                    foreach ($sender->getServer()->getOnlinePlayers() as $onlinePlayers) {
                        if ($onlinePlayers->getAddress() == $ip) {
                            $onlinePlayers->kick(TextFormat::RED . "Fuiste baneado por IP", false);
                        }
                    }
                    $sender->getServer()->broadcastMessage(TextFormat::RED . "La direccion " . TextFormat::YELLOW . $ip . TextFormat::RED . " ya fue baneada.");
                } else {
                    if ($player != null) {
                        $banList->addBan($player->getAddress(), null, null, $sender->getName());
                        $player->kick(TextFormat::RED . "Fuiste baneado por IP.", false);
                        $sender->getServer()->broadcastMessage(TextFormat::YELLOW . $player->getName() . TextFormat::RED . " Ya fue baneado.");
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
                    foreach ($sender->getServer()->getOnlinePlayers() as $players) {
                        $banList->addBan($ip, $reason, null, $sender->getName());   
                        if ($players->getAddress() == $ip) {
                            $players->kick(TextFormat::YELLOW . "Tu IP fue baneada por: " . TextFormat::RED . $reason . TextFormat::RED . ".", false);
                        }
                    }
                    $sender->getServer()->broadcastMessage(TextFormat::RED . "La direccion " . TextFormat::YELLOW . $ip . TextFormat::RED . " Fue baneada por: " . TextFormat::RED . $reason . TextFormat::RED . ".");
                } else {
                    if ($player != null) {
                        $banList->addBan($player->getAddress(), $reason, null, $sender->getName());
                        $player->kick(TextFormat::YELLOW . "Tu IP fue baneada por: " . TextFormat::RED . $reason . TextFormat::RED . ".", false);
                        $sender->getServer()->broadcastMessage(TextFormat::YELLOW . $player->getName() . TextFormat::RED . " fue baneada por: " . TextFormat::RED . $reason . TextFormat::RED . ".");  
                    } else {
                        $sender->sendMessage(Translation::translate("playerNotFound"));
                    }
                }
            } else {
                $sender->sendMessage(Translation::translate("noPermission"));
            }
        }
        return true;
    }
}