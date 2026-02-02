<?php

declare(strict_types=1);

namespace TerrainCreator\generator\noise;

class NoiseFactory {

    /**
     * Erstellt eine Noise-Instanz basierend auf dem Typ
     */
    public static function createNoise(string $type, int $seed, array $options = []) {
        return match($type) {
            'Perlin' => new PerlinNoise($seed),
            'Simplex' => new SimplexNoise($seed),
            'OpenSimplex2' => new OpenSimplex2Noise($seed),
            'Cellular' => new CellularNoise($seed),
            'Value' => new ValueNoise($seed),
            'Worley' => new WorleyNoise($seed, $options['cellCount'] ?? 4),
            'Gradient' => new GradientNoise($seed),
            'Cubic' => new CubicNoise($seed),
            default => new PerlinNoise($seed)
        };
    }

    /**
     * Erstellt Fractal Brownian Motion Noise
     */
    public static function createFBM(
        string $baseType,
        int $seed,
        int $octaves = 6,
        float $frequency = 0.01,
        float $amplitude = 1.0,
        float $lacunarity = 2.0,
        float $persistence = 0.5
    ): FractalBrownianMotion {
        $baseNoise = self::createNoise($baseType, $seed);
        return new FractalBrownianMotion(
            $baseNoise,
            $octaves,
            $frequency,
            $amplitude,
            $lacunarity,
            $persistence
        );
    }

    /**
     * Erstellt Domain Warp Noise
     */
    public static function createDomainWarp(
        string $baseType,
        int $seed,
        float $warpStrength = 50.0
    ): DomainWarpNoise {
        $baseNoise = self::createNoise($baseType, $seed);
        return new DomainWarpNoise($baseNoise, $seed, $warpStrength);
    }

    /**
     * Erstellt einen Noise Combiner mit vordefinierten Layern
     */
    public static function createCombiner(array $layers, int $seed): NoiseCombiner {
        $combiner = new NoiseCombiner();

        foreach ($layers as $layer) {
            $noise = self::createNoise($layer['type'], $seed + $layer['seedOffset']);
            $combiner->addLayer(
                $noise,
                $layer['mode'] ?? NoiseCombiner::MODE_ADD,
                $layer['weight'] ?? 1.0
            );
        }

        return $combiner;
    }

    /**
     * Gibt alle verfügbaren Noise-Typen zurück
     */
    public static function getAvailableTypes(): array {
        return [
            'Perlin' => 'Classic Perlin Noise - Natürlich, organisch',
            'Simplex' => 'Simplex Noise - Schneller, weniger Artefakte',
            'OpenSimplex2' => 'OpenSimplex2 - Verbesserte Version von Simplex',
            'Cellular' => 'Cellular/Voronoi - Zelluläre Strukturen',
            'Value' => 'Value Noise - Einfach, blocky',
            'Worley' => 'Worley Noise - Organische Zellen, Steine',
            'Gradient' => 'Gradient Noise - Glatte Übergänge',
            'Cubic' => 'Cubic Noise - Kubische Interpolation'
        ];
    }

    /**
     * Gibt Preset-Konfigurationen für verschiedene Terrain-Typen zurück
     */
    public static function getPresets(): array {
        return [
            'mountains' => [
                'type' => 'Perlin',
                'octaves' => 8,
                'frequency' => 0.005,
                'amplitude' => 80.0,
                'lacunarity' => 2.2,
                'persistence' => 0.6
            ],
            'hills' => [
                'type' => 'Simplex',
                'octaves' => 6,
                'frequency' => 0.01,
                'amplitude' => 40.0,
                'lacunarity' => 2.0,
                'persistence' => 0.5
            ],
            'plains' => [
                'type' => 'Value',
                'octaves' => 4,
                'frequency' => 0.02,
                'amplitude' => 10.0,
                'lacunarity' => 2.0,
                'persistence' => 0.4
            ],
            'canyon' => [
                'type' => 'Worley',
                'octaves' => 5,
                'frequency' => 0.008,
                'amplitude' => 60.0,
                'lacunarity' => 2.5,
                'persistence' => 0.55
            ],
            'islands' => [
                'type' => 'Gradient',
                'octaves' => 6,
                'frequency' => 0.012,
                'amplitude' => 50.0,
                'lacunarity' => 2.0,
                'persistence' => 0.5
            ]
        ];
    }
}