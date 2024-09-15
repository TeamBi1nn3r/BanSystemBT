<?php

namespace bansystem\command;

use bansystem\Manager;
use bansystem\translation\Translation;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class UnblockIPCommand extends Command {
    
    public function __construct() {
        parent::__construct("unblock-c");
        $this->description = "Allows the given IP address from running server command.";
        $this->usageMessage = "/unblock-ip <address>";
        $this->setPermission("bansystem.command.unblockc");
    }
    
    public function execute(CommandSender $sender, $commandLabel, array $args) {
        if ($this->testPermissionSilent($sender)) {
            if (count($args) <= 0) {
                $sender->sendMessage(Translation::translateParams("usage", array($this)));
                return false;
            }
            $blockList = Manager::getIPBlocks();
            if (!$blockList->isBanned($args[0])) {
                $sender->sendMessage("§cEste pais no fué baneado");
                return false;
            }
            $blockList->remove($args[0]);
            $sender->getServer()->broadcastMessage(TextFormat::GREEN . "El pais " . TextFormat::AQUA . $args[0] . TextFormat::GREEN . " fué desbaneado");
        } else {
            $sender->sendMessage(Translation::translate("noPermission"));
        }
        return true;
    }
}