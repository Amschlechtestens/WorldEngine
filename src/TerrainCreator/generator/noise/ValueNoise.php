<?php

declare(strict_types=1);

namespace TerrainCreator\generator\noise;

class ValueNoise {

    private int $seed;

    public function __construct(int $seed) {
        $this->seed = $seed;
    }

    public function noise2D(float $x, float $y): float {
        $x0 = (int)floor($x);
        $y0 = (int)floor($y);
        $x1 = $x0 + 1;
        $y1 = $y0 + 1;
        
        $sx = $x - $x0;
        $sy = $y - $y0;
        
        // Smooth interpolation
        $sx = $sx * $sx * (3.0 - 2.0 * $sx);
        $sy = $sy * $sy * (3.0 - 2.0 * $sy);
        
        $n0 = $this->randomValue($x0, $y0);
        $n1 = $this->randomValue($x1, $y0);
        $n2 = $this->randomValue($x0, $y1);
        $n3 = $this->randomValue($x1, $y1);
        
        $nx0 = $n0 * (1 - $sx) + $n1 * $sx;
        $nx1 = $n2 * (1 - $sx) + $n3 * $sx;
        
        return $nx0 * (1 - $sy) + $nx1 * $sy;
    }

    public function noise3D(float $x, float $y, float $z): float {
        return $this->noise2D($x + $z * 0.5, $y + $z * 0.5);
    }

    private function randomValue(int $x, int $y): float {
        $n = $x + $y * 57 + $this->seed * 131;
        $n = ($n << 13) ^ $n;
        $n = ($n * ($n * $n * 15731 + 789221) + 1376312589) & 0x7fffffff;
        return 1.0 - ($n / 1073741824.0);
    }
}
