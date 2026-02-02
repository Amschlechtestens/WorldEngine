<?php

declare(strict_types=1);

namespace TerrainCreator;

use pocketmine\utils\Config;

class TerrainConfigManager {

    private Main $plugin;
    private array $generators = [];
    private Config $generatorsConfig;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
        $this->generatorsConfig = new Config(
            $plugin->getDataFolder() . "generators.yml",
            Config::YAML
        );
        $this->loadGenerators();
    }

    private function loadGenerators(): void {
        $this->generators = $this->generatorsConfig->getAll();
    }

    public function saveGenerator(string $name, array $config): void {
        $this->generators[$name] = $config;
        $this->generatorsConfig->set($name, $config);
        $this->generatorsConfig->save();
    }

    public function deleteGenerator(string $name): bool {
        if (isset($this->generators[$name])) {
            unset($this->generators[$name]);
            $this->generatorsConfig->remove($name);
            $this->generatorsConfig->save();
            return true;
        }
        return false;
    }

    public function getGenerator(string $name): ?array {
        return $this->generators[$name] ?? null;
    }

    public function getAllGenerators(): array {
        return $this->generators;
    }

    public function generatorExists(string $name): bool {
        return isset($this->generators[$name]);
    }

    public function saveAll(): void {
        $this->generatorsConfig->setAll($this->generators);
        $this->generatorsConfig->save();
    }
}
