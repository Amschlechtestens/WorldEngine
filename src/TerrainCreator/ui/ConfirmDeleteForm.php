<?php

declare(strict_types=1);

namespace TerrainCreator\ui;

use pocketmine\form\Form;
use pocketmine\player\Player;
use TerrainCreator\Main;

class ConfirmDeleteForm implements Form {

    private Main $plugin;
    private string $generatorName;

    public function __construct(Main $plugin, string $generatorName) {
        $this->plugin = $plugin;
        $this->generatorName = $generatorName;
    }

    public function jsonSerialize(): array {
        return [
            "type" => "modal",
            "title" => "§l§cGenerator löschen",
            "content" => "§7Möchtest du den Generator §e" . $this->generatorName . " §7wirklich löschen?\n\n§cDiese Aktion kann nicht rückgängig gemacht werden!",
            "button1" => "§l§cLöschen",
            "button2" => "§l§aAbbrechen"
        ];
    }

    public function handleResponse(Player $player, $data): void {
        if ($data === null || $data === false) {
            $player->sendForm(new MainMenuForm($this->plugin));
            return;
        }

        if ($this->plugin->getConfigManager()->deleteGenerator($this->generatorName)) {
            $player->sendMessage("§aGenerator '§e" . $this->generatorName . "§a' wurde gelöscht!");
        } else {
            $player->sendMessage("§cFehler beim Löschen des Generators!");
        }
        
        $player->sendForm(new MainMenuForm($this->plugin));
    }
}
