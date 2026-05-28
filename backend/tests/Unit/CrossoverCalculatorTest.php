<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\CrossoverCalculator;

class CrossoverCalculatorTest extends TestCase
{
    public function test_no_crossovers_calculated_with_insufficient_data(): void
    {
        $calculator = new CrossoverCalculator();
        $crossovers = $calculator->calculate([10.0, 10.5], [1700000000000, 1700001000000], 5, 10);
        $this->assertEmpty($crossovers);
    }

    public function test_crossovers_calculated_correctly_ascending_and_descending(): void
    {
        $calculator = new CrossoverCalculator();
        
        // Simulating data where short SMA goes above long SMA then below long SMA
        // Prices constructed to yield clear crosses
        $closes = [
            10, 11, 12, 13, 14, 15, 16, 17, 18, 19, // period of 10
            25, 30, 35, 40, // rise (short sma cross above)
            30, 20, 15, 10  // fall (short sma cross below)
        ];
        
        $timestamps = [];
        for ($i = 0; $i < count($closes); $i++) {
            $timestamps[] = (1700000000 + $i * 60) * 1000;
        }

        $crossovers = $calculator->calculate($closes, $timestamps, 3, 6);
        $this->assertNotEmpty($crossovers);
    }
}
