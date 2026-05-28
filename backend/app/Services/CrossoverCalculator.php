<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Crossover;

class CrossoverCalculator
{
    /**
     * @param float[] $closes
     * @param int[] $timestamps
     * @return Crossover[]
     */
    public function calculate(array $closes, array $timestamps, int $shortPeriod, int $longPeriod): array
    {
        return $this->calculateDetailed($closes, $timestamps, $shortPeriod, $longPeriod)['crossovers'];
    }

    /**
     * @param float[] $closes
     * @param int[] $timestamps
     * @return array{crossovers: Crossover[], short_smas: array<int, float>, long_smas: array<int, float>, first_crossover: ?Crossover, second_crossover: ?Crossover}
     */
    public function calculateDetailed(array $closes, array $timestamps, int $shortPeriod, int $longPeriod): array
    {
        $count = count($closes);
        if ($count < $longPeriod) {
            return [
                'crossovers' => [],
                'short_smas' => [],
                'long_smas' => [],
                'first_crossover' => null,
                'second_crossover' => null,
            ];
        }

        $shortSmas = $this->computeSma($closes, $shortPeriod);
        $longSmas = $this->computeSma($closes, $longPeriod);

        $crossovers = [];
        $startIndex = $longPeriod; // We need at least one point before to detect crossover

        for ($i = $startIndex; $i < $count; $i++) {
            if (!isset($shortSmas[$i - 1], $longSmas[$i - 1], $shortSmas[$i], $longSmas[$i])) {
                continue;
            }

            $prevShort = $shortSmas[$i - 1];
            $prevLong = $longSmas[$i - 1];
            $currShort = $shortSmas[$i];
            $currLong = $longSmas[$i];

            // Ascending cross: short crosses above long
            if ($prevShort <= $prevLong && $currShort > $currLong) {
                $crossovers[] = new Crossover(
                    date('Y-m-d H:i', (int) ($timestamps[$i] / 1000)),
                    'Ascendente',
                    $currShort,
                    $currLong,
                    (int) ($timestamps[$i] / 1000)
                );
            }
            // Descending cross: short crosses below long
            elseif ($prevShort >= $prevLong && $currShort < $currLong) {
                $crossovers[] = new Crossover(
                    date('Y-m-d H:i', (int) ($timestamps[$i] / 1000)),
                    'Descendente',
                    $currShort,
                    $currLong,
                    (int) ($timestamps[$i] / 1000)
                );
            }
        }

        return [
            'crossovers' => $crossovers,
            'short_smas' => $shortSmas,
            'long_smas' => $longSmas,
            'first_crossover' => $crossovers[0] ?? null,
            'second_crossover' => $crossovers[1] ?? null,
        ];
    }

    /**
     * Compute Simple Moving Average using sliding window
     * @param float[] $closes
     * @return array<int, float>
     */
    private function computeSma(array $closes, int $period): array
    {
        $count = count($closes);
        if ($count < $period) {
            return [];
        }

        $smas = [];
        $runningSum = 0.0;

        for ($i = 0; $i < $count; $i++) {
            $runningSum += $closes[$i];
            if ($i >= $period) {
                $runningSum -= $closes[$i - $period];
            }
            if ($i >= $period - 1) {
                $smas[$i] = $runningSum / $period;
            }
        }

        return $smas;
    }
}
