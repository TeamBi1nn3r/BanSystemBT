<?php

namespace bansystem\command;

use bansystem\Manager;
use bansystem\translation\Translation;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class UnmuteCommand extends Command {
    
    public function __construct() {
        parent::__construct("unmute");
        $this->description = "§eDesmutea a un jugador";
        $this->usageMessage  = "§e/unmute <Jugador>";
        $this->setPermission("bansystem.command.unmute");
    }
    
    public function execute(CommandSender $sender, $commandLabel, array $args) {
        if ($this->testPermissionSilent($sender)) {
            if (count($args) <= 0) {
                $sender->sendMessage(Translation::translateParams("usage", array($this)));
                return false;
            }
            $muteList = Manager::getNameMutes();
            if (!$muteList->isBanned($args[0])) {
                $sender->sendMessage(Translation::translate("playerNotMuted"));
                return false;
            }
            $muteList->remove($args[0]);
            $sender->getServer()->broadcastMessage(TextFormat::AQUA . $args[0] . TextFormat::GREEN . " fué desmuteado. ");
        } else {
            $sender->sendMessage(Translation::translate("noPermission"));
        }
        return true;
    }
}