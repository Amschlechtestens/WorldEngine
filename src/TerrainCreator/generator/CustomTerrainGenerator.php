<?php

declare(strict_types=1);

namespace TerrainCreator\generator;

use pocketmine\block\VanillaBlocks;
use pocketmine\world\ChunkManager;
use pocketmine\world\generator\Generator;
use TerrainCreator\generator\noise\NoiseFactory;
use TerrainCreator\generator\noise\DomainWarpNoise;
use TerrainCreator\generator\noise\FractalBrownianMotion;

class CustomTerrainGenerator extends Generator {

    private array $config;
    private string $generatorName;
    private NoiseGenerator $mainNoise;
    private ?NoiseGenerator $detailNoise = null;
    private ?NoiseGenerator $ridgeNoise = null;
    private ?DomainWarpNoise $domainWarp = null;
    private ?NoiseGenerator $biomBlendNoise = null;
    private array $blocks = [];

    public function __construct(int $seed, string $preset) {
        parent::__construct($seed, $preset);

        $presetData = json_decode($preset, true);
        if ($presetData === null) {
            throw new \RuntimeException("Invalid generator preset");
        }

        $this->generatorName = $presetData['generatorName'] ?? 'Unknown';
        $this->config = $presetData['config'] ?? [];

        $this->initializeNoise($seed);
        $this->initializeBlocks();
    }

    private function initializeNoise(int $seed): void {
        // Hauptnoise mit allen Parametern initialisieren
        $mainConfig = $this->config['mainNoise'];

        $baseNoise = NoiseFactory::createNoise($mainConfig['type'], $seed);

        // Erstelle FBM für Haupt-Noise
        $fbm = new FractalBrownianMotion(
            $baseNoise,
            $mainConfig['octaves'],
            $mainConfig['frequency'],
            $mainConfig['amplitude'],
            $mainConfig['lacunarity'],
            $mainConfig['persistence']
        );

        // Wenn Domain Warping aktiviert ist, wrappen wir das FBM
        if ($this->config['domainWarp']['enabled']) {
            $this->mainNoise = new NoiseGenerator(
                new DomainWarpNoise(
                    $fbm,
                    $seed,
                    $this->config['domainWarp']['strength']
                ),
                $mainConfig['amplitude'],
                $mainConfig['frequency'],
                1, // Octaves werden bereits in FBM behandelt
                0.5,
                2.0
            );
        } else {
            $this->mainNoise = new NoiseGenerator(
                $fbm,
                $mainConfig['amplitude'],
                $mainConfig['frequency'],
                1,
                0.5,
                2.0
            );
        }

        // Detail Noise mit eigenem FBM
        if ($this->config['detailNoise']['enabled']) {
            $detailConfig = $this->config['detailNoise'];
            $detailBase = NoiseFactory::createNoise($detailConfig['type'], $seed + 1000);

            $this->detailNoise = new NoiseGenerator(
                $detailBase,
                $detailConfig['amplitude'],
                $detailConfig['frequency'],
                1,
                0.5,
                2.0
            );
        }

        // Ridge Noise für Bergkämme
        if ($this->config['ridgeNoise']['enabled']) {
            $ridgeConfig = $this->config['ridgeNoise'];
            $ridgeBase = NoiseFactory::createNoise('Perlin', $seed + 2000);

            $ridgeFBM = new FractalBrownianMotion(
                $ridgeBase,
                4, // Weniger Oktaven für scharfe Features
                $ridgeConfig['frequency'],
                $ridgeConfig['amplitude'],
                2.5,
                0.6
            );

            $this->ridgeNoise = new NoiseGenerator(
                $ridgeFBM,
                $ridgeConfig['amplitude'],
                $ridgeConfig['frequency'],
                1,
                0.5,
                2.0
            );
        }

        // Biom-Blending Noise
        if ($this->config['biomBlend']['enabled']) {
            $this->biomBlendNoise = NoiseFactory::createNoise('Simplex', $seed + 3000);
        }
    }

    private function initializeBlocks(): void {
        $blockNames = $this->config['blocks'];

        $this->blocks = [
            'stone' => $this->getBlockFromString($blockNames['stone']),
            'dirt' => $this->getBlockFromString($blockNames['dirt']),
            'surface' => $this->getBlockFromString($blockNames['surface']),
            'water' => $this->getBlockFromString($blockNames['water']),
            'bedrock' => $this->getBlockFromString($blockNames['bedrock'])
        ];
    }

    private function getBlockFromString(string $blockName) {
        try {
            $method = str_replace('_', '', ucwords($blockName, '_'));
            if (method_exists(VanillaBlocks::class, $method)) {
                return VanillaBlocks::$method();
            }
        } catch (\Exception $e) {
        }

        return VanillaBlocks::STONE();
    }

