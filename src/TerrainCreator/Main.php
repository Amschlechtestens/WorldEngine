<?php

declare(strict_types=1);

namespace TerrainCreator;

use pocketmine\plugin\PluginBase;
use pocketmine\world\generator\GeneratorManager;
use TerrainCreator\commands\TerrainCreatorCommand;
use TerrainCreator\generator\CustomTerrainGenerator;

class Main extends PluginBase {

    private static self $instance;
    private TerrainConfigManager $configManager;

    protected function onEnable(): void {
        self::$instance = $this;
        
        $this->saveDefaultConfig();
        $this->configManager = new TerrainConfigManager($this);
        
        // Registriere den benutzerdefinierten Generator
        GeneratorManager::getInstance()->addGenerator(
            CustomTerrainGenerator::class,
            "custom",
            fn() => null
        );
        
        // Registriere Commands
        $this->getServer()->getCommandMap()->register(
            "terraincreator",
            new TerrainCreatorCommand($this)
        );
        
        $this->getLogger()->info("TerrainCreator Plugin aktiviert!");
    }

    protected function onDisable(): void {
        $this->configManager->saveAll();
        $this->getLogger()->info("TerrainCreator Plugin deaktiviert!");
    }

    public static function getInstance(): self {
        return self::$instance;
    }

    public function getConfigManager(): TerrainConfigManager {
        return $this->configManager;
    }
}
