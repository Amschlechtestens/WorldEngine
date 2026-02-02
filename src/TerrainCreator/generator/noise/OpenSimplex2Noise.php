<?php

declare(strict_types=1);

namespace TerrainCreator\generator\noise;

class OpenSimplex2Noise {

    private SimplexNoise $simplex;

    public function __construct(int $seed) {
        // OpenSimplex2 ist eine Verbesserung von Simplex
        // Für diese Implementierung nutzen wir eine modifizierte Simplex-Version
        $this->simplex = new SimplexNoise($seed);
    }

    public function noise2D(float $x, float $y): float {
        // OpenSimplex2 mit zusätzlicher Rotation für bessere visuelle Qualität
        $s = ($x + $y) * 0.366025403784439;
        $xs = $x + $s;
        $ys = $y + $s;
        
        return $this->simplex->noise2D($xs, $ys);
    }

    public function noise3D(float $x, float $y, float $z): float {
        return $this->simplex->noise3D($x, $y, $z);
    }
}
