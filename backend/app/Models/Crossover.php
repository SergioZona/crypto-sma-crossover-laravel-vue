<?php

declare(strict_types=1);

namespace App\Models;

class Crossover
{
    public function __construct(
        public readonly string $time,
        public readonly string $type,
        public readonly float $shortSmaValue,
        public readonly float $longSmaValue,
        public readonly int $timestamp
    ) {}
}
