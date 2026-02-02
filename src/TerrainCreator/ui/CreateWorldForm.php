<?php

declare(strict_types=1);

namespace TerrainCreator\ui;

use pocketmine\form\Form;
use pocketmine\player\Player;
use pocketmine\world\World;
use pocketmine\world\WorldCreationOptions;
use TerrainCreator\Main;
use TerrainCreator\generator\CustomTerrainGenerator;

class CreateWorldForm implements Form {

    private Main $plugin;
    private string $generatorName;

    public function __construct(Main $plugin, string $generatorName) {
        $this->plugin = $plugin;
        $this->generatorName = $generatorName;
    }

    public function jsonSerialize(): array {
        return [
            "type" => "custom_form",
            "title" => "§l§bWelt erstellen",
            "content" => [
                [
                    "type" => "label",
                    "text" => "§7Generator: §e" . $this->generatorName
                ],
                [
                    "type" => "input",
                    "text" => "§7Welt-Name:",
                    "placeholder" => "z.B. MyWorld"
                ],
                [
                    "type" => "input",
                    "text" => "§7Seed (optional):",
                    "placeholder" => "Leer für zufällig"
                ]
            ]
        ];
    }

    public function handleResponse(Player $player, $data): void {
        if ($data === null) {
            $player->sendForm(new MainMenuForm($this->plugin));
            return;
        }

        $worldName = trim($data[1] ?? "");
        $seed = trim($data[2] ?? "");

        if ($worldName === "") {
            $player->sendMessage("§cBitte gib einen gültigen Welt-Namen ein!");
            return;
        }

        $worldManager = $this->plugin->getServer()->getWorldManager();
        
        if ($worldManager->isWorldLoaded($worldName) || $worldManager->isWorldGenerated($worldName)) {
            $player->sendMessage("§cEine Welt mit diesem Namen existiert bereits!");
            return;
        }

        $config = $this->plugin->getConfigManager()->getGenerator($this->generatorName);
        if ($config === null) {
            $player->sendMessage("§cGenerator nicht gefunden!");
            return;
        }

        try {
            $seedInt = $seed !== "" ? (int)$seed : mt_rand(0, 2147483647);
            
            $options = new WorldCreationOptions();
            $options->setSeed($seedInt);
            $options->setGeneratorClass(CustomTerrainGenerator::class);
            $options->setGeneratorOptions(json_encode([
                'generatorName' => $this->generatorName,
                'config' => $config
            ]));

            $worldManager->generateWorld($worldName, $options);
            
            if ($worldManager->loadWorld($worldName)) {
                $player->sendMessage("§aWelt '§e" . $worldName . "§a' wurde erstellt!");
                $player->sendMessage("§7Verwende §e/mv tp " . $worldName . " §7um zur Welt zu teleportieren.");
            } else {
                $player->sendMessage("§cWelt wurde erstellt, konnte aber nicht geladen werden!");
            }
        } catch (\Exception $e) {
            $player->sendMessage("§cFehler beim Erstellen der Welt: " . $e->getMessage());
            $this->plugin->getLogger()->error("World creation error: " . $e->getMessage());
        }
        
        $player->sendForm(new MainMenuForm($this->plugin));
    }
}
