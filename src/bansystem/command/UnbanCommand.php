<?php

namespace bansystem\command;

use bansystem\translation\Translation;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class UnbanCommand extends Command {
    
    public function __construct() {
        parent::__construct("unban");
        $this->description = "§eDesbanea a un jugador";
        $this->usageMessage = "§e/unban <Jugador>";
        $this->setPermission("bansystem.command.unban");
    }
    
    public function execute(CommandSender $sender, $commandLabel, array $args) {
        if ($this->testPermissionSilent($sender)) {
            if (count($args) <= 0) {
                $sender->sendMessage(Translation::translateParams("usage", array($this)));
                return false;
            }
            $banList = $sender->getServer()->getNameBans();
            if (!$banList->isBanned($args[0])) {
                $sender->sendMessage(Translation::translate("playerNotBanned"));
                return false;
            }
            $banList->remove($args[0]);
            $sender->getServer()->broadcastMessage(TextFormat::AQUA . $args[0] . TextFormat::GREEN . " fué desbaneado");
        } else {
            $sender->sendMessage(Translation::translate("noPermission"));
        }
        return true;
    }
}