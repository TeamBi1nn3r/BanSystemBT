<?php

namespace bansystem\command;

use bansystem\translation\Translation;
use bansystem\util\date\Countdown;
use DateTime;
use InvalidArgumentException;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class TempBanIPCommand extends Command {
    
    public function __construct() {
        parent::__construct("tempban-ip");
        $this->description = "§eBanea a un jugador mediante su IP por un tiempo definido";
        $this->usageMessage = "§a/tempban-ip§c <jugador | IP> <FormatoDeTiempo> [motivo]";
        $this->setPermission("bansystem.command.tempbanip");
    }
    
    public function execute(CommandSender $sender, $commandLabel, array $args) {
        if ($this->testPermissionSilent($sender)) {
            if (count($args) <= 1) {
                $sender->sendMessage(Translation::translateParams("usage", array($this)));
                return false;
            }
            $player = $sender->getServer()->getPlayer($args[0]);
            $banList = $sender->getServer()->getIPBans();
            if ($banList->isBanned($args[0])) {
                $sender->sendMessage(Translation::translate("ipAlreadyBanned"));
                return false;
            }
            try {
                $expiry = new Countdown($args[1]);
                $expiryToString = Countdown::expirationTimerToString($expiry->getDate(), new DateTime());
                $ip = filter_var($args[0], FILTER_VALIDATE_IP);
                if (count($args) == 2) {
                    if ($ip != null) {
                        $banList->addBan($ip, null, $expiry->getDate(), $sender->getName());
                        foreach ($sender->getServer()->getOnlinePlayers() as $onlinePlayers) {
                            if ($onlinePlayers->getAddress() == $ip) {
                                $onlinePlayers->kick(TextFormat::RED . "Fuiste baneado temporalmente mediante tu IP,"
                                        . " Tu baneo por IP expira en " . TextFormat::YELLOW . $expiryToString . TextFormat::RED . ".", false);
                            }
                        }
                        $sender->getServer()->broadcastMessage(TextFormat::RED . "La IP " . TextFormat::YELLOW . $ip . TextFormat::RED . " fué baneada con durante " . TextFormat::YELLOW . $expiryToString . TextFormat::RED . ".");
                    } else {
                        if ($player != null) {
                            $player->kick(TextFormat::RED . "Fuiste baneado temporalmente mediante tu IP,"
                                        . " Tu baneo por IP expira en " . TextFormat::YELLOW . $expiryToString . TextFormat::RED . ".", false);
                            $banList->addBan($player->getName(), null, $expiry->getDate(), $sender->getName());
                            $sender->getServer()->broadcastMessage(TextFormat::RED . $player->getName() . TextFormat::YELLOW . " fué baneado con durante " . TextFormat::AQUA . $expiryToString . TextFormat::RED . ".");
                        } else {
                            $sender->sendMessage(Translation::translate("playerNotFound"));
                        }
                    }
                } else if (count($args) >= 3) {
                    $reason = "";
                    for ($i = 2; $i < count($args); $i++) {
                        $reason .= $args[$i];
                        $reason .= " ";
                    }
                    $reason = substr($reason, 0, strlen($reason) - 1);
                    if ($ip != null) {
                        $banList->addBan($ip, $reason, $expiry->getDate(), $sender->getName());
                        foreach ($sender->getServer()->getOnlinePlayers() as $players) {
                            if ($players->getAddress() == $ip) {
                                $players->kick(TextFormat::RED . "Fuiste temporalmente baneado mediante IP por " . TextFormat::YELLOW . $reason . TextFormat::RED . ","
                                    . " Tu baneo por IP expira en " . TextFormat::AQUA . $expiryToString . TextFormat::RED . ".", false);
                            }
                        }
                        $sender->getServer()->broadcastMessage(TextFormat::RED . "La IP " . TextFormat::YELLOW . $ip . TextFormat::RED . " fué baneada mediante IP por " . TextFormat::YELLOW . $reason . TextFormat::RED . " Durante " . TextFormat::YELLOW . $expiryToString . TextFormat::RED . ".");
                    } else {
                        if ($player != null) {
                            $banList->addBan($player->getAddress(), $reason, $expiry->getDate(), $sender->getName());
                            $player->kick(TextFormat::RED . "Fuiste temporalmente baneado por " . TextFormat::YELLOW . $reason . TextFormat::RED . ","
                                    . " Tu baneo por IP expira en " . TextFormat::AQUA . $expiryToString . TextFormat::RED . ".", false);
                            $sender->getServer()->broadcastMessage(TextFormat::AQUA . $player->getName() . TextFormat::RED . " fue baneado por " . TextFormat::AQUA . $reason . TextFormat::RED . " Durante " . TextFormat::AQUA . $expiryToString . TextFormat::RED . ".");
                        } else {
                            $sender->sendMessage(Translation::translate("playerNotFound"));
                        }
                    }
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