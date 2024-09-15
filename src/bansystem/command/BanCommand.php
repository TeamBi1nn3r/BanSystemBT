<?php namespace bansystem\command;

use bansystem\translation\Translation;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class BanCommand extends Command {
    public function __construct() {
        parent::__construct("ban");
        $this->description = "§eBanea a un jugador";
        $this->usageMessage = "§a/ban §c<jugador> [motivo]";
        $this->setPermission("bansystem.command.ban");
    }

    public function execute(CommandSender $sender, $label, array $args) {
        if ($this->testPermissionSilent($sender)) {
            if (count($args) <= 0) {
                $sender->sendMessage(Translation::translateParams("usage", array($this)));
                return false;
            }

            $player = $sender->getServer()->getPlayer($args[0]);
            $banList = $sender->getServer()->getNameBans();
            $playerName = $args[0];
            $baneador = $sender->getName();

            if ($banList->isBanned($args[0])) {
                $sender->sendMessage(Translation::translate("playerAlreadyBanned"));
                return false;
            }

            if (count($args) == 1) {
                if ($player != null) {
                    $banList->addBan($player->getName(), null, null, $sender->getName());
                    $player->kick(TextFormat::RED . "Fuiste Baneado!", false);
                    $playerName = $player->getName();
                } else {
                    $banList->addBan($args[0], null, null, $sender->getName());
                }
                $sender->getServer()->broadcastMessage(TextFormat::AQUA . $playerName . TextFormat::RED . " Fue baneado.");
            } else if (count($args) >= 2) {
                $reason = "";
                for ($i = 1; $i < count($args); $i++) {
                    $reason .= $args[$i];
                    $reason .= " ";
                }
                $reason = substr($reason, 0, strlen($reason) - 1);

                if ($player != null) {
                    $banList->addBan($player->getName(), $reason, null, $sender->getName());
                    $player->kick(TextFormat::GREEN . "Fuiste §cBaneado! \n\n §eMotivo: " . TextFormat::RED . $reason . TextFormat::RED . ". \n§ePor: " .TextFormat::RED . $baneador . ".\n §ePuedes apelar en: §cDiscord. \n §1https://discord.gg/anXJR8jEuB", false);
                    $playerName = $player->getName();
                } else {
                    $banList->addBan($args[0], $reason, null, $sender->getName());
                }
                $sender->getServer()->broadcastMessage(TextFormat::AQUA . $playerName . TextFormat::RED . "Fue baneado por " . TextFormat::YELLOW . $reason . TextFormat::RED . ".");
            }
        } else {
            $sender->sendMessage(Translation::translate("noPermission"));
        }

        return true;
    }
}
