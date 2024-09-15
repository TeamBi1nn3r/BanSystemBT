<?php

namespace bansystem\command;

use bansystem\Manager;
use bansystem\translation\Translation;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use bansystem\BanSystem;

class UnblockCommand extends Command {

    public $pl;
    
    public function __construct(BanSystem $bv) {
        parent::__construct("unblock");
        $this->pl = $bv;
        $this->description = "§eDesbloquea a un jugador si fué bloqueado antes";
        $this->usageMessage = "§a/unblock §c<nombre>";
        $this->setPermission("bansystem.command.unblock");
    }
    
    public function execute(CommandSender $sender, $commandLabel, array $args) {
        if ($this->testPermissionSilent($sender)) {


            $banList = $sender->getServer()->getNameBans();
            $banListIP = $sender->getServer()->getIPBans();
            $cidBans = $sender->getServer()->getCIDBans();

            $cfg = $this->pl;

            $getUUIDS = $cfg->getUUIDConfig();
            $getIPS = $cfg->getIPSConfig();
            $getCIDS = $cfg->getCIDConfig();

            $getNameUUID = $getUUIDS->get($args[0]);
                    $getNameIPS = $getIPS->get($args[0]);
                    $getNameCIDS = $getCIDS->get($args[0]);

            if (count($args) <= 0) {
                $sender->sendMessage(Translation::translateParams("usage", array($this)));
                return false;
            }
            $blockList = Manager::getNameBlocks();
            if (!$blockList->isBanned($getNameUUID)) {
                $sender->sendMessage("§eEste jugador no fue bloqueado del servidor");
                return false;
            }
            $getNameUUID = $getUUIDS->get($args[0]);
                    $getNameIPS = $getIPS->get($args[0]);
                    $getNameCIDS = $getCIDS->get($args[0]);

                    $banList->remove($args[0]);
                    $banListIP->remove($getNameIPS);
            $blockList->remove($getNameUUID);
            $cidBans->remove($getNameCIDS);

            $sender->getServer()->broadcastMessage(TextFormat::AQUA . $args[0] . TextFormat::GREEN . " fué desbloqueado");
        } else {
            
        }
    }
}