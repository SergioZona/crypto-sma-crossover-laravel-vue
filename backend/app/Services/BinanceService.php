<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Candle;
use App\Exceptions\BinanceApiException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class BinanceService
{
    private const BASE_URL = 'https://api.binance.com';

    /**
     * @return Candle[]
     */
    public function fetchKlines(string $symbol, string $interval, int $startTime, int $endTime): array
    {
        $cacheKey = "klines_{$symbol}_{$interval}_{$startTime}_{$endTime}";

        return Cache::remember($cacheKey, 600, function () use ($symbol, $interval, $startTime, $endTime) {
            $response = Http::get(self::BASE_URL . '/api/v3/klines', [
                'symbol' => $symbol,
                'interval' => $interval,
                'startTime' => $startTime,
                'endTime' => $endTime,
                'limit' => 1000
            ]);

            if ($response->failed()) {
                throw new BinanceApiException("Failed to fetch klines from Binance: " . $response->body());
            }


            $klines = $response->json();
            $candles = [];

            foreach ($klines as $kline) {
                $candles[] = new Candle(
                    open: (float) $kline[1],
                    high: (float) $kline[2],
                    low: (float) $kline[3],
                    close: (float) $kline[4],
                    timestamp: (int) $kline[0]
                );
            }

            return $candles;
        });
    }
}
