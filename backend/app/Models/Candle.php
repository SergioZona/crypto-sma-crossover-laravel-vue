<?php

declare(strict_types=1);

namespace App\Models;

class Candle
{
    public function __construct(
        public readonly float $open,
        public readonly float $high,
        public readonly float $low,
        public readonly float $close,
        public readonly int $timestamp
    ) {}
}
