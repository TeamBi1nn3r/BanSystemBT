<?php

namespace bansystem\command;

use bansystem\translation\Translation;
use bansystem\util\date\Countdown;
use DateTime;
use InvalidArgumentException;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class TempBanCommand extends Command {
    
    public function __construct() {
        parent::__construct("tempban");
        $this->description = "§eBanea a un jugador por un tiempo definido";
        $this->usageMessage = "§a/tempban §c<jugador> <FormatoDeTiempo> [motivo]";
        $this->setPermission("bansystem.command.tempban");
    }
    
    public function execute(CommandSender $sender, $commandLabel, array $args) {
        if ($this->testPermissionSilent($sender)) {
            if (count($args) <= 1) {
                $sender->sendMessage(Translation::translateParams("usage", array($this)));
                return false;
            }
            $player = $sender->getServer()->getPlayer($args[0]);
            $playerName = $args[0]; 
            $banList = $sender->getServer()->getNameBans();
            try {
                if ($banList->isBanned($args[0])) {
                    $sender->sendMessage(Translation::translate("playerAlreadyBanned"));
                    return false;
                }
                $expiry = new Countdown($args[1]);
                $expiryToString = Countdown::expirationTimerToString($expiry->getDate(), new DateTime());
                if (count($args) == 2) {
                    if ($player != null) {
                        $playerName = $player->getName();
                        $banList->addBan($player->getName(), null, $expiry->getDate(), $sender->getName());
                        $player->kick(TextFormat::RED . "Fuiste temporalmente baneado,"
                                . " Tu baneo expira en " . TextFormat::YELLOW . $expiryToString . TextFormat::RED . ".", false);
                    } else {
                        $banList->addBan($args[0], null, $expiry->getDate(), $sender->getName());
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
                        $banList->addBan($player->getName(), $banReason, $expiry->getDate(), $sender->getName());
                        $player->kick(TextFormat::RED . "Fuiste temporalmente baneado por " . TextFormat::BLUE . $banReason . TextFormat::RED . ","
                                . " Tu baneo expira en " . TextFormat::BLUE . $expiryToString . TextFormat::RED . ".", false);
                    } else {
                        $banList->addBan($args[0], $banReason, $expiry->getDate(), $sender->getName());
                    }
                    $sender->getServer()->broadcastMessage(TextFormat::RED . $playerName
                            . TextFormat::YELLOW . " Fué baneado por " . TextFormat::GREEN . $banReason . TextFormat::RED . " Durante " . TextFormat::GREEN . $expiryToString . TextFormat::RED . ".");
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