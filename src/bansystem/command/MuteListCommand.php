<?php

namespace bansystem\command;

use bansystem\Manager;
use bansystem\translation\Translation;
use bansystem\util\ArrayPage;
use InvalidArgumentException;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class MuteListCommand extends Command {
    
    public function __construct() {
        parent::__construct("mutelist");
        $this->description = "§eLista de todos los muteados en el servidor";
        $this->usageMessage = "§e/mutelist <nombre | ip> [pagina]";
        $this->setPermission("bansystem.command.mutelist");
    }
    
    private function forEachLists(string $type) : array {
        $array = array();
        switch ($type) {
            case "name":
                $nameMutes = Manager::getNameMutes();
                foreach ($nameMutes->getEntries() as $nameEntry) {
                    $array[count($array)] = $nameEntry->getName();
                }
                break;
            case "ip":
                $ipMutes = Manager::getIPMutes();
                foreach ($ipMutes->getEntries() as $ipEntry) {
                    $array[count($array)] = $ipEntry->getName();
                }
                break;
            default:
                throw new InvalidArgumentException("Invalid type.");
        }
        return $array;
    }
    
    public function execute(CommandSender $sender, $commandLabel, array $args) {
        if ($this->testPermissionSilent($sender)) {
            if (count($args) <= 0) {
                $sender->sendMessage(Translation::translateParams("usage", array($this)));
                return false;
            }
            try {
                $names = $this->forEachLists(strtolower($args[0]));
                $arrayPage = new ArrayPage($names, 5);
                $page = 1;
                if (count($args) >= 2) {
                    if (is_numeric($args[1])) {
                        if (intval($args[1]) > $arrayPage->getMaxPages() || intval($args[1]) <= 0) {
                            $sender->sendMessage(TextFormat::GOLD . "Ingresa un número válido");
                            return false;
                        }
                        $page = intval($args[1]);
                    } else {
                        $sender->sendMessage(TextFormat::GOLD . "\"" . $args[1] . "\" es un número de página inválido");
                        return false;
                    }
                }
                $sender->sendMessage(TextFormat::DARK_GREEN . "--[" . TextFormat::GREEN . "Hay " . strval(count($names)) . " " . (strtolower($args[0]) == "name" ? "players" : "IP address") . " muteados en el servidor." . TextFormat::DARK_GREEN . "]--");
                if (count($names) >= 1) {
                    foreach ($arrayPage->yieldFromPage($page) as $nameValue) {
                        $sender->sendMessage(TextFormat::AQUA . $nameValue);
                    }
                } else {
                    $sender->sendMessage(TextFormat::GOLD . "No hay lista de muteados");
                }
                $sender->sendMessage(TextFormat::GREEN . "------------[Page (" . strval($page <= $arrayPage->getMaxPages() ? $page : "1") . " / " . strval($arrayPage->getMaxPages()) . ")]------------");
            } catch (InvalidArgumentException $ex) {
                $sender->sendMessage(Translation::translateParams("usage", array($this)));
            }
        } else {
            $sender->sendMessage(Translation::translate("noPermission"));
        }
        return true;
    }
}