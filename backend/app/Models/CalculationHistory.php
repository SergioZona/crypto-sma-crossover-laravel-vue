<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CalculationHistory extends Model
{
    protected $table = 'calculation_history';

    protected $fillable = [
        'symbol',
        'interval',
        'from_date',
        'to_date',
        'short_period',
        'long_period',
        'crossover_count',
        'crossovers',
    ];

    protected $casts = [
        'crossovers' => 'array',
    ];
}
