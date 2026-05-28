<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CrossoverApiController;

Route::get('/health', function () {
    return response()->json(['status' => 'success', 'data' => ['alive' => true]]);
});

Route::prefix('v1')->group(function () {
    Route::post('/crossovers/calculate', [CrossoverApiController::class, 'calculate']);
    Route::get('/crossovers/history', [CrossoverApiController::class, 'history']);
});
