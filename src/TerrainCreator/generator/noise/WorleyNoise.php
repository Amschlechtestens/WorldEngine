<?php

declare(strict_types=1);

namespace TerrainCreator\generator\noise;

class WorleyNoise {

    private int $seed;
    private int $cellCount;

    public function __construct(int $seed, int $cellCount = 4) {
        $this->seed = $seed;
        $this->cellCount = $cellCount;
    }

    public function noise2D(float $x, float $y): float {
        $cellX = (int)floor($x);
        $cellY = (int)floor($y);

        $distances = [];

        // Überprüfe mehrere umliegende Zellen
        for ($i = -$this->cellCount; $i <= $this->cellCount; $i++) {
            for ($j = -$this->cellCount; $j <= $this->cellCount; $j++) {
                $cx = $cellX + $i;
                $cy = $cellY + $j;

                // Generiere mehrere Feature-Punkte pro Zelle
                $pointCount = $this->hashInt($cx, $cy, 0) % 3 + 1;

                for ($p = 0; $p < $pointCount; $p++) {
                    $fx = $cx + $this->hash($cx, $cy, $p * 2);
                    $fy = $cy + $this->hash($cx, $cy, $p * 2 + 1);

                    $dx = $x - $fx;
                    $dy = $y - $fy;
                    $dist = sqrt($dx * $dx + $dy * $dy);

                    $distances[] = $dist;
                }
            }
        }

        sort($distances);

        // F1 - Nächster Punkt
        $f1 = $distances[0] ?? 0.0;
        // F2 - Zweitnächster Punkt
        $f2 = $distances[1] ?? 0.0;

        // Verschiedene Kombinationen für unterschiedliche Effekte
        // F2 - F1 erzeugt zelluläre Muster mit klaren Grenzen
        $result = $f2 - $f1;

        // Normalisiere
        return max(-1.0, min(1.0, $result * 2.0));
    }

    public function noise3D(float $x, float $y, float $z): float {
        $cellX = (int)floor($x);
        $cellY = (int)floor($y);
        $cellZ = (int)floor($z);

        $distances = [];

        for ($i = -1; $i <= 1; $i++) {
            for ($j = -1; $j <= 1; $j++) {
                for ($k = -1; $k <= 1; $k++) {
                    $cx = $cellX + $i;
                    $cy = $cellY + $j;
                    $cz = $cellZ + $k;

                    $fx = $cx + $this->hash($cx, $cy, $cz);
                    $fy = $cy + $this->hash($cx, $cy, $cz + 1);
                    $fz = $cz + $this->hash($cx, $cy, $cz + 2);

                    $dx = $x - $fx;
                    $dy = $y - $fy;
                    $dz = $z - $fz;
                    $dist = sqrt($dx * $dx + $dy * $dy + $dz * $dz);

                    $distances[] = $dist;
                }
            }
        }

        sort($distances);
        $f1 = $distances[0] ?? 0.0;
        $f2 = $distances[1] ?? 0.0;

        return max(-1.0, min(1.0, ($f2 - $f1) * 2.0));
    }

    private function hash(int $x, int $y, int $offset = 0): float {
        $n = $x + $y * 57 + $offset * 131 + $this->seed;
        $n = ($n << 13) ^ $n;
        $n = fmod(($n * ($n * $n * 15731 + 789221) + 1376312589), 2147483647);
        return 1.0 - ($n / 1073741824.0);
    }


    private function hashInt(int $x, int $y, int $offset): int {
        $n = $x + $y * 57 + $offset * 131 + $this->seed;
        $n = ($n << 13) ^ $n;
        return abs($n);
    }
}