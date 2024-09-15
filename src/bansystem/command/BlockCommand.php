<?php

namespace bansystem\command;

use bansystem\Manager;
use bansystem\BanSystem;
use bansystem\translation\Translation;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class BlockCommand extends Command {

    public $plug;
    
    public function __construct(BanSystem $bn) {
        parent::__construct("block");
        $this->plug = $bn;
        $this->description = "§eBanea a un jugador por Nombre, IP, ClientID e ID Única";
        $this->usageMessage = "§a/block §c<nombre> [motivo]";
        $this->setPermission("bansystem.command.block");
    }
    
    public function execute(CommandSender $sender, $commandLabel, array $args) {
        if ($this->testPermissionSilent($sender)) {
            if (count($args) <= 0) {
                $sender->sendMessage(Translation::translateParams("usage", array($this)));
                return false;
            }

            $configs = $this->plug;

            $getUUIDS = $configs->getUUIDConfig();
            $getIPS = $configs->getIPSConfig();
            $getCIDS = $configs->getCIDConfig();


            $blockList = Manager::getNameBlocks();
            $player = $sender->getServer()->getPlayer($args[0]);

            $banList = $sender->getServer()->getNameBans();
            $banListIP = $sender->getServer()->getIPBans();
            $cidBans = $sender->getServer()->getCIDBans();

            $getNameUUID = $getUUIDS->get($args[0]);
                    $getNameIPS = $getIPS->get($args[0]);
                    $getNameCIDS = $getCIDS->get($args[0]);         
            
            
            //$uuidList = Manager::getUUID

            $player = $sender->getServer()->getPlayer($args[0]);
            
            $target = $sender->getServer()->getPlayer($args[0]);
            $cid = $player ? $player->getClientId() : null;
            $ip = $player ? $player->getPlayer()->getAddress() : null;
           $uuid = $player ? $player->getUniqueId()->toString() : null;


            if ($blockList->isBanned($getNameUUID)) {
                $sender->sendMessage(Translation::translate("playerAlreadyBlocked"));
                return false;
            }
            if (count($args) == 1) {
                if ($player != null or $uuid != null) {
                   
                    $blockList->addBan($uuid, null, null, $sender->getName());
                    $banList->addBan($player->getName(), null, null, $sender->getName());
                    $banListIP->addBan($ip, null, null, $sender->getName());
                    $cidBans->addBan($cid, null, null, $sender->getName());

                    $sender->getServer()->broadcastMessage(TextFormat::AQUA . $player->getName() . TextFormat::RED . " fué bloqueado");
                    $player->kick(TextFormat::RED . "Fuiste Bloqueado!", false);
                } else{
                    $getNameUUID = $getUUIDS->get($args[0]);
                    $getNameIPS = $getIPS->get($args[0]);
                    $getNameCIDS = $getCIDS->get($args[0]);

                    if($getNameUUID != null && $getNameIPS != null && $getNameCIDS != null){
                  
                    $blockList->addBan($getNameUUID, null, null, $sender->getName());
                    $banList->addBan($args[0], null, null, $sender->getName());
                    $banListIP->addBan($getNameIPS, null, null, $sender->getName());
                    $cidBans->addBan($getNameCIDS, null, null, $sender->getName());

                    $sender->getServer()->broadcastMessage(TextFormat::AQUA . $args[0] . TextFormat::RED . " fué bloqueado");
                }else{
                    $sender->sendMessage("§cEl jugador proporcionado no tiene registros dentro del servidor!");
                }
            }
            } else if (count($args) >= 2) {
                $reason = "";
                for ($i = 1; $i < count($args); $i++) {
                    $reason .= $args[$i];
                    $reason .= " ";
                }
                $reason = substr($reason, 0, strlen($reason) - 1);
                if ($player != null && $uuid != null) {
                  
                    $baneador = $sender->getName();
                    $blockList->addBan($uuid, $reason, null, $sender->getName());
                    $banList->addBan($player->getName(), $reason, null, $sender->getName());
                    $banListIP->addBan($ip, $reason, null, $sender->getName());
                    $cidBans->addBan($cid, $reason, null, $sender->getName());

                    $sender->getServer()->broadcastMessage(TextFormat::AQUA . $player->getName() . TextFormat::RED . " fué bloqueado por " . TextFormat::AQUA . $reason . TextFormat::RED . ".");
                    $player->kick(TextFormat::GREEN . "Fuiste §cBloqueado! \n\n §eMotivo: " . TextFormat::RED . $reason . TextFormat::RED . ". \n§ePor: " .TextFormat::RED . $baneador . ".\n §ePuedes apelar en: §cDiscord. \n §1https://discord.gg/anXJR8jEuB", false);
                } else{

                    $getNameUUID = $getUUIDS->get($args[0]);
                    $getNameIPS = $getIPS->get($args[0]);
                    $getNameCIDS = $getCIDS->get($args[0]);


                    if($getNameUUID != null && $getNameIPS != null && $getNameCIDS != null){

                        
                     $blockList->addBan($getNameUUID, $reason, null, $sender->getName());
                    $banList->addBan($args[0], $reason, null, $sender->getName());
                    $banListIP->addBan($getNameIPS, $reason, null, $sender->getName());
                    $cidBans->addBan($getNameCIDS, $reason, null, $sender->getName());
                }else{
                    $sender->sendMessage("§cEl jugador proporcionado no tiene registros dentro del servidor!");
                }
            }
            }
        } else {
            $sender->sendMessage(Translation::translate("noPermission"));
        }
        return true;
    }
}