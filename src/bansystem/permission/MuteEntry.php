<?php

namespace bansystem\permission;

use pocketmine\permission\BanEntry;

class MuteEntry extends BanEntry {
    
    public function __construct($name) {
        parent::__construct($name);
        $this->setReason("Fuiste silenciado por un moderador");
    }
}