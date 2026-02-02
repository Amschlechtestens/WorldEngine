<?php

declare(strict_types=1);

namespace TerrainCreator\ui;

use pocketmine\form\Form;
use pocketmine\player\Player;
use TerrainCreator\Main;

class MainMenuForm implements Form {

    private Main $plugin;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    public function jsonSerialize(): array {
        $generators = $this->plugin->getConfigManager()->getAllGenerators();
        $buttons = [
            [
                "text" => "§l§aNeuen Generator erstellen\n§r§7Erstelle einen neuen Terrain-Generator"
            ],
            [
                "text" => "§l§6Preset laden\n§r§7Lade eine vorgefertigte Vorlage"
            ],
            [
                "text" => "§l§eGenerator bearbeiten\n§r§7Bearbeite einen existierenden Generator"
            ],
            [
                "text" => "§l§cGenerator löschen\n§r§7Lösche einen Generator"
            ],
            [
                "text" => "§l§bWelt erstellen\n§r§7Erstelle eine Welt mit einem Generator"
            ]
        ];

        return [
            "type" => "form",
            "title" => "§l§6Terrain Creator",
            "content" => "§7Wähle eine Option:\n§8Verfügbare Generatoren: §e" . count($generators),
            "buttons" => $buttons
        ];
    }

    public function handleResponse(Player $player, $data): void {
        if ($data === null) {
            return;
        }

        switch ($data) {
            case 0:
                $player->sendForm(new CreateGeneratorForm($this->plugin));
                break;
            case 1:
                $player->sendForm(new PresetForm($this->plugin));
                break;
            case 2:
                $player->sendForm(new SelectGeneratorForm($this->plugin, SelectGeneratorForm::MODE_EDIT));
                break;
            case 3:
                $player->sendForm(new SelectGeneratorForm($this->plugin, SelectGeneratorForm::MODE_DELETE));
                break;
            case 4:
                $player->sendForm(new SelectGeneratorForm($this->plugin, SelectGeneratorForm::MODE_CREATE_WORLD));
                break;
        }
    }
}