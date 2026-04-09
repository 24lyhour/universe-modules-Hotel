<?php

use Illuminate\Support\Facades\Route;
use Modules\Hotel\Http\Controllers\Dashboard\V1\HotelController;

/*
|--------------------------------------------------------------------------
| Hotel API Routes
|--------------------------------------------------------------------------
*/

// Customer API routes (separated into api/customer/v1.php)
require __DIR__ . '/api/customer/v1.php';

// Authenticated dashboard API
Route::middleware(['auth:sanctum'])->prefix('v1/dashboard')->group(function () {
    Route::apiResource('hotels', HotelController::class)->names('api.hotel.hotels');
});
