<?php

declare(strict_types=1);

namespace TerrainCreator\generator\noise;

class CubicNoise {

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

        // Cubic interpolation
        $sx = $this->cubicInterpolate($sx);
        $sy = $this->cubicInterpolate($sy);

        $n00 = $this->randomValue($x0, $y0);
        $n10 = $this->randomValue($x1, $y0);
        $n01 = $this->randomValue($x0, $y1);
        $n11 = $this->randomValue($x1, $y1);

        $nx0 = $n00 * (1 - $sx) + $n10 * $sx;
        $nx1 = $n01 * (1 - $sx) + $n11 * $sx;

        return $nx0 * (1 - $sy) + $nx1 * $sy;
    }

    public function noise3D(float $x, float $y, float $z): float {
        $x0 = (int)floor($x);
        $y0 = (int)floor($y);
        $z0 = (int)floor($z);

        $sx = $this->cubicInterpolate($x - $x0);
        $sy = $this->cubicInterpolate($y - $y0);
        $sz = $this->cubicInterpolate($z - $z0);

        // 8 Eckpunkte des Würfels
        $c000 = $this->randomValue3D($x0, $y0, $z0);
        $c100 = $this->randomValue3D($x0 + 1, $y0, $z0);
        $c010 = $this->randomValue3D($x0, $y0 + 1, $z0);
        $c110 = $this->randomValue3D($x0 + 1, $y0 + 1, $z0);
        $c001 = $this->randomValue3D($x0, $y0, $z0 + 1);
        $c101 = $this->randomValue3D($x0 + 1, $y0, $z0 + 1);
        $c011 = $this->randomValue3D($x0, $y0 + 1, $z0 + 1);
        $c111 = $this->randomValue3D($x0 + 1, $y0 + 1, $z0 + 1);

        // Trilinear interpolation
        $c00 = $c000 * (1 - $sx) + $c100 * $sx;
        $c10 = $c010 * (1 - $sx) + $c110 * $sx;
        $c01 = $c001 * (1 - $sx) + $c101 * $sx;
        $c11 = $c011 * (1 - $sx) + $c111 * $sx;

        $c0 = $c00 * (1 - $sy) + $c10 * $sy;
        $c1 = $c01 * (1 - $sy) + $c11 * $sy;

        return $c0 * (1 - $sz) + $c1 * $sz;
    }

    private function cubicInterpolate(float $t): float {
        return $t * $t * (3.0 - 2.0 * $t);
    }

    private function randomValue(int $x, int $y): float {
        $n = $x + $y * 57 + $this->seed * 131;
        $n = ($n << 13) ^ $n;
        $n = ($n * ($n * $n * 15731 + 789221) + 1376312589) & 0x7fffffff;
        return 1.0 - ($n / 1073741824.0);
    }

    private function randomValue3D(int $x, int $y, int $z): float {
        $n = $x + $y * 57 + $z * 997 + $this->seed * 131;
        $n = ($n << 13) ^ $n;
        $n = ($n * ($n * $n * 15731 + 789221) + 1376312589) & 0x7fffffff;
        return 1.0 - ($n / 1073741824.0);
    }
}