<?php

declare(strict_types=1);

namespace TerrainCreator\generator\noise;

class NoiseCombiner {

    public const MODE_ADD = 'add';
    public const MODE_MULTIPLY = 'multiply';
    public const MODE_MIN = 'min';
    public const MODE_MAX = 'max';
    public const MODE_BLEND = 'blend';
    public const MODE_SUBTRACT = 'subtract';
    public const MODE_SCREEN = 'screen';
    public const MODE_OVERLAY = 'overlay';

    private array $noiseLayers = [];

    public function addLayer($noise, string $mode = self::MODE_ADD, float $weight = 1.0): void {
        $this->noiseLayers[] = [
            'noise' => $noise,
            'mode' => $mode,
            'weight' => $weight
        ];
    }

    public function noise2D(float $x, float $y): float {
        if (empty($this->noiseLayers)) {
            return 0.0;
        }

        $result = $this->noiseLayers[0]['noise']->noise2D($x, $y);

        for ($i = 1; $i < count($this->noiseLayers); $i++) {
            $layer = $this->noiseLayers[$i];
            $value = $layer['noise']->noise2D($x, $y);

            $result = $this->combine($result, $value, $layer['mode'], $layer['weight']);
        }

        return max(-1.0, min(1.0, $result));
    }

    public function noise3D(float $x, float $y, float $z): float {
        if (empty($this->noiseLayers)) {
            return 0.0;
        }

        $result = $this->noiseLayers[0]['noise']->noise3D($x, $y, $z);

        for ($i = 1; $i < count($this->noiseLayers); $i++) {
            $layer = $this->noiseLayers[$i];
            $value = $layer['noise']->noise3D($x, $y, $z);

            $result = $this->combine($result, $value, $layer['mode'], $layer['weight']);
        }

        return max(-1.0, min(1.0, $result));
    }

    private function combine(float $a, float $b, string $mode, float $weight): float {
        $b *= $weight;

        return match($mode) {
            self::MODE_ADD => $a + $b,
            self::MODE_MULTIPLY => $a * $b,
            self::MODE_MIN => min($a, $b),
            self::MODE_MAX => max($a, $b),
            self::MODE_BLEND => $a * (1.0 - abs($weight)) + $b * abs($weight),
            self::MODE_SUBTRACT => $a - $b,
            self::MODE_SCREEN => 1.0 - (1.0 - $a) * (1.0 - $b),
            self::MODE_OVERLAY => $a < 0.5
                ? 2.0 * $a * $b
                : 1.0 - 2.0 * (1.0 - $a) * (1.0 - $b),
            default => $a + $b
        };
    }

    /**
     * Verwendet ein Noise als Maske für ein anderes
     */
    public function maskedNoise2D(float $x, float $y, $mainNoise, $maskNoise, float $threshold = 0.0): float {
        $maskValue = $maskNoise->noise2D($x, $y);

        if ($maskValue < $threshold) {
            return 0.0;
        }

        $mainValue = $mainNoise->noise2D($x, $y);
        $blend = ($maskValue - $threshold) / (1.0 - $threshold);

        return $mainValue * $blend;
    }

    /**
     * Erstellt einen Gradient-basierten Noise-Mix
     */
    public function gradientBlend2D(
        float $x,
        float $y,
              $noise1,
              $noise2,
        float $centerX = 0.0,
        float $centerY = 0.0,
        float $radius = 1000.0
    ): float {
        $dx = $x - $centerX;
        $dy = $y - $centerY;
        $distance = sqrt($dx * $dx + $dy * $dy);

        $blend = min(1.0, $distance / $radius);

        $value1 = $noise1->noise2D($x, $y);
        $value2 = $noise2->noise2D($x, $y);

        return $value1 * (1.0 - $blend) + $value2 * $blend;
    }
}