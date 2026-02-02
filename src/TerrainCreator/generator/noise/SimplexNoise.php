<?php

declare(strict_types=1);

namespace TerrainCreator\generator\noise;

class SimplexNoise {

    private array $perm = [];
    private const F2 = 0.366025403784439;
    private const G2 = 0.211324865405187;

    public function __construct(int $seed) {
        mt_srand($seed);
        $p = range(0, 255);
        shuffle($p);
        $this->perm = array_merge($p, $p);
    }

    public function noise2D(float $x, float $y): float {
        $s = ($x + $y) * self::F2;
        $i = (int)floor($x + $s);
        $j = (int)floor($y + $s);
        
        $t = ($i + $j) * self::G2;
        $X0 = $i - $t;
        $Y0 = $j - $t;
        $x0 = $x - $X0;
        $y0 = $y - $Y0;
        
        if ($x0 > $y0) {
            $i1 = 1;
            $j1 = 0;
        } else {
            $i1 = 0;
            $j1 = 1;
        }
        
        $x1 = $x0 - $i1 + self::G2;
        $y1 = $y0 - $j1 + self::G2;
        $x2 = $x0 - 1.0 + 2.0 * self::G2;
        $y2 = $y0 - 1.0 + 2.0 * self::G2;
        
        $ii = $i & 255;
        $jj = $j & 255;
        
        $n0 = $this->contribute($ii, $jj, $x0, $y0);
        $n1 = $this->contribute($ii + $i1, $jj + $j1, $x1, $y1);
        $n2 = $this->contribute($ii + 1, $jj + 1, $x2, $y2);
        
        return 70.0 * ($n0 + $n1 + $n2);
    }

    public function noise3D(float $x, float $y, float $z): float {
        // Vereinfachte 3D Version - nutzt 2D noise
        return $this->noise2D($x + $z, $y);
    }

    private function contribute(int $i, int $j, float $x, float $y): float {
        $t = 0.5 - $x * $x - $y * $y;
        if ($t < 0) {
            return 0.0;
        }
        $gi = $this->perm[$i + $this->perm[$j]] % 12;
        $t *= $t;
        return $t * $t * $this->dot2d($gi, $x, $y);
    }

    private function dot2d(int $gi, float $x, float $y): float {
        $grad = [
            [1, 1], [-1, 1], [1, -1], [-1, -1],
            [1, 0], [-1, 0], [1, 0], [-1, 0],
            [0, 1], [0, -1], [0, 1], [0, -1]
        ];
        $g = $grad[$gi % 12];
        return $g[0] * $x + $g[1] * $y;
    }
}
