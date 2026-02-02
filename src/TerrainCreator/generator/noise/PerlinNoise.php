<?php

declare(strict_types=1);

namespace TerrainCreator\generator\noise;

class PerlinNoise {

    private array $perm = [];

    public function __construct(int $seed) {
        $this->initPermutation($seed);
    }

    private function initPermutation(int $seed): void {
        mt_srand($seed);
        $p = range(0, 255);
        shuffle($p);
        $this->perm = array_merge($p, $p);
    }

    public function noise2D(float $x, float $y): float {
        $X = (int)floor($x) & 255;
        $Y = (int)floor($y) & 255;
        
        $x -= floor($x);
        $y -= floor($y);
        
        $u = $this->fade($x);
        $v = $this->fade($y);
        
        $A = $this->perm[$X] + $Y;
        $B = $this->perm[$X + 1] + $Y;
        
        return $this->lerp($v,
            $this->lerp($u,
                $this->grad($this->perm[$A], $x, $y),
                $this->grad($this->perm[$B], $x - 1, $y)
            ),
            $this->lerp($u,
                $this->grad($this->perm[$A + 1], $x, $y - 1),
                $this->grad($this->perm[$B + 1], $x - 1, $y - 1)
            )
        );
    }

    public function noise3D(float $x, float $y, float $z): float {
        $X = (int)floor($x) & 255;
        $Y = (int)floor($y) & 255;
        $Z = (int)floor($z) & 255;
        
        $x -= floor($x);
        $y -= floor($y);
        $z -= floor($z);
        
        $u = $this->fade($x);
        $v = $this->fade($y);
        $w = $this->fade($z);
        
        $A = $this->perm[$X] + $Y;
        $AA = $this->perm[$A] + $Z;
        $AB = $this->perm[$A + 1] + $Z;
        $B = $this->perm[$X + 1] + $Y;
        $BA = $this->perm[$B] + $Z;
        $BB = $this->perm[$B + 1] + $Z;
        
        return $this->lerp($w,
            $this->lerp($v,
                $this->lerp($u,
                    $this->grad3d($this->perm[$AA], $x, $y, $z),
                    $this->grad3d($this->perm[$BA], $x - 1, $y, $z)
                ),
                $this->lerp($u,
                    $this->grad3d($this->perm[$AB], $x, $y - 1, $z),
                    $this->grad3d($this->perm[$BB], $x - 1, $y - 1, $z)
                )
            ),
            $this->lerp($v,
                $this->lerp($u,
                    $this->grad3d($this->perm[$AA + 1], $x, $y, $z - 1),
                    $this->grad3d($this->perm[$BA + 1], $x - 1, $y, $z - 1)
                ),
                $this->lerp($u,
                    $this->grad3d($this->perm[$AB + 1], $x, $y - 1, $z - 1),
                    $this->grad3d($this->perm[$BB + 1], $x - 1, $y - 1, $z - 1)
                )
            )
        );
    }

    private function fade(float $t): float {
        return $t * $t * $t * ($t * ($t * 6 - 15) + 10);
    }

    private function lerp(float $t, float $a, float $b): float {
        return $a + $t * ($b - $a);
    }

    private function grad(int $hash, float $x, float $y): float {
        $h = $hash & 3;
        $u = $h < 2 ? $x : $y;
        $v = $h < 2 ? $y : $x;
        return (($h & 1) == 0 ? $u : -$u) + (($h & 2) == 0 ? $v : -$v);
    }

    private function grad3d(int $hash, float $x, float $y, float $z): float {
        $h = $hash & 15;
        $u = $h < 8 ? $x : $y;
        $v = $h < 4 ? $y : ($h == 12 || $h == 14 ? $x : $z);
        return (($h & 1) == 0 ? $u : -$u) + (($h & 2) == 0 ? $v : -$v);
    }
}
