<?php

namespace bansystem\command;

use bansystem\translation\Translation;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class KickCommand extends Command {
    
    public function __construct() {
        parent::__construct("kick");
        $this->description = "§eExpulsa a un jugador";
        $this->usageMessage = "§e/kick <jugador> <motivo>";
        $this->setPermission("bansystem.command.kick");
    }
    
    public function execute(CommandSender $sender, $commandLabel, array $args) {
        if ($this->testPermissionSilent($sender)) {
            if (count($args) <= 0) {
                $sender->sendMessage(Translation::translateParams("usage", array($this)));
                return false;
            }
            $player = $sender->getServer()->getPlayer($args[0]);
            if (count($args) == 1) {
                if ($player != null) {
                    $player->kick(TextFormat::RED . "Has sido echado/a.", false);
                    $sender->getServer()->broadcastMessage(TextFormat::YELLOW . $player->getName() . TextFormat::RED . " ha sido echado/a.");
                } else {
                    $sender->sendMessage(Translation::translate("playerNotFound"));
                }
            } else if (count($args) >= 2) {
                if ($player != null) {
                    $reason = "";
                    for ($i = 1; $i < count($args); $i++) {
                        $reason .= $reason[$i];
                        $reason .= " ";
                    }
                    $reason = substr($reason, 0, strlen($reason) - 1);
                    $player->kick(TextFormat::YELLOW . "Has sido echado/a por: " . TextFormat::RED . $reason . TextFormat::RED . ".", false);
                    $sender->getServer()->broadcastMessage(TextFormat::AQUA . $player->getName() . TextFormat::YELLOW . " ha sido echado/a por: " . TextFormat::RED . $reason . TextFormat::RED . ".");
                } else {
                    $sender->sendMessage(Translation::translate("playerNotFound"));
                }
            }
        } else {
            $sender->sendMessage(Translation::translate("noPermission"));
        }
        return true;
    }
}