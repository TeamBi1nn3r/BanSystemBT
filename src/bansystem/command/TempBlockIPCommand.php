<?php

namespace bansystem\command;

use bansystem\Manager;
use bansystem\translation\Translation;
use bansystem\util\date\Countdown;
use DateTime;
use InvalidArgumentException;
use bansystem\GetApiCountry;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class TempBlockIPCommand extends Command {

    public $apiCountry;
    
    public function __construct() {
        parent::__construct("tempblock-c");
        $this->description = "§eBloquea temporalmente un pais §7(Escribir el nombre de los paises en ingles!)";
        $this->usageMessage = "/tempblock-c <Pais> <FormatoDeTiempo> [Motivo]";
        $this->setPermission("bansystem.command.tempblockc");
    }
    
    public function execute(CommandSender $sender, $commandLabel, array $args) {
        if ($this->testPermissionSilent($sender)) {
            if (count($args) <= 1) {
                $sender->sendMessage(Translation::translateParams("usage", array($this)));
                return false;
            }
           
           // $ip = filter_var($args[0], FILTER_VALIDATE_IP);
           
           
            
            $blockList = Manager::getIPBlocks();
            if ($blockList->isBanned($args[0])) {
                $sender->sendMessage("§cEste Pais ya está baneado");
                return false;
            }
            try {
                $expiry = new Countdown($args[1]);
                $expiryToString = Countdown::expirationTimerToString($expiry->getDate(), new DateTime());
                if (count($args) == 2) {
                    
                        
                            $blockList->addBan($args[0], null, $expiry->getDate(), $sender->getName());
                            $sender->getServer()->broadcastMessage( "§cEl pais " . TextFormat::AQUA . $args[0] . TextFormat::RED . " fué baneado del servidor. \n §eDurante: " . TextFormat::AQUA . $expiryToString . TextFormat::RED . "\n §ePor: " . TextFormat::AQUA. $sender->getName());
                            $sender->getServer()->broadcastMessage("§7El baneo se aplicará a los usuarios de aquel pais que ingresen al servidor a partir de ahora");
                           
                        
                }else if (count($args) >= 3) {
                    $reason = "";
                    for ($i = 2; $i < count($args); $i++) {
                        $reason .= $args[$i];
                        $reason .= " ";
                    }
                    $reason = substr($reason, 0, strlen($reason) - 1);
                    
                        
                            $blockList->addBan($args[0], $reason, $expiry->getDate(), $sender->getName());
                            $sender->getServer()->broadcastMessage("§cEl pais " . TextFormat::AQUA . $args[0] . TextFormat::RED . " fué baneado del servidor. \n §eMotivo " . TextFormat::AQUA . $reason . TextFormat::RED . "\n §eDurante: " . TextFormat::AQUA . $expiryToString . TextFormat::RED . "\n §ePor: " . $sender->getName());
                            $sender->getServer()->broadcastMessage("§7El baneo se aplicará a los usuarios de aquel pais que ingresen al servidor a partir de ahora");

                
                }
            } catch (InvalidArgumentException $ex) {
                $sender->sendMessage(TextFormat::RED . $ex->getMessage());
            }
        } else {
            $sender->sendMessage(Translation::translate("noPermission"));
        }
        return true;
    }
}