<?php

namespace bansystem\command;

use bansystem\Manager;
use bansystem\translation\Translation;
use bansystem\util\date\Countdown;
use DateTime;
use InvalidArgumentException;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class TempMuteIPCommand extends Command {
    
    public function __construct() {
        parent::__construct("tempmute-ip");
        $this->description = "Mutea temporalmente mediante IP";
        $this->usageMessage = "§e/tempmute-ip <jugador | IP> <FormatoDeTiempo> [motivo]";
        $this->setPermission("bansystem.command.tempmuteip");
    }
    
    public function execute(CommandSender $sender, $commandLabel, array $args) {
        if ($this->testPermission($sender)) {
            if (count($args) <= 1) {
                $sender->sendMessage(Translation::translateParams("usage", array($this)));
                return false;
            }
            $muteList = Manager::getIPMutes();
            if ($muteList->isBanned($args[0])) {
                $sender->sendMessage(Translation::translate("ipAlreadyMuted"));
                return false;
            }
            try {
                $player = $sender->getServer()->getPlayer($args[0]);
                $ip = filter_var($args[0], FILTER_VALIDATE_IP);
                $expiry = new Countdown($args[1]);
                $expiryToString = Countdown::expirationTimerToString($expiry->getDate(), new DateTime());
                if (count($args) == 2) {
                    if ($ip != null) {
                        $muteList->addBan($ip, null, $expiry->getDate(), $sender->getName());
                        foreach ($sender->getServer()->getOnlinePlayers() as $players) {
                            if ($player->getAddress() == $ip) {
                                $player->sendMessage(TextFormat::RED . "Tu ip fue muteada durante " . TextFormat::GREEN . $expiryToString . TextFormat::RED . ".");
                            }
                        }
                        $sender->getServer()->broadcastMessage(TextFormat::RED . "La IP " . TextFormat::AQUA . $ip . TextFormat::RED . " fué muteada durante " . TextFormat::AQUA . $expiryToString . TextFormat::RED . ".");
                    } else {
                        $muteList->addBan($player->getAddress(), null, $expiry->getDate(), $sender->getName());
                        $player->sendMessage(TextFormat::RED . "Tu IP fué muteada temporalmente durante " . TextFormat::AQUA . $expiryToString . TextFormat::RED . ".");
                        $sender->getServer()->broadcastMessage(TextFormat::AQUA . $player->getName() . TextFormat::RED . " fue muteado durante " . TextFormat::AQUA . $expiryToString . TextFormat::RED . ".");
                    }
                } else if (count($args) >= 3) {
                    $reason = "";
                    for ($i = 2; $i < count($args); $i++) {
                        $reason .= $args[$i];
                        $reason .= " ";
                    }
                    $reason = substr($reason, 0, strlen($reason) - 1);
                    if ($ip != null) {
                        $muteList->addBan($ip, $reason, $expiry->getDate(), $sender->getName());
                        foreach ($sender->getServer()->getOnlinePlayers() as $players) {
                            if ($player->getAddress() == $ip) {
                                $player->sendMessage(TextFormat::RED . "Tu IP fue muteada por " . TextFormat::AQUA . $reason . " Durante " . TextFormat::AQUA . $expiryToString . TextFormat::RED . ".");
                            }
                        }
                        $sender->getServer()->broadcastMessage(TextFormat::AQUA . $ip . TextFormat::RED . " fué muteada por " . TextFormat::AQUA . $reason . TextFormat::RED . " Durante " . TextFormat::AQUA . $expiryToString . TextFormat::RED . ".");
                    } else {
                        $muteList->addBan($player->getAddress(), $reason, $expiry->getDate(), $sender->getName());
                        $player->sendMessage(TextFormat::RED . "Tu IP fué muteada por " . TextFormat::RED . $reason . TextFormat::RED . " durante " . TextFormat::AQUA . $expiryToString . TextFormat::RED . ".");
                        $sender->getServer()->broadcastMessage(TextFormat::AQUA . $player->getName() . TextFormat::RED . " fue muteado durante " . TextFormat::AQUA . $expiryToString . TextFormat::RED . ".");
                    }
                }
            } catch (InvalidArgumentException $ex) {
                $sender->sendMessage(TextFormat::RED . $ex->getMessage());
            }
        } else {
            $sender->sendMessage(Translation::translate("noPermission"));
        }
    }
}