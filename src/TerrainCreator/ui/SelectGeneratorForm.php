<?php

declare(strict_types=1);

namespace TerrainCreator\ui;

use pocketmine\form\Form;
use pocketmine\player\Player;
use TerrainCreator\Main;

class SelectGeneratorForm implements Form {

    public const MODE_EDIT = 0;
    public const MODE_DELETE = 1;
    public const MODE_CREATE_WORLD = 2;

    private Main $plugin;
    private int $mode;

    public function __construct(Main $plugin, int $mode) {
        $this->plugin = $plugin;
        $this->mode = $mode;
    }

    public function jsonSerialize(): array {
        $generators = $this->plugin->getConfigManager()->getAllGenerators();
        
        if (empty($generators)) {
            return [
                "type" => "form",
                "title" => "§l§cKeine Generatoren",
                "content" => "§7Es sind noch keine Generatoren vorhanden.\nErstelle zuerst einen Generator!",
                "buttons" => [
                    ["text" => "§l§aZurück"]
                ]
            ];
        }

        $buttons = [];
        foreach (array_keys($generators) as $name) {
            $icon = match($this->mode) {
                self::MODE_EDIT => "§e✎",
                self::MODE_DELETE => "§c✖",
                self::MODE_CREATE_WORLD => "§a+",
                default => "§7•"
            };
            $buttons[] = [
                "text" => "$icon §r$name"
            ];
        }
        
        $buttons[] = ["text" => "§l§8« Zurück"];

        $title = match($this->mode) {
            self::MODE_EDIT => "§l§eGenerator bearbeiten",
            self::MODE_DELETE => "§l§cGenerator löschen",
            self::MODE_CREATE_WORLD => "§l§bWelt erstellen",
            default => "§l§7Generator auswählen"
        };

        return [
            "type" => "form",
            "title" => $title,
            "content" => "§7Wähle einen Generator:",
            "buttons" => $buttons
        ];
    }

    public function handleResponse(Player $player, $data): void {
        if ($data === null) {
            return;
        }

        $generators = array_keys($this->plugin->getConfigManager()->getAllGenerators());
        
        if (empty($generators)) {
            $player->sendForm(new MainMenuForm($this->plugin));
            return;
        }

        // Zurück-Button
        if ($data === count($generators)) {
            $player->sendForm(new MainMenuForm($this->plugin));
            return;
        }

        $selectedGenerator = $generators[$data];

        match($this->mode) {
            self::MODE_EDIT => $this->handleEdit($player, $selectedGenerator),
            self::MODE_DELETE => $this->handleDelete($player, $selectedGenerator),
            self::MODE_CREATE_WORLD => $this->handleCreateWorld($player, $selectedGenerator),
            default => null
        };
    }

    private function handleEdit(Player $player, string $generatorName): void {
        $config = $this->plugin->getConfigManager()->getGenerator($generatorName);
        $player->sendForm(new TerrainConfigForm($this->plugin, $generatorName, $config));
    }

    private function handleDelete(Player $player, string $generatorName): void {
        $player->sendForm(new ConfirmDeleteForm($this->plugin, $generatorName));
    }

    private function handleCreateWorld(Player $player, string $generatorName): void {
        $player->sendForm(new CreateWorldForm($this->plugin, $generatorName));
    }
}
