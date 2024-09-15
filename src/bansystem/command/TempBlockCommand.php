<?php

namespace bansystem\command;

use bansystem\translation\Translation;
use bansystem\util\date\Countdown;
use DateTime;
use InvalidArgumentException;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use bansystem\BanSystem;
use bansystem\Manager;

class TempBlockCommand extends Command {

    public $bs;
    
    public function __construct(BanSystem $sb) {
        parent::__construct("tempblock");
        $this->bs = $sb;
        $this->description = "§eBloquea a un jugador por un tiempo definido (menos eficiente)";
        $this->usageMessage = "§a/tempblock §c<jugador> <FormatoDeTiempo> [motivo]";
        $this->setPermission("bansystem.command.tempblock");
    }
    
    public function execute(CommandSender $sender, $commandLabel, array $args) {
        if ($this->testPermissionSilent($sender)) {

            if (count($args) <= 1 or $args[0] === null) {
                $sender->sendMessage(Translation::translateParams("usage", array($this)));
                return false;
            }
            $playerName = $args[0]; 
            $configs = $this->bs;

            $getUUIDS = $configs->getUUIDConfig();
            $getIPS = $configs->getIPSConfig();
            $getCIDS = $configs->getCIDConfig();


            $blockList = Manager::getNameBlocks();
            $player = $sender->getServer()->getPlayer($args[0]);

            $banList = $sender->getServer()->getNameBans();
            $banListIP = $sender->getServer()->getIPBans();
            $cidBans = $sender->getServer()->getCIDBans();
         
            
            $cid = $player ? $player->getClientId() : null;
            $ip = $player ? $player->getPlayer()->getAddress() : null;
           $uuid = $player ? $player->getUniqueId()->toString() : null;


            try {
                if ($blockList->isBanned($args[0])) {
                    $sender->sendMessage(Translation::translate("playerAlreadyBanned"));
                    return false;
                }
                $expiry = new Countdown($args[1]);
                $expiryToString = Countdown::expirationTimerToString($expiry->getDate(), new DateTime());
                if (count($args) == 2) {
                    if ($player != null) {
                        $playerName = $player->getName();


                      $banList->addBan($player->getName(), null, $expiry->getDate(), $sender->getName());
                        $blockList->addBan($uuid, null, $expiry->getDate(), $sender->getName());                   
                        $banListIP->addBan($ip, null, $expiry->getDate(), $sender->getName());
                        $cidBans->addBan($cid, null, $expiry->getDate(), $sender->getName());

                        $player->kick(TextFormat::RED . "Fuiste temporalmente baneado,"
                                . " Tu baneo expira en " . TextFormat::YELLOW . $expiryToString . TextFormat::RED . ".", false);
                    } else {

                        $getNameUUID = $getUUIDS->get($args[0]);
                        $getNameIPS = $getIPS->get($args[0]);
                        $getNameCIDS = $getCIDS->get($args[0]);

                        if($getNameUUID != null && $getNameIPS != null && $getNameCIDS != null){

                         $banList->addBan($args[0], null, $expiry->getDate(), $sender->getName());
                        $blockList->addBan($getNameUUID, null, $expiry->getDate(), $sender->getName());
                        $banListIP->addBan($getNameIPS, null, $expiry->getDate(), $sender->getName());
                        $cidBans->addBan($getNameCIDS, null, $expiry->getDate(), $sender->getName());
                       
                        }else{
                            $sender->sendMessage("§cEl jugador proporcionado no tiene registros dentro del servidor!");
                            return true;
                        }

                    }
                    $sender->getServer()->broadcastMessage(TextFormat::RED . $playerName
                            . TextFormat::YELLOW . " fué baneado durante " . TextFormat::RED . $expiryToString . TextFormat::RED . ".");
                    
                } else if (count($args) >= 3) {
                    $banReason = "";
                    for ($i = 2; $i < count($args); $i++) {
                        $banReason .= $args[$i];
                        $banReason .= " ";
                    }
                    $banReason = substr($banReason, 0, strlen($banReason) - 1);
                    if ($player != null) {
                        $bansed = $sender->getName();


                        $banList->addBan($player->getName(), $banReason, $expiry->getDate(), $sender->getName());
                        $blockList->addBan($uuid, $banReason, $expiry->getDate(), $sender->getName());                        
                        $banListIP->addBan($ip, $banReason, $expiry->getDate(), $sender->getName());
                        $cidBans->addBan($cid, $banReason, $expiry->getDate(), $sender->getName());

                        $player->kick(TextFormat::RED . "¡Fuiste temporalmente baneado! \n Por:  {$bansed} \n §eMotivo: " . TextFormat::BLUE . $banReason . TextFormat::RED . "\n §eDurante: "
                                 . TextFormat::BLUE . $expiryToString . TextFormat::RED . ".", false);
                    } else {

                        $getNameUUID = $getUUIDS->get($args[0]);
                        $getNameIPS = $getIPS->get($args[0]);
                        $getNameCIDS = $getCIDS->get($args[0]);

                        if($getNameUUID != null && $getNameIPS != null && $getNameCIDS != null){

                        $banList->addBan($args[0], $banReason, $expiry->getDate(), $sender->getName());
                        $blockList->addBan($getNameUUID, $banReason, $expiry->getDate(), $sender->getName()); 
                        $banListIP->addBan($getNameIPS, $banReason, $expiry->getDate(), $sender->getName());
                        $cidBans->addBan($getNameCIDS, $banReason, $expiry->getDate(), $sender->getName());
                    }else{
                        $sender->sendMessage("§cEl jugador proporcionado no tiene registros dentro del servidor!");
                        return true;
                    }

                    }
                    $bansed = $sender->getName();
                    $sender->getServer()->broadcastMessage(TextFormat::RED . $playerName
                            . TextFormat::YELLOW . "§cFué baneado del servidor \n §ePor: {$bansed} \n §eMotivo: " . TextFormat::RED . $banReason . TextFormat::RED . "\n §eDurante " . TextFormat::GREEN . $expiryToString . TextFormat::RED . ".");
                }
            } catch (InvalidArgumentException $e) {
                $sender->sendMessage(TextFormat::RED . $e->getMessage());
            }
        } else {
            $sender->sendMessage(Translation::translate("noPermission"));
        }
        return true;
    }
}