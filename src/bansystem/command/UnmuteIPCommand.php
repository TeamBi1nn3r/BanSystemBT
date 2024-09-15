<?php

namespace bansystem\command;

use bansystem\Manager;
use bansystem\translation\Translation;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class UnmuteIPCommand extends Command {
    
    public function __construct() {
        parent::__construct("unmute-ip");
        $this->description = "§eDesmutea una IP";
        $this->usageMessage = "§e/unmute-ip <IP>";
        $this->setPermission("bansystem.command.unmuteip");
    }
    
    public function execute(CommandSender $sender, $commandLabel, array $args) {
        if ($this->testPermission($sender)) {
            if (count($args) <= 0) {
                $sender->sendMessage(Translation::translateParams("usage", array($this)));
                return false;
            }
            $muteList = Manager::getIPMutes();
            if (!$muteList->isBanned($args[0])) {
                $sender->sendMessage(Translation::translate("ipNotMuted"));
                return false;
            }
            $muteList->remove($args[0]);
            $sender->getServer()->broadcastMessage(TextFormat::GREEN . "La IP " . TextFormat::AQUA . $args[0] . TextFormat::GREEN . " fue desmuteada");
        } else {
            $sender->sendMessage(Translation::translate("noPermission"));
        }
        return true;
    }
}