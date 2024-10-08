<?php

namespace bansystem\command;

use bansystem\translation\Translation;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class UnbanIPCommand extends Command {
    
    public function __construct() {
        parent::__construct("unban-ip");
        $this->description = "§eDesbanea la IP un jugador";
        $this->usageMessage = "§e/unban-ip <IP>";
        $this->setPermission("bansystem.command.pardonip");
    }
    
    public function execute(CommandSender $sender, $commandLabel, array $args) {
        if ($this->testPermissionSilent($sender)) {
            if (count($args) <= 0) {
                $sender->sendMessage(Translation::translateParams("usage", array($this)));
                return false;
            }
            $banList = $sender->getServer()->getIPBans();
            if (!$banList->isBanned($args[0])) {
                $sender->sendMessage(Translation::translate("ipNotBanned"));
                return false;
            }
            $banList->remove($args[0]);
            $sender->getServer()->broadcastMessage(TextFormat::GREEN . "La IP " . TextFormat::AQUA . $args[0] . TextFormat::GREEN . " fué desbaneada");
        } else {
            $sender->sendMessage(Translation::translate("noPermission"));
        }
        return true;
    }
}