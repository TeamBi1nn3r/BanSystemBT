<?php

namespace bansystem\command;

use bansystem\Manager;
use bansystem\translation\Translation;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use bansystem\GetApiCountry;
use bansystem\BanSystem;

class BlockIPCommand extends Command {

    public $capi;
    public $plugi;
    public $apiCountry;
    
    public function __construct() {
        parent::__construct("block-c");
        $this->description = "§eBloquea o Banea paises del servidor §7(Escribir el nombre de los paises en ingles!)";
        $this->usageMessage = "/block-c <Pais> [motivo]";
        $this->setPermission("bansystem.command.blockc");
    
    }
    
    public function execute(CommandSender $sender, $commandLabel, array $args) {
        if ($this->testPermissionSilent($sender)) {
            
        
            if (count($args) <= 0) {
                $sender->sendMessage(Translation::translateParams("usage", array($this)));
                return false;
            }

            
            $blockList = Manager::getIPBlocks();
            $playerName = $args[0];
            $baneador = $sender->getName();

            if ($blockList->isBanned($args[0])) {
                $sender->sendMessage("§cEste Pais ya está baneado");
                return false;
            }

            if (count($args) == 1) {
                    $blockList->addBan($args[0], null, null, $sender->getName());
                
                $sender->getServer()->broadcastMessage("§cEl pais " . TextFormat::AQUA . $playerName . TextFormat::RED . " Fue baneado del servidor");
                $sender->getServer()->broadcastMessage("§7El baneo se aplicará a los usuarios de aquel pais que ingresen al servidor a partir de ahora");

                
            } else if (count($args) >= 2) {
                $reason = "";
                for ($i = 1; $i < count($args); $i++) {
                    $reason .= $args[$i];
                    $reason .= " ";
                }
                $reason = substr($reason, 0, strlen($reason) - 1);

               
                    $blockList->addBan($args[0], $reason, null, $sender->getName());
                
                $sender->getServer()->broadcastMessage("§cEl pais " . TextFormat::AQUA . $playerName . TextFormat::RED . "Fue baneado por " . TextFormat::YELLOW . $reason . TextFormat::RED . ".");
                $sender->getServer()->broadcastMessage("§7El baneo se aplicará a los usuarios de aquel pais que ingresen al servidor a partir de ahora");

                
            
        } else {
            $sender->sendMessage(Translation::translate("noPermission"));
        }

        return true;
    }
}
}