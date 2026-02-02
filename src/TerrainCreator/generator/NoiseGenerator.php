<?php

declare(strict_types=1);

namespace TerrainCreator\generator;

class NoiseGenerator {

    private $noise;
    private float $amplitude;
    private float $frequency;
    private int $octaves;
    private float $persistence;
    private float $lacunarity;

    public function __construct(
        $noise,
        float $amplitude,
        float $frequency,
        int $octaves = 1,
        float $persistence = 0.5,
        float $lacunarity = 2.0
    ) {
        $this->noise = $noise;
        $this->amplitude = $amplitude;
        $this->frequency = $frequency;
        $this->octaves = $octaves;
        $this->persistence = $persistence;
        $this->lacunarity = $lacunarity;
    }

    public function getNoise(int $x, int $z): float {
        $result = 0.0;
        $amplitude = $this->amplitude;
        $frequency = $this->frequency;
        $maxValue = 0.0;

        for ($i = 0; $i < $this->octaves; $i++) {
            $nx = $x * $frequency;
            $nz = $z * $frequency;
            
            $noiseValue = $this->noise->noise2D($nx, $nz);
            $result += $noiseValue * $amplitude;
            
            $maxValue += $amplitude;
            $amplitude *= $this->persistence;
            $frequency *= $this->lacunarity;
        }

        // Normalisiere das Ergebnis
        if ($maxValue > 0) {
            $result /= $maxValue;
        }

        return $result * $this->amplitude;
    }

    public function getNoise3D(int $x, int $y, int $z): float {
        $result = 0.0;
        $amplitude = $this->amplitude;
        $frequency = $this->frequency;
        $maxValue = 0.0;

        for ($i = 0; $i < $this->octaves; $i++) {
            $nx = $x * $frequency;
            $ny = $y * $frequency;
            $nz = $z * $frequency;
            
            $noiseValue = $this->noise->noise3D($nx, $ny, $nz);
            $result += $noiseValue * $amplitude;
            
            $maxValue += $amplitude;
            $amplitude *= $this->persistence;
            $frequency *= $this->lacunarity;
        }

        if ($maxValue > 0) {
            $result /= $maxValue;
        }

        return $result * $this->amplitude;
    }
}
