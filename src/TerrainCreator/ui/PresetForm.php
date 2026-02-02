<?php

declare(strict_types=1);

namespace TerrainCreator\ui;

use pocketmine\form\Form;
use pocketmine\player\Player;
use TerrainCreator\Main;

class PresetForm implements Form {

    private Main $plugin;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    public function jsonSerialize(): array {
        return [
            "type" => "form",
            "title" => "§l§6Terrain Presets",
            "content" => "§7Wähle ein Preset zum Laden:\n§8Diese Vorlagen kannst du anpassen!",
            "buttons" => [
                [
                    "text" => "§l§2Extreme Mountains\n§r§7Hohe, dramatische Berge"
                ],
                [
                    "text" => "§l§aRolling Hills\n§r§7Sanfte Hügel"
                ],
                [
                    "text" => "§l§ePlains\n§r§7Flaches Grasland"
                ],
                [
                    "text" => "§l§cCanyons\n§r§7Tiefe Schluchten"
                ],
                [
                    "text" => "§l§9Islands\n§r§7Inselwelt"
                ],
                [
                    "text" => "§l§6Mesa/Badlands\n§r§7Terrassen-Landschaft"
                ],
                [
                    "text" => "§l§dAlien World\n§r§7Fremdartige Landschaft"
                ],
                [
                    "text" => "§l§5Floating Islands\n§r§7Schwebende Inseln"
                ],
                [
                    "text" => "§l§3Deep Ocean\n§r§7Tiefe Ozeane"
                ],
                [
                    "text" => "§l§8Volcanic\n§r§7Vulkanische Landschaft"
                ],
                [
                    "text" => "§l§8« Zurück"
                ]
            ]
        ];
    }

    public function handleResponse(Player $player, $data): void {
        if ($data === null || $data === 10) {
            $player->sendForm(new MainMenuForm($this->plugin));
            return;
        }

        $presets = $this->getPresets();
        $presetNames = array_keys($presets);

        if (!isset($presetNames[$data])) {
            return;
        }

        $presetName = $presetNames[$data];
        $config = $presets[$presetName];

        // Öffne Terrain Config Form mit Preset
        $player->sendForm(new TerrainConfigForm($this->plugin, $presetName, $config));
    }

