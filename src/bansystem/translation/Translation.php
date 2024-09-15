<?php

namespace bansystem\translation;

use bansystem\exception\TranslationFailedException;
use InvalidArgumentException;
use pocketmine\command\Command;
use pocketmine\utils\TextFormat;

class Translation {
    
    public static function translate(string $translation) : string {
        switch ($translation) {
            case "noPermission":
                return TextFormat::RED . "§cNo tienes permiso para usar el comando";
            case "playerNotFound":
                return TextFormat::GOLD . "§cEl jugador no está online";
            case "playerAlreadyBanned":
                return TextFormat::GOLD . "§cEl jugador ya está baneado";
            case "ipAlreadyBanned":
                return TextFormat::GOLD . "§cLa IP ya está baneada";
            case "ipNotBanned":
                return TextFormat::GOLD . "La IP no está baneada";
            case "ipAlreadyMuted":
                return TextFormat::GOLD . "La IP ya está silenciada";
            case "playerNotBanned":
                return TextFormat::GOLD . "El jugador no está baneado";
            case "playerAlreadyMuted":
                return TextFormat::GOLD . "El jugador ya está silenciado";
            case "playerNotMuted":
                return TextFormat::GOLD . "El jugador no está silenciado";
            case "ipNotMuted":
                return TextFormat::GOLD . "La IP no está silenciada";
            case "playerAlreadyBlocked":
                return TextFormat::GOLD . "Player is already blocked.";
            case "playerNotBlocked":
                return TextFormat::GOLD . "Player is not blocked.";
            case "ipAlreadyBlocked":
                return TextFormat::GOLD . "IP address is already blocked.";
            case "ipNotBlocked":
                return TextFormat::GOLD . "IP address is not blocked.";
            default:
                throw new TranslationFailedException("Failed to translate.");
        }
    }
    
    public static function translateParams(string $translation, array $parameters) : string {
        if (empty($parameters)) {
            throw new InvalidArgumentException("Parameter is empty.");
        }
        switch ($translation) {
            case "usage":
                $command = $parameters[0];
                if ($command instanceof Command) {
                    return TextFormat::DARK_GREEN . "Usage: " . TextFormat::GREEN . $command->getUsage();
                } else {
                    throw new InvalidArgumentException("Parameter index 0 must be the type of Command.");
                }
        }
    }
}