<?php

declare(strict_types=1);

namespace TerrainCreator\ui;

use pocketmine\form\Form;
use pocketmine\player\Player;
use TerrainCreator\Main;

class TerrainConfigForm implements Form {

    private Main $plugin;
    private string $generatorName;
    private ?array $existingConfig;

    public function __construct(Main $plugin, string $generatorName, ?array $existingConfig = null) {
        $this->plugin = $plugin;
        $this->generatorName = $generatorName;
        $this->existingConfig = $existingConfig;
    }

    public function jsonSerialize(): array {
        $config = $this->existingConfig ?? $this->getDefaultConfig();

        return [
            "type" => "custom_form",
            "title" => "§l§6Terrain Konfiguration",
            "content" => [
                // === GRUNDEINSTELLUNGEN ===
                [
                    "type" => "label",
                    "text" => "§l§e=== GRUNDEINSTELLUNGEN ===\n§r§7Generator: §e" . $this->generatorName
                ],
                [
                    "type" => "slider",
                    "text" => "§7Basis-Höhe:",
                    "min" => 0,
                    "max" => 256,
                    "step" => 1,
                    "default" => $config['baseHeight']
                ],
                [
                    "type" => "slider",
                    "text" => "§7Wasser-Level:",
                    "min" => 0,
                    "max" => 256,
                    "step" => 1,
                    "default" => $config['waterLevel']
                ],
                [
                    "type" => "slider",
                    "text" => "§7Min Höhe:",
                    "min" => -64,
                    "max" => 256,
                    "step" => 1,
                    "default" => $config['minHeight']
                ],
                [
                    "type" => "slider",
                    "text" => "§7Max Höhe:",
                    "min" => 0,
                    "max" => 320,
                    "step" => 1,
                    "default" => $config['maxHeight']
                ],

                // === HAUPT-NOISE ===
                [
                    "type" => "label",
                    "text" => "§l§a=== HAUPT-NOISE ===\n§r§7Bestimmt die grundlegende Terrain-Form"
                ],
                [
                    "type" => "dropdown",
                    "text" => "§7Noise-Typ:",
                    "options" => [
                        "Perlin - Natürlich, organisch",
                        "Simplex - Schneller, glatter",
                        "OpenSimplex2 - Verbessert, keine Muster",
                        "Cellular - Zelluläre Strukturen",
                        "Value - Einfach, blocky",
                        "Worley - Organische Zellen",
                        "Gradient - Sehr glatt",
                        "Cubic - Kubisch interpoliert"
                    ],
                    "default" => $this->getNoiseTypeIndex($config['mainNoise']['type'])
                ],
                [
                    "type" => "slider",
                    "text" => "§7Amplitude (Höhenvariation):",
                    "min" => 1,
                    "max" => 200,
                    "step" => 1,
                    "default" => $config['mainNoise']['amplitude']
                ],
                [
                    "type" => "slider",
                    "text" => "§7Frequenz (Detailgrad):",
                    "min" => 0.001,
                    "max" => 0.1,
                    "step" => 0.001,
                    "default" => $config['mainNoise']['frequency']
                ],
                [
                    "type" => "slider",
                    "text" => "§7Oktaven (Detailstufen):",
                    "min" => 1,
                    "max" => 12,
                    "step" => 1,
                    "default" => $config['mainNoise']['octaves']
                ],
                [
                    "type" => "slider",
                    "text" => "§7Persistence (Detail-Stärke):",
                    "min" => 0.1,
                    "max" => 1.0,
                    "step" => 0.05,
                    "default" => $config['mainNoise']['persistence']
                ],
                [
                    "type" => "slider",
                    "text" => "§7Lacunarity (Detail-Frequenz):",
                    "min" => 1.5,
                    "max" => 4.0,
                    "step" => 0.1,
                    "default" => $config['mainNoise']['lacunarity']
                ],

                // === DETAIL-NOISE ===
                [
                    "type" => "label",
                    "text" => "§l§b=== DETAIL-NOISE ===\n§r§7Fügt kleinere Details hinzu"
                ],
                [
                    "type" => "toggle",
                    "text" => "§7Detail-Noise aktivieren",
                    "default" => $config['detailNoise']['enabled']
                ],
                [
                    "type" => "dropdown",
                    "text" => "§7Detail Noise-Typ:",
                    "options" => [
                        "Perlin", "Simplex", "OpenSimplex2", "Cellular",
                        "Value", "Worley", "Gradient", "Cubic"
                    ],
                    "default" => $this->getNoiseTypeIndex($config['detailNoise']['type'])
                ],
                [
                    "type" => "slider",
                    "text" => "§7Detail Amplitude:",
                    "min" => 1,
                    "max" => 50,
                    "step" => 1,
                    "default" => $config['detailNoise']['amplitude']
                ],
                [
                    "type" => "slider",
                    "text" => "§7Detail Frequenz:",
                    "min" => 0.01,
                    "max" => 0.5,
                    "step" => 0.01,
                    "default" => $config['detailNoise']['frequency']
                ],

                // === RIDGE-NOISE (GEBIRGSKÄMME) ===
                [
                    "type" => "label",
                    "text" => "§l§c=== RIDGE-NOISE ===\n§r§7Erstellt scharfe Gebirgskämme"
                ],
                [
                    "type" => "toggle",
                    "text" => "§7Ridge-Noise aktivieren",
                    "default" => $config['ridgeNoise']['enabled']
                ],
                [
                    "type" => "slider",
                    "text" => "§7Ridge Amplitude:",
                    "min" => 1,
                    "max" => 100,
                    "step" => 1,
                    "default" => $config['ridgeNoise']['amplitude']
                ],
                [
                    "type" => "slider",
                    "text" => "§7Ridge Frequenz:",
                    "min" => 0.001,
                    "max" => 0.05,
                    "step" => 0.001,
                    "default" => $config['ridgeNoise']['frequency']
                ],
                [
                    "type" => "slider",
                    "text" => "§7Ridge Schärfe:",
                    "min" => 0.5,
                    "max" => 4.0,
                    "step" => 0.1,
                    "default" => $config['ridgeNoise']['sharpness']
                ],

                // === DOMAIN WARPING ===
                [
                    "type" => "label",
                    "text" => "§l§d=== DOMAIN WARPING ===\n§r§7Verzerrt das Terrain für natürlichere Formen"
                ],
                [
                    "type" => "toggle",
                    "text" => "§7Domain Warping aktivieren",
                    "default" => $config['domainWarp']['enabled']
                ],
                [
                    "type" => "slider",
                    "text" => "§7Warp-Stärke:",
                    "min" => 1,
                    "max" => 200,
                    "step" => 1,
                    "default" => $config['domainWarp']['strength']
                ],
                [
                    "type" => "slider",
                    "text" => "§7Warp-Iterationen:",
                    "min" => 1,
                    "max" => 5,
                    "step" => 1,
                    "default" => $config['domainWarp']['iterations']
                ],

                // === HÖHEN-MODIFIKATOREN ===
                [
                    "type" => "label",
                    "text" => "§l§e=== HÖHEN-MODIFIKATOREN ==="
                ],
                [
                    "type" => "slider",
                    "text" => "§7Höhen-Multiplikator:",
                    "min" => 0.1,
                    "max" => 5.0,
                    "step" => 0.1,
                    "default" => $config['heightMultiplier']
                ],
                [
                    "type" => "slider",
                    "text" => "§7Höhen-Exponent (Steilheit):",
                    "min" => 0.5,
                    "max" => 3.0,
                    "step" => 0.1,
                    "default" => $config['heightExponent']
                ],

                // === TERRASSEN-EFFEKT ===
                [
                    "type" => "label",
                    "text" => "§l§6=== TERRASSEN-EFFEKT ===\n§r§7Mesa/Badlands-ähnliche Stufen"
                ],
                [
                    "type" => "toggle",
                    "text" => "§7Terrassen aktivieren",
                    "default" => $config['terraces']['enabled']
                ],
                [
                    "type" => "slider",
                    "text" => "§7Anzahl Stufen:",
                    "min" => 2,
                    "max" => 32,
                    "step" => 1,
                    "default" => $config['terraces']['steps']
                ],
                [
                    "type" => "slider",
                    "text" => "§7Terrassen-Stärke:",
                    "min" => 0.0,
                    "max" => 1.0,
                    "step" => 0.05,
                    "default" => $config['terraces']['strength']
                ],

                // === BIOM-BLENDING ===
                [
                    "type" => "label",
                    "text" => "§l§a=== BIOM-BLENDING ===\n§r§7Mischt verschiedene Terrain-Typen"
                ],
                [
                    "type" => "toggle",
                    "text" => "§7Biom-Blending aktivieren",
                    "default" => $config['biomBlend']['enabled']
                ],
                [
                    "type" => "slider",
                    "text" => "§7Blend-Radius:",
                    "min" => 100,
                    "max" => 5000,
                    "step" => 100,
                    "default" => $config['biomBlend']['radius']
                ],
                [
                    "type" => "slider",
                    "text" => "§7Blend-Frequenz:",
                    "min" => 0.0001,
                    "max" => 0.01,
                    "step" => 0.0001,
                    "default" => $config['biomBlend']['frequency']
                ],

                // === BLOCK-EINSTELLUNGEN ===
                [
                    "type" => "label",
                    "text" => "§l§7=== BLOCK-EINSTELLUNGEN ==="
                ],
                [
                    "type" => "input",
                    "text" => "§7Stein-Block:",
                    "placeholder" => "stone",
                    "default" => $config['blocks']['stone']
                ],
                [
                    "type" => "input",
                    "text" => "§7Erd-Block:",
                    "placeholder" => "dirt",
                    "default" => $config['blocks']['dirt']
                ],
                [
                    "type" => "input",
                    "text" => "§7Oberflächen-Block:",
                    "placeholder" => "grass",
                    "default" => $config['blocks']['surface']
                ],
                [
                    "type" => "input",
                    "text" => "§7Wasser-Block:",
                    "placeholder" => "water",
                    "default" => $config['blocks']['water']
                ],
                [
                    "type" => "input",
                    "text" => "§7Bedrock-Block:",
                    "placeholder" => "bedrock",
                    "default" => $config['blocks']['bedrock']
                ],
                [
                    "type" => "slider",
                    "text" => "§7Erd-Tiefe:",
                    "min" => 1,
                    "max" => 10,
                    "step" => 1,
                    "default" => $config['dirtDepth']
                ],
                [
                    "type" => "toggle",
                    "text" => "§7Bedrock generieren",
                    "default" => $config['generateBedrock']
                ]
            ]
        ];
    }

