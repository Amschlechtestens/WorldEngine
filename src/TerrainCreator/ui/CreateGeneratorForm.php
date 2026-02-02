<?php

declare(strict_types=1);

namespace TerrainCreator\ui;

use pocketmine\form\Form;
use pocketmine\player\Player;
use TerrainCreator\Main;

class CreateGeneratorForm implements Form {

    private Main $plugin;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    public function jsonSerialize(): array {
        return [
            "type" => "custom_form",
            "title" => "§l§aNeuer Generator",
            "content" => [
                [
                    "type" => "input",
                    "text" => "§7Generator Name:",
                    "placeholder" => "z.B. MyTerrainGen"
                ],
                [
                    "type" => "label",
                    "text" => "§7Dieser Name wird zur Identifikation des Generators verwendet."
                ]
            ]
        ];
    }

    public function handleResponse(Player $player, $data): void {
        if ($data === null) {
            return;
        }

        $name = trim($data[0] ?? "");
        
        if ($name === "") {
            $player->sendMessage("§cBitte gib einen gültigen Namen ein!");
            return;
        }

        if ($this->plugin->getConfigManager()->generatorExists($name)) {
            $player->sendMessage("§cEin Generator mit diesem Namen existiert bereits!");
            return;
        }

        // Öffne das Terrain-Konfigurations-Formular
        $player->sendForm(new TerrainConfigForm($this->plugin, $name));
    }
}
