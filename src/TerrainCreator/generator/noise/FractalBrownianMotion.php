<?php

declare(strict_types=1);

namespace TerrainCreator\generator\noise;

class FractalBrownianMotion {

    private $noise;
    private int $octaves;
    private float $frequency;
    private float $amplitude;
    private float $lacunarity;
    private float $persistence;
    private float $gain;

    public function __construct(
        $noise,
        int $octaves = 6,
        float $frequency = 0.01,
        float $amplitude = 1.0,
        float $lacunarity = 2.0,
        float $persistence = 0.5,
        float $gain = 2.0
    ) {
        $this->noise = $noise;
        $this->octaves = $octaves;
        $this->frequency = $frequency;
        $this->amplitude = $amplitude;
        $this->lacunarity = $lacunarity;
        $this->persistence = $persistence;
        $this->gain = $gain;
    }

    public function noise2D(float $x, float $y): float {
        $result = 0.0;
        $amplitude = $this->amplitude;
        $frequency = $this->frequency;
        $maxValue = 0.0;

        for ($i = 0; $i < $this->octaves; $i++) {
            $nx = $x * $frequency;
            $ny = $y * $frequency;

            $noiseValue = $this->noise->noise2D($nx, $ny);
            $result += $noiseValue * $amplitude;

            $maxValue += $amplitude;
            $amplitude *= $this->persistence;
            $frequency *= $this->lacunarity;
        }

        // Normalisiere
        if ($maxValue > 0) {
            $result /= $maxValue;
        }

        return $result;
    }

    public function noise3D(float $x, float $y, float $z): float {
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

        return $result;
    }

    /**
     * Turbulenz-Effekt - nimmt Absolutwert jeder Oktave
     */
    public function turbulence2D(float $x, float $y): float {
        $result = 0.0;
        $amplitude = $this->amplitude;
        $frequency = $this->frequency;
        $maxValue = 0.0;

        for ($i = 0; $i < $this->octaves; $i++) {
            $nx = $x * $frequency;
            $ny = $y * $frequency;

            $noiseValue = abs($this->noise->noise2D($nx, $ny));
            $result += $noiseValue * $amplitude;

            $maxValue += $amplitude;
            $amplitude *= $this->persistence;
            $frequency *= $this->lacunarity;
        }

        if ($maxValue > 0) {
            $result /= $maxValue;
        }

        return $result;
    }

    /**
     * Ridge Noise - für Bergkämme und scharfe Features
     */
    public function ridge2D(float $x, float $y, float $ridgeOffset = 1.0): float {
        $result = 0.0;
        $amplitude = $this->amplitude;
        $frequency = $this->frequency;
        $weight = 1.0;

        for ($i = 0; $i < $this->octaves; $i++) {
            $nx = $x * $frequency;
            $ny = $y * $frequency;

            $noiseValue = $ridgeOffset - abs($this->noise->noise2D($nx, $ny));
            $noiseValue *= $noiseValue; // Quadrieren für schärfere Kanten
            $noiseValue *= $weight;

            // Gewicht für nächste Oktave basierend auf aktuellem Wert
            $weight = $noiseValue * $this->gain;
            $weight = max(0.0, min(1.0, $weight));

            $result += $noiseValue * $amplitude;
            $amplitude *= $this->persistence;
            $frequency *= $this->lacunarity;
        }

        return $result;
    }

    /**
     * Billowy Noise - für Wolken-ähnliche Formationen
     */
    public function billowy2D(float $x, float $y): float {
        $result = 0.0;
        $amplitude = $this->amplitude;
        $frequency = $this->frequency;
        $maxValue = 0.0;

        for ($i = 0; $i < $this->octaves; $i++) {
            $nx = $x * $frequency;
            $ny = $y * $frequency;

            $noiseValue = 2.0 * abs($this->noise->noise2D($nx, $ny)) - 1.0;
            $result += $noiseValue * $amplitude;

            $maxValue += $amplitude;
            $amplitude *= $this->persistence;
            $frequency *= $this->lacunarity;
        }

        if ($maxValue > 0) {
            $result /= $maxValue;
        }

        return $result;
    }
}