    public function handleResponse(Player $player, $data): void {
        if ($data === null) {
            $player->sendForm(new MainMenuForm($this->plugin));
            return;
        }

        $noiseTypes = ['Perlin', 'Simplex', 'OpenSimplex2', 'Cellular', 'Value', 'Worley', 'Gradient', 'Cubic'];

        $config = [
            'baseHeight' => (int)$data[1],
            'waterLevel' => (int)$data[2],
            'minHeight' => (int)$data[3],
            'maxHeight' => (int)$data[4],

            'mainNoise' => [
                'type' => $noiseTypes[$data[6]],
                'amplitude' => (float)$data[7],
                'frequency' => (float)$data[8],
                'octaves' => (int)$data[9],
                'persistence' => (float)$data[10],
                'lacunarity' => (float)$data[11]
            ],

            'detailNoise' => [
                'enabled' => (bool)$data[13],
                'type' => $noiseTypes[$data[14]],
                'amplitude' => (float)$data[15],
                'frequency' => (float)$data[16]
            ],

            'ridgeNoise' => [
                'enabled' => (bool)$data[18],
                'amplitude' => (float)$data[19],
                'frequency' => (float)$data[20],
                'sharpness' => (float)$data[21]
            ],

            'domainWarp' => [
                'enabled' => (bool)$data[23],
                'strength' => (float)$data[24],
                'iterations' => (int)$data[25]
            ],

            'heightMultiplier' => (float)$data[27],
            'heightExponent' => (float)$data[28],

            'terraces' => [
                'enabled' => (bool)$data[30],
                'steps' => (int)$data[31],
                'strength' => (float)$data[32]
            ],

            'biomBlend' => [
                'enabled' => (bool)$data[34],
                'radius' => (float)$data[35],
                'frequency' => (float)$data[36]
            ],

            'blocks' => [
                'stone' => trim($data[38]),
                'dirt' => trim($data[39]),
                'surface' => trim($data[40]),
                'water' => trim($data[41]),
                'bedrock' => trim($data[42])
            ],

            'dirtDepth' => (int)$data[43],
            'generateBedrock' => (bool)$data[44]
        ];

        $this->plugin->getConfigManager()->saveGenerator($this->generatorName, $config);
        $player->sendMessage("§aGenerator '§e" . $this->generatorName . "§a' wurde gespeichert!");
        $player->sendForm(new MainMenuForm($this->plugin));
    }

    private function getNoiseTypeIndex(string $type): int {
        $types = ['Perlin', 'Simplex', 'OpenSimplex2', 'Cellular', 'Value', 'Worley', 'Gradient', 'Cubic'];
        $index = array_search($type, $types);
        return $index !== false ? $index : 0;
    }

    private function getDefaultConfig(): array {
        return [
            'baseHeight' => 64,
            'waterLevel' => 62,
            'minHeight' => 0,
            'maxHeight' => 256,
            'mainNoise' => [
                'type' => 'Perlin',
                'amplitude' => 50.0,
                'frequency' => 0.005,
                'octaves' => 6,
                'persistence' => 0.5,
                'lacunarity' => 2.0
            ],
            'detailNoise' => [
                'enabled' => true,
                'type' => 'Simplex',
                'amplitude' => 10.0,
                'frequency' => 0.05
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
                'strength' => 0.5
            ],
            'biomBlend' => [
                'enabled' => false,
                'radius' => 1000.0,
                'frequency' => 0.001
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
        ];
    }
}