    public function generateChunk(ChunkManager $world, int $chunkX, int $chunkZ): void {
        $chunk = $world->getChunk($chunkX, $chunkZ);
        if ($chunk === null) {
            return;
        }

        $baseHeight = $this->config['baseHeight'];
        $waterLevel = $this->config['waterLevel'];
        $dirtDepth = $this->config['dirtDepth'];

        for ($x = 0; $x < 16; $x++) {
            for ($z = 0; $z < 16; $z++) {
                $worldX = ($chunkX << 4) + $x;
                $worldZ = ($chunkZ << 4) + $z;

                // Berechne Höhe mit allen Noise-Layern
                $height = $this->calculateHeight($worldX, $worldZ, $baseHeight);

                // Setze Blöcke
                $this->generateColumn($chunk, $x, $z, (int)$height, $waterLevel, $dirtDepth);
            }
        }
    }

    private function calculateHeight(int $x, int $z, int $baseHeight): float {
        // Haupt-Noise-Wert
        $height = $this->mainNoise->getNoise($x, $z);

        // Detail Noise hinzufügen
        if ($this->detailNoise !== null) {
            $detailValue = $this->detailNoise->getNoise($x, $z);
            $height += $detailValue;
        }

        // Ridge Noise für Bergkämme
        if ($this->ridgeNoise !== null) {
            $ridgeValue = $this->ridgeNoise->getNoise($x, $z);
            $sharpness = $this->config['ridgeNoise']['sharpness'];

            // Invertieren und schärfen
            $ridgeValue = 1.0 - abs($ridgeValue);
            $ridgeValue = pow($ridgeValue, $sharpness);

            $height += $ridgeValue * $this->config['ridgeNoise']['amplitude'];
        }

        // Biom-Blending für Variation
        if ($this->biomBlendNoise !== null && $this->config['biomBlend']['enabled']) {
            $blendFreq = $this->config['biomBlend']['frequency'];
            $blendValue = $this->biomBlendNoise->noise2D($x * $blendFreq, $z * $blendFreq);

            // Modifiziere Höhe basierend auf Biom
            $biomModifier = $blendValue * 20.0;
            $height += $biomModifier;
        }

        // Basis-Höhe hinzufügen
        $height += $baseHeight;

        // Höhen-Exponent für Steilheit
        $exponent = $this->config['heightExponent'];
        if ($exponent !== 1.0) {
            $normalized = ($height - $baseHeight) / 100.0;
            $normalized = $normalized < 0 ?
                -pow(abs($normalized), $exponent) :
                pow($normalized, $exponent);
            $height = $baseHeight + ($normalized * 100.0);
        }

        // Höhen-Multiplikator
        $height *= $this->config['heightMultiplier'];

        // Terrassen-Effekt (Mesa-Style)
        if ($this->config['terraces']['enabled']) {
            $height = $this->applyTerraces($height);
        }

        // Clamp zu Min/Max
        $height = max($this->config['minHeight'], min($this->config['maxHeight'], $height));

        return $height;
    }

    private function applyTerraces(float $height): float {
        $steps = $this->config['terraces']['steps'];
        $strength = $this->config['terraces']['strength'];

        $stepHeight = 256.0 / $steps;
        $terraceHeight = round($height / $stepHeight) * $stepHeight;

        return $height * (1.0 - $strength) + $terraceHeight * $strength;
    }

    private function generateColumn($chunk, int $x, int $z, int $height, int $waterLevel, int $dirtDepth): void {
        $minY = -64;
        $maxY = 320;

        for ($y = $minY; $y < $maxY; $y++) {
            if ($y === $minY && $this->config['generateBedrock']) {
                // Bedrock
                $chunk->setBlockStateId($x, $y, $z, $this->blocks['bedrock']->getStateId());
            } elseif ($y < $height - $dirtDepth) {
                // Stein
                $chunk->setBlockStateId($x, $y, $z, $this->blocks['stone']->getStateId());
            } elseif ($y < $height) {
                // Erde
                $chunk->setBlockStateId($x, $y, $z, $this->blocks['dirt']->getStateId());
            } elseif ($y === $height) {
                // Oberfläche
                if ($y < $waterLevel) {
                    $chunk->setBlockStateId($x, $y, $z, $this->blocks['dirt']->getStateId());
                } else {
                    $chunk->setBlockStateId($x, $y, $z, $this->blocks['surface']->getStateId());
                }
            } elseif ($y <= $waterLevel) {
                // Wasser
                $chunk->setBlockStateId($x, $y, $z, $this->blocks['water']->getStateId());
            }
        }
    }

    public function populateChunk(ChunkManager $world, int $chunkX, int $chunkZ): void {
        // Populatoren können hier hinzugefügt werden
    }
}