<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

try {
    $controller = $app->make(App\Http\Controllers\CrossoverApiController::class);
    $request = Illuminate\Http\Request::create('/api/v1/crossovers/calculate', 'POST', [
        'symbol' => 'BTCUSDT',
        'interval' => '30m',
        'from' => '2024-10-20 00:00:00',
        'to' => '2024-10-27 00:00:00',
        'short_period' => 50,
        'long_period' => 200,
    ]);
    $request->headers->set('X-App-Password', 'secret123');

    // Wipe database to make sure it runs the calculation
    App\Models\CalculationHistory::truncate();

    $response = $controller->calculate($request);
    $data = json_decode($response->getContent(), true);

    echo "Crossovers Count in JSON response: " . $data['data']['crossovers_count'] . "\n";
    echo "Crossovers in JSON:\n";
    foreach ($data['data']['crossovers'] as $cross) {
        echo "Time: {$cross['time']} | Type: {$cross['type']} | Short: {$cross['short_sma']} | Long: {$cross['long_sma']}\n";
    }
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
