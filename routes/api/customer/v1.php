<?php

use Illuminate\Support\Facades\Route;
use Modules\Hotel\Http\Controllers\Api\V1\Customer\HotelApiController;

/*
|--------------------------------------------------------------------------
| Hotel Customer API Routes
|--------------------------------------------------------------------------
*/

// Public customer-facing API (no auth required)
Route::prefix('v1/hotels')->name('hotel.')->group(function () {
    Route::get('/', [HotelApiController::class, 'index'])->name('index');
    Route::get('/featured', [HotelApiController::class, 'featured'])->name('featured');
    Route::get('/cities', [HotelApiController::class, 'cities'])->name('cities');
    Route::get('/provinces', [HotelApiController::class, 'provinces'])->name('provinces');
    Route::get('/categories', [HotelApiController::class, 'categories'])->name('categories');
    Route::get('/amenities', [HotelApiController::class, 'amenities'])->name('amenities');
    Route::get('/{hotel}', [HotelApiController::class, 'show'])->name('show');
    Route::get('/{hotel}/rooms', [HotelApiController::class, 'rooms'])->name('rooms');
});
