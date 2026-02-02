<?php

declare(strict_types=1);

namespace TerrainCreator\generator\noise;

class GradientNoise {

    private int $seed;
    private array $gradients = [];

    public function __construct(int $seed) {
        $this->seed = $seed;
        $this->initGradients();
    }

    private function initGradients(): void {
        mt_srand($this->seed);
        for ($i = 0; $i < 256; $i++) {
            $angle = mt_rand() / mt_getrandmax() * 2.0 * M_PI;
            $this->gradients[] = [cos($angle), sin($angle)];
        }
    }

    public function noise2D(float $x, float $y): float {
        $x0 = (int)floor($x);
        $y0 = (int)floor($y);
        $x1 = $x0 + 1;
        $y1 = $y0 + 1;

        $sx = $x - $x0;
        $sy = $y - $y0;

        $n00 = $this->dotGridGradient($x0, $y0, $x, $y);
        $n10 = $this->dotGridGradient($x1, $y0, $x, $y);
        $n01 = $this->dotGridGradient($x0, $y1, $x, $y);
        $n11 = $this->dotGridGradient($x1, $y1, $x, $y);

        $sx = $this->smootherstep($sx);
        $sy = $this->smootherstep($sy);

        $nx0 = $this->lerp($n00, $n10, $sx);
        $nx1 = $this->lerp($n01, $n11, $sx);

        return $this->lerp($nx0, $nx1, $sy);
    }

    public function noise3D(float $x, float $y, float $z): float {
        // Vereinfachte 3D-Version
        return $this->noise2D($x + $z * 0.5, $y + $z * 0.5);
    }

    private function dotGridGradient(int $ix, int $iy, float $x, float $y): float {
        $hash = ($ix * 374761393 + $iy * 668265263 + $this->seed) & 255;
        $gradient = $this->gradients[$hash];

        $dx = $x - (float)$ix;
        $dy = $y - (float)$iy;

        return $dx * $gradient[0] + $dy * $gradient[1];
    }

    private function lerp(float $a, float $b, float $t): float {
        return $a + $t * ($b - $a);
    }

    private function smootherstep(float $t): float {
        return $t * $t * $t * ($t * ($t * 6.0 - 15.0) + 10.0);
    }
}