<?php

declare(strict_types=1);

namespace TerrainCreator\generator\noise;

class CellularNoise {

    private int $seed;

    public function __construct(int $seed) {
        $this->seed = $seed;
    }

    public function noise2D(float $x, float $y): float {
        $cellX = (int)floor($x);
        $cellY = (int)floor($y);
        
        $minDist = PHP_FLOAT_MAX;
        
        // Überprüfe 9 umliegende Zellen
        for ($i = -1; $i <= 1; $i++) {
            for ($j = -1; $j <= 1; $j++) {
                $cx = $cellX + $i;
                $cy = $cellY + $j;
                
                // Generiere Feature-Punkt für diese Zelle
                $fx = $cx + $this->hash($cx, $cy, 0);
                $fy = $cy + $this->hash($cx, $cy, 1);
                
                // Berechne Distanz
                $dx = $x - $fx;
                $dy = $y - $fy;
                $dist = sqrt($dx * $dx + $dy * $dy);
                
                if ($dist < $minDist) {
                    $minDist = $dist;
                }
            }
        }
        
        // Normalisiere auf -1 bis 1
        return 1.0 - min($minDist * 2.0, 1.0) * 2.0;
    }

    public function noise3D(float $x, float $y, float $z): float {
        return $this->noise2D($x + $z, $y);
    }

    private function hash(int $x, int $y, int $offset): float {
        $n = $x + $y * 57 + $offset * 131 + $this->seed;
        $n = ($n << 13) ^ $n;
        $n = $n & 0x7fffffff;
        $n = (int)(($n * ($n * $n * 15731 + 789221) + 1376312589) & 0x7fffffff);
        return $n / 2147483648.0;
    }

}