    private function getPresets(): array {
        return [
            'ExtremeMountains' => [
                'baseHeight' => 80,
                'waterLevel' => 62,
                'minHeight' => 0,
                'maxHeight' => 280,
                'mainNoise' => [
                    'type' => 'Perlin',
                    'amplitude' => 120.0,
                    'frequency' => 0.003,
                    'octaves' => 10,
                    'persistence' => 0.65,
                    'lacunarity' => 2.3
                ],
                'detailNoise' => [
                    'enabled' => true,
                    'type' => 'Simplex',
                    'amplitude' => 20.0,
                    'frequency' => 0.08
                ],
                'ridgeNoise' => [
                    'enabled' => true,
                    'amplitude' => 60.0,
                    'frequency' => 0.008,
                    'sharpness' => 3.0
                ],
                'domainWarp' => [
                    'enabled' => true,
                    'strength' => 80.0,
                    'iterations' => 2
                ],
                'heightMultiplier' => 1.3,
                'heightExponent' => 1.4,
                'terraces' => [
                    'enabled' => false,
                    'steps' => 8,
                    'strength' => 0.0
                ],
                'biomBlend' => [
                    'enabled' => true,
                    'radius' => 2000.0,
                    'frequency' => 0.0005
                ],
                'blocks' => [
                    'stone' => 'stone',
                    'dirt' => 'dirt',
                    'surface' => 'grass',
                    'water' => 'water',
                    'bedrock' => 'bedrock'
                ],
                'dirtDepth' => 4,
                'generateBedrock' => true
            ],

            'RollingHills' => [
                'baseHeight' => 68,
                'waterLevel' => 62,
                'minHeight' => 40,
                'maxHeight' => 120,
                'mainNoise' => [
                    'type' => 'Simplex',
                    'amplitude' => 30.0,
                    'frequency' => 0.008,
                    'octaves' => 6,
                    'persistence' => 0.5,
                    'lacunarity' => 2.0
                ],
                'detailNoise' => [
                    'enabled' => true,
                    'type' => 'Value',
                    'amplitude' => 8.0,
                    'frequency' => 0.05
                ],
                'ridgeNoise' => [
                    'enabled' => false,
                    'amplitude' => 30.0,
                    'frequency' => 0.01,
                    'sharpness' => 2.0
                ],
                'domainWarp' => [
                    'enabled' => true,
                    'strength' => 40.0,
                    'iterations' => 1
                ],
                'heightMultiplier' => 1.0,
                'heightExponent' => 0.9,
                'terraces' => [
                    'enabled' => false,
                    'steps' => 8,
                    'strength' => 0.0
                ],
                'biomBlend' => [
                    'enabled' => true,
                    'radius' => 1500.0,
                    'frequency' => 0.001
                ],
                'blocks' => [
                    'stone' => 'stone',
                    'dirt' => 'dirt',
                    'surface' => 'grass',
                    'water' => 'water',
                    'bedrock' => 'bedrock'
                ],
                'dirtDepth' => 5,
                'generateBedrock' => true
            ],

            'Plains' => [
                'baseHeight' => 64,
                'waterLevel' => 62,
                'minHeight' => 58,
                'maxHeight' => 75,
                'mainNoise' => [
                    'type' => 'Value',
                    'amplitude' => 6.0,
                    'frequency' => 0.015,
                    'octaves' => 4,
                    'persistence' => 0.4,
                    'lacunarity' => 1.8
                ],
                'detailNoise' => [
                    'enabled' => true,
                    'type' => 'Gradient',
                    'amplitude' => 2.0,
                    'frequency' => 0.1
                ],
                'ridgeNoise' => [
                    'enabled' => false,
                    'amplitude' => 30.0,
                    'frequency' => 0.01,
                    'sharpness' => 2.0
                ],
                'domainWarp' => [
                    'enabled' => false,
                    'strength' => 50.0,
                    'iterations' => 2
                ],
                'heightMultiplier' => 1.0,
                'heightExponent' => 1.0,
                'terraces' => [
                    'enabled' => false,
                    'steps' => 8,
                    'strength' => 0.0
                ],
                'biomBlend' => [
                    'enabled' => true,
                    'radius' => 3000.0,
                    'frequency' => 0.0003
                ],
                'blocks' => [
                    'stone' => 'stone',
                    'dirt' => 'dirt',
                    'surface' => 'grass',
                    'water' => 'water',
                    'bedrock' => 'bedrock'
                ],
                'dirtDepth' => 6,
                'generateBedrock' => true
            ],

            'Canyons' => [
                'baseHeight' => 70,
                'waterLevel' => 40,
                'minHeight' => 10,
                'maxHeight' => 140,
                'mainNoise' => [
                    'type' => 'Worley',
                    'amplitude' => 60.0,
                    'frequency' => 0.006,
                    'octaves' => 6,
                    'persistence' => 0.55,
                    'lacunarity' => 2.5
                ],
                'detailNoise' => [
                    'enabled' => true,
                    'type' => 'Cellular',
                    'amplitude' => 15.0,
                    'frequency' => 0.03
                ],
                'ridgeNoise' => [
                    'enabled' => true,
                    'amplitude' => 40.0,
                    'frequency' => 0.012,
                    'sharpness' => 2.5
                ],
                'domainWarp' => [
                    'enabled' => true,
                    'strength' => 100.0,
                    'iterations' => 3
                ],
                'heightMultiplier' => 1.2,
                'heightExponent' => 1.5,
                'terraces' => [
                    'enabled' => false,
                    'steps' => 8,
                    'strength' => 0.0
                ],
                'biomBlend' => [
                    'enabled' => false,
                    'radius' => 1000.0,
                    'frequency' => 0.001
                ],
                'blocks' => [
                    'stone' => 'red_sandstone',
                    'dirt' => 'sand',
                    'surface' => 'red_sand',
                    'water' => 'water',
                    'bedrock' => 'bedrock'
                ],
                'dirtDepth' => 3,
                'generateBedrock' => true
            ],

            'Islands' => [
                'baseHeight' => 50,
                'waterLevel' => 62,
                'minHeight' => 30,
                'maxHeight' => 100,
                'mainNoise' => [
                    'type' => 'Gradient',
                    'amplitude' => 40.0,
                    'frequency' => 0.01,
                    'octaves' => 7,
                    'persistence' => 0.5,
                    'lacunarity' => 2.0
                ],
                'detailNoise' => [
                    'enabled' => true,
                    'type' => 'Simplex',
                    'amplitude' => 12.0,
                    'frequency' => 0.06
                ],
                'ridgeNoise' => [
                    'enabled' => false,
                    'amplitude' => 30.0,
                    'frequency' => 0.01,
                    'sharpness' => 2.0
                ],
                'domainWarp' => [
                    'enabled' => true,
                    'strength' => 60.0,
                    'iterations' => 2
                ],
                'heightMultiplier' => 1.1,
                'heightExponent' => 1.2,
                'terraces' => [
                    'enabled' => false,
                    'steps' => 8,
                    'strength' => 0.0
                ],
                'biomBlend' => [
                    'enabled' => true,
                    'radius' => 800.0,
                    'frequency' => 0.002
                ],
                'blocks' => [
                    'stone' => 'stone',
                    'dirt' => 'dirt',
                    'surface' => 'grass',
                    'water' => 'water',
                    'bedrock' => 'bedrock'
                ],
                'dirtDepth' => 4,
                'generateBedrock' => true
            ],

            'Mesa' => [
                'baseHeight' => 75,
                'waterLevel' => 62,
                'minHeight' => 50,
                'maxHeight' => 160,
                'mainNoise' => [
                    'type' => 'Perlin',
                    'amplitude' => 45.0,
                    'frequency' => 0.007,
                    'octaves' => 6,
                    'persistence' => 0.5,
                    'lacunarity' => 2.0
                ],
                'detailNoise' => [
                    'enabled' => true,
                    'type' => 'Cubic',
                    'amplitude' => 10.0,
                    'frequency' => 0.04
                ],
                'ridgeNoise' => [
                    'enabled' => false,
                    'amplitude' => 30.0,
                    'frequency' => 0.01,
                    'sharpness' => 2.0
                ],
                'domainWarp' => [
                    'enabled' => false,
                    'strength' => 50.0,
                    'iterations' => 2
                ],
                'heightMultiplier' => 1.0,
                'heightExponent' => 1.0,
                'terraces' => [
                    'enabled' => true,
                    'steps' => 12,
                    'strength' => 0.8
                ],
                'biomBlend' => [
                    'enabled' => false,
                    'radius' => 1000.0,
                    'frequency' => 0.001
                ],
                'blocks' => [
                    'stone' => 'terracotta',
                    'dirt' => 'red_sand',
                    'surface' => 'red_sand',
                    'water' => 'water',
                    'bedrock' => 'bedrock'
                ],
                'dirtDepth' => 2,
                'generateBedrock' => true
            ],

            'AlienWorld' => [
                'baseHeight' => 70,
                'waterLevel' => 62,
                'minHeight' => 0,
                'maxHeight' => 250,
                'mainNoise' => [
                    'type' => 'Worley',
                    'amplitude' => 90.0,
                    'frequency' => 0.004,
                    'octaves' => 8,
                    'persistence' => 0.7,
                    'lacunarity' => 3.0
                ],
                'detailNoise' => [
                    'enabled' => true,
                    'type' => 'Cellular',
                    'amplitude' => 30.0,
                    'frequency' => 0.02
                ],
                'ridgeNoise' => [
                    'enabled' => true,
                    'amplitude' => 50.0,
                    'frequency' => 0.015,
                    'sharpness' => 3.5
                ],
                'domainWarp' => [
                    'enabled' => true,
                    'strength' => 150.0,
                    'iterations' => 4
                ],
                'heightMultiplier' => 1.5,
                'heightExponent' => 1.6,
                'terraces' => [
                    'enabled' => false,
                    'steps' => 8,
                    'strength' => 0.0
                ],
                'biomBlend' => [
                    'enabled' => true,
                    'radius' => 500.0,
                    'frequency' => 0.005
                ],
                'blocks' => [
                    'stone' => 'end_stone',
                    'dirt' => 'mycelium',
                    'surface' => 'warped_nylium',
                    'water' => 'lava',
                    'bedrock' => 'bedrock'
                ],
                'dirtDepth' => 3,
                'generateBedrock' => true
            ],

            'FloatingIslands' => [
                'baseHeight' => 150,
                'waterLevel' => 0,
                'minHeight' => 80,
                'maxHeight' => 250,
                'mainNoise' => [
                    'type' => 'OpenSimplex2',
                    'amplitude' => 50.0,
                    'frequency' => 0.012,
                    'octaves' => 5,
                    'persistence' => 0.6,
                    'lacunarity' => 2.2
                ],
                'detailNoise' => [
                    'enabled' => true,
                    'type' => 'Worley',
                    'amplitude' => 25.0,
                    'frequency' => 0.03
                ],
                'ridgeNoise' => [
                    'enabled' => false,
                    'amplitude' => 30.0,
                    'frequency' => 0.01,
                    'sharpness' => 2.0
                ],
                'domainWarp' => [
                    'enabled' => true,
                    'strength' => 70.0,
                    'iterations' => 2
                ],
                'heightMultiplier' => 1.4,
                'heightExponent' => 2.0,
                'terraces' => [
                    'enabled' => false,
                    'steps' => 8,
                    'strength' => 0.0
                ],
                'biomBlend' => [
                    'enabled' => true,
                    'radius' => 600.0,
                    'frequency' => 0.003
                ],
                'blocks' => [
                    'stone' => 'stone',
                    'dirt' => 'dirt',
                    'surface' => 'grass',
                    'water' => 'air',
                    'bedrock' => 'bedrock'
                ],
                'dirtDepth' => 4,
                'generateBedrock' => false
            ],

            'DeepOcean' => [
                'baseHeight' => 30,
                'waterLevel' => 62,
                'minHeight' => 10,
                'maxHeight' => 45,
                'mainNoise' => [
                    'type' => 'Simplex',
                    'amplitude' => 15.0,
                    'frequency' => 0.01,
                    'octaves' => 5,
                    'persistence' => 0.45,
                    'lacunarity' => 1.9
                ],
                'detailNoise' => [
                    'enabled' => true,
                    'type' => 'Value',
                    'amplitude' => 5.0,
                    'frequency' => 0.08
                ],
                'ridgeNoise' => [
                    'enabled' => false,
                    'amplitude' => 30.0,
                    'frequency' => 0.01,
                    'sharpness' => 2.0
                ],
                'domainWarp' => [
                    'enabled' => true,
                    'strength' => 30.0,
                    'iterations' => 1
                ],
                'heightMultiplier' => 1.0,
                'heightExponent' => 0.8,
                'terraces' => [
                    'enabled' => false,
                    'steps' => 8,
                    'strength' => 0.0
                ],
                'biomBlend' => [
                    'enabled' => true,
                    'radius' => 2000.0,
                    'frequency' => 0.0008
                ],
                'blocks' => [
                    'stone' => 'stone',
                    'dirt' => 'gravel',
                    'surface' => 'sand',
                    'water' => 'water',
                    'bedrock' => 'bedrock'
                ],
                'dirtDepth' => 3,
                'generateBedrock' => true
            ],

            'Volcanic' => [
                'baseHeight' => 70,
                'waterLevel' => 62,
                'minHeight' => 30,
                'maxHeight' => 200,
                'mainNoise' => [
                    'type' => 'Perlin',
                    'amplitude' => 70.0,
                    'frequency' => 0.005,
                    'octaves' => 7,
                    'persistence' => 0.6,
                    'lacunarity' => 2.4
                ],
                'detailNoise' => [
                    'enabled' => true,
                    'type' => 'Cellular',
                    'amplitude' => 18.0,
                    'frequency' => 0.04
                ],
                'ridgeNoise' => [
                    'enabled' => true,
                    'amplitude' => 55.0,
                    'frequency' => 0.009,
                    'sharpness' => 2.8
                ],
                'domainWarp' => [
                    'enabled' => true,
                    'strength' => 90.0,
                    'iterations' => 3
                ],
                'heightMultiplier' => 1.3,
                'heightExponent' => 1.5,
                'terraces' => [
                    'enabled' => false,
                    'steps' => 8,
                    'strength' => 0.0
                ],
                'biomBlend' => [
                    'enabled' => true,
                    'radius' => 1200.0,
                    'frequency' => 0.0012
                ],
                'blocks' => [
                    'stone' => 'basalt',
                    'dirt' => 'blackstone',
                    'surface' => 'netherrack',
                    'water' => 'lava',
                    'bedrock' => 'bedrock'
                ],
                'dirtDepth' => 2,
                'generateBedrock' => true
            ]
        ];
    }
}