<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\CalculationHistory;
use App\Models\Crossover;
use App\Services\BinanceService;
use App\Services\CrossoverCalculator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class CrossoverApiController extends Controller
{
    /** Interval label → seconds map */
    private const INTERVAL_SECONDS = [
        '1m'  => 60,
        '3m'  => 180,
        '5m'  => 300,
        '15m' => 900,
        '30m' => 1800,
        '1h'  => 3600,
        '2h'  => 7200,
        '4h'  => 14400,
        '6h'  => 21600,
        '8h'  => 28800,
        '12h' => 43200,
        '1d'  => 86400,
        '3d'  => 259200,
        '1w'  => 604800,
    ];

    public function __construct(
        private readonly BinanceService $binanceService,
        private readonly CrossoverCalculator $calculator
    ) {}

    // -------------------------------------------------------------------------
    // Public endpoints
    // -------------------------------------------------------------------------

    public function calculate(Request $request): JsonResponse
    {
        $authError = $this->checkAuth($request);
        if ($authError !== null) {
            return $authError;
        }

        $validator = $this->buildValidator($request);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'data'   => $validator->errors()->toArray(),
            ], 400);
        }

        try {
            return $this->runCalculation($request);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
                'code'    => 500,
            ], 500);
        }
    }

    public function history(Request $request): JsonResponse
    {
        $authError = $this->checkAuth($request);
        if ($authError !== null) {
            return $authError;
        }

        $history = CalculationHistory::orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return response()->json(['status' => 'success', 'data' => $history]);
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    private function checkAuth(Request $request): ?JsonResponse
    {
        $pass = $request->header('X-App-Password') ?? $request->query('password');
        if ($pass !== config('app.app_password')) {
            return response()->json([
                'status' => 'fail',
                'data'   => ['auth' => 'Invalid credentials'],
            ], 401);
        }

        return null;
    }

    private function buildValidator(Request $request): \Illuminate\Validation\Validator
    {
        return Validator::make($request->all(), [
            'symbol'       => 'required|string|in:BTCUSDT,ETHUSDT,XRPUSDT',
            'interval'     => 'required|string|in:1m,3m,5m,15m,30m,1h,2h,4h,6h,8h,12h,1d,3d,1w',
            'from'         => 'required|date_format:Y-m-d H:i:s',
            'to'           => 'required|date_format:Y-m-d H:i:s|after:from',
            'short_period' => 'required|integer|min:2',
            'long_period'  => 'required|integer|gt:short_period',
        ]);
    }

    private function runCalculation(Request $request): JsonResponse
    {
        $params = $this->extractParams($request);
        $cacheKey = $this->buildCacheKey($params);

        $cached = Cache::get($cacheKey);
        if ($cached !== null) {
            return response()->json(['status' => 'success', 'data' => $cached]);
        }

        $responseData = $this->computeResponseData($params);
        Cache::put($cacheKey, $responseData, 3600);

        return response()->json(['status' => 'success', 'data' => $responseData]);
    }

    /** @return array<string,mixed> */
    private function extractParams(Request $request): array
    {
        return [
            'symbol'       => (string) $request->input('symbol'),
            'interval'     => (string) $request->input('interval'),
            'from'         => (string) $request->input('from'),
            'to'           => (string) $request->input('to'),
            'short_period' => (int) $request->input('short_period'),
            'long_period'  => (int) $request->input('long_period'),
        ];
    }

    private function buildCacheKey(array $params): string
    {
        return 'crossover_calc_res:' . hash('sha256', (string) json_encode($params));
    }

    /** @return array<string,mixed> */
    private function computeResponseData(array $params): array
    {
        $symbol      = $params['symbol'];
        $interval    = $params['interval'];
        $from        = $params['from'];
        $to          = $params['to'];
        $shortPeriod = $params['short_period'];
        $longPeriod  = $params['long_period'];

        $startTime = strtotime($from) * 1000;
        $endTime   = strtotime($to)   * 1000;

        $intervalSeconds = self::INTERVAL_SECONDS[$interval] ?? 1800;
        $bufferStartTime = $startTime - ($longPeriod * $intervalSeconds * 1000);

        $candles = $this->binanceService->fetchKlines($symbol, $interval, $bufferStartTime, $endTime);

        $closes     = array_map(fn($c) => $c->close,     $candles);
        $timestamps = array_map(fn($c) => $c->timestamp, $candles);

        $detailed  = $this->calculator->calculateDetailed($closes, $timestamps, $shortPeriod, $longPeriod);
        $shortSmas = $detailed['short_smas'];
        $longSmas  = $detailed['long_smas'];

        $startTimeSec = (int) ($startTime / 1000);

        [$mappedCrossovers, $crossoversCount, $firstCrossover, $secondCrossover] =
            $this->resolveCrossovers($params, $detailed, $startTimeSec);

        $chartData = $this->buildChartData($candles, $shortSmas, $longSmas, $startTimeSec);

        return [
            'crossovers_count' => $crossoversCount,
            'crossovers'       => $mappedCrossovers,
            'first_crossover'  => $firstCrossover,
            'second_crossover' => $secondCrossover,
            'candles'          => $chartData['candles'],
            'short_smas'       => $chartData['short_smas'],
            'long_smas'        => $chartData['long_smas'],
        ];
    }

    /**
     * Return crossover data from DB history when available, otherwise compute and persist.
     *
     * @return array{0: array<int,array<string,mixed>>, 1: int, 2: array<string,mixed>|null, 3: array<string,mixed>|null}
     */
    private function resolveCrossovers(array $params, array $detailed, int $startTimeSec): array
    {
        $existing = CalculationHistory::where([
            'symbol'       => $params['symbol'],
            'interval'     => $params['interval'],
            'from_date'    => $params['from'],
            'to_date'      => $params['to'],
            'short_period' => $params['short_period'],
            'long_period'  => $params['long_period'],
        ])->first();

        if ($existing !== null) {
            return $this->crossoversFromHistory($existing);
        }

        return $this->crossoversFromCalculation($params, $detailed, $startTimeSec);
    }

    /** @return array{0: array<int,array<string,mixed>>, 1: int, 2: array<string,mixed>|null, 3: array<string,mixed>|null} */
    private function crossoversFromHistory(CalculationHistory $existing): array
    {
        $mapped = $existing->crossovers;
        $count  = $existing->crossover_count;
        $first  = !empty($mapped) ? (array) $mapped[0] : null;
        $second = count($mapped) > 1 ? (array) $mapped[1] : null;

        return [$mapped, $count, $first, $second];
    }

    /** @return array{0: array<int,array<string,mixed>>, 1: int, 2: array<string,mixed>|null, 3: array<string,mixed>|null} */
    private function crossoversFromCalculation(array $params, array $detailed, int $startTimeSec): array
    {
        $crossovers = array_values(array_filter(
            $detailed['crossovers'],
            fn(Crossover $c) => $c->timestamp >= $startTimeSec
        ));

        $mapped = array_map(fn(Crossover $c) => [
            'time'      => $c->time,
            'timestamp' => $c->timestamp,
            'type'      => $c->type,
            'short_sma' => round($c->shortSmaValue, 4),
            'long_sma'  => round($c->longSmaValue,  4),
        ], $crossovers);

        $count  = count($crossovers);
        $first  = !empty($mapped) ? $mapped[0] : null;
        $second = count($mapped) > 1 ? $mapped[1] : null;

        CalculationHistory::create([
            'symbol'          => $params['symbol'],
            'interval'        => $params['interval'],
            'from_date'       => $params['from'],
            'to_date'         => $params['to'],
            'short_period'    => $params['short_period'],
            'long_period'     => $params['long_period'],
            'crossover_count' => $count,
            'crossovers'      => $mapped,
        ]);

        return [$mapped, $count, $first, $second];
    }

    /**
     * Filter candles and SMAs to the user-requested date range and format for charting.
     *
     * @param  array<int,\App\Models\Candle> $candles
     * @param  array<int,float|null>         $shortSmas
     * @param  array<int,float|null>         $longSmas
     * @return array{candles: list<array<string,mixed>>, short_smas: list<array<string,mixed>>, long_smas: list<array<string,mixed>>}
     */
    private function buildChartData(array $candles, array $shortSmas, array $longSmas, int $startTimeSec): array
    {
        $candleData   = [];
        $shortSmaData = [];
        $longSmaData  = [];

        foreach ($candles as $i => $candle) {
            $timeSec = (int) ($candle->timestamp / 1000);
            if ($timeSec < $startTimeSec) {
                continue;
            }

            $candleData[] = [
                'time'  => $timeSec,
                'open'  => $candle->open,
                'high'  => $candle->high,
                'low'   => $candle->low,
                'close' => $candle->close,
            ];

            if (isset($shortSmas[$i])) {
                $shortSmaData[] = ['time' => $timeSec, 'value' => round($shortSmas[$i], 4)];
            }

            if (isset($longSmas[$i])) {
                $longSmaData[] = ['time' => $timeSec, 'value' => round($longSmas[$i], 4)];
            }
        }

        return ['candles' => $candleData, 'short_smas' => $shortSmaData, 'long_smas' => $longSmaData];
    }
}
