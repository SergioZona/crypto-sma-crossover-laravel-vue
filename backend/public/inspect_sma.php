<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

try {
    $binance = $app->make(App\Services\BinanceService::class);
    $calculator = $app->make(App\Services\CrossoverCalculator::class);

    $from = '2024-10-20 00:00:00';
    $to = '2024-10-27 00:00:00';
    $startTime = strtotime($from) * 1000;
    $endTime = strtotime($to) * 1000;
    $longPeriod = 200;
    $intervalSeconds = 1800; // 30m

    $bufferStartTime = $startTime - ($longPeriod * $intervalSeconds * 1000);
    $candles = $binance->fetchKlines('BTCUSDT', '30m', $bufferStartTime, $endTime);

    $closes = [];
    $timestamps = [];
    foreach ($candles as $candle) {
        $closes[] = $candle->close;
        $timestamps[] = $candle->timestamp;
    }

    $detailed = $calculator->calculateDetailed($closes, $timestamps, 50, 200);
    $crossovers = $detailed['crossovers'];

    $startTimeSec = (int) ($startTime / 1000);

    echo "Crossovers before filtering: " . count($crossovers) . "\n";
    foreach ($crossovers as $cross) {
        $inRange = $cross->timestamp >= $startTimeSec ? 'YES' : 'NO';
        echo "Time: {$cross->time} | TS: {$cross->timestamp} | startTimeSec: {$startTimeSec} | InRange: {$inRange}\n";
    }

    // Now filter:
    $filtered = array_values(array_filter(
        $crossovers,
        fn($cross) => $cross->timestamp >= $startTimeSec
    ));
    echo "Crossovers after filtering: " . count($filtered) . "\n";

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
