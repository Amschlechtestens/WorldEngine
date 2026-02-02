<?php

declare(strict_types=1);

namespace TerrainCreator\generator\noise;

class DomainWarpNoise {

    private $baseNoise;
    private $warpNoiseX;
    private $warpNoiseY;
    private float $warpStrength;

    public function __construct($baseNoise, int $seed, float $warpStrength = 50.0) {
        $this->baseNoise = $baseNoise;
        $this->warpStrength = $warpStrength;

        // Separate Noise-Instanzen für X und Y Warping
        $this->warpNoiseX = new PerlinNoise($seed + 1000);
        $this->warpNoiseY = new PerlinNoise($seed + 2000);
    }

    public function noise2D(float $x, float $y): float {
        // Berechne Offset durch Warp-Noise
        $offsetX = $this->warpNoiseX->noise2D($x * 0.01, $y * 0.01) * $this->warpStrength;
        $offsetY = $this->warpNoiseY->noise2D($x * 0.01, $y * 0.01) * $this->warpStrength;

        // Wende Offset auf Original-Koordinaten an
        $warpedX = $x + $offsetX;
        $warpedY = $y + $offsetY;

        // Hole Noise-Wert an verzerrter Position
        return $this->baseNoise->noise2D($warpedX, $warpedY);
    }

    public function noise3D(float $x, float $y, float $z): float {
        $offsetX = $this->warpNoiseX->noise2D($x * 0.01, $y * 0.01) * $this->warpStrength;
        $offsetY = $this->warpNoiseY->noise2D($y * 0.01, $z * 0.01) * $this->warpStrength;

        $warpedX = $x + $offsetX;
        $warpedY = $y + $offsetY;

        return $this->baseNoise->noise3D($warpedX, $warpedY, $z);
    }

    /**
     * Multi-Octave Domain Warping für noch komplexere Effekte
     */
    public function noise2DMultiWarp(float $x, float $y, int $iterations = 2): float {
        $warpedX = $x;
        $warpedY = $y;

        for ($i = 0; $i < $iterations; $i++) {
            $scale = 0.01 * ($i + 1);
            $strength = $this->warpStrength / ($i + 1);

            $offsetX = $this->warpNoiseX->noise2D($warpedX * $scale, $warpedY * $scale) * $strength;
            $offsetY = $this->warpNoiseY->noise2D($warpedX * $scale, $warpedY * $scale) * $strength;

            $warpedX += $offsetX;
            $warpedY += $offsetY;
        }

        return $this->baseNoise->noise2D($warpedX, $warpedY);
    }
}