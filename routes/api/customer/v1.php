<?php

use Illuminate\Support\Facades\Route;
use Modules\Hotel\Http\Controllers\Api\V1\Customer\HotelApiController;
use Modules\Hotel\Http\Controllers\Api\V1\Customer\HotelBookingController;
use Modules\Hotel\Http\Controllers\Api\V1\Customer\ProvinceController;
use Modules\Hotel\Http\Controllers\Api\V1\Customer\AmenityController;
use Modules\Hotel\Http\Controllers\Api\V1\Customer\HotelReviewController;

/*
|--------------------------------------------------------------------------
| Hotel Customer API Routes
|--------------------------------------------------------------------------
*/

// Public customer-facing API (no auth required)
Route::prefix('v1/hotels')->name('hotel.')->group(function () {
    // Booking home & search
    Route::get('/booking-home', [HotelBookingController::class, 'home'])->name('booking.home');
    Route::get('/search', [HotelBookingController::class, 'search'])->name('search');

    // Hotel listings & details
    Route::get('/', [HotelApiController::class, 'index'])->name('index');
    Route::get('/featured', [HotelApiController::class, 'featured'])->name('featured');
    Route::get('/{hotel}', [HotelApiController::class, 'show'])->name('show');
    Route::get('/{hotel}/rooms', [HotelApiController::class, 'rooms'])->name('rooms');

    // Hotel reviews (public & authenticated)
    Route::get('/{hotel}/reviews', [HotelReviewController::class, 'index'])->name('reviews.index');
    Route::get('/{hotel}/reviews/stats', [HotelReviewController::class, 'stats'])->name('reviews.stats');
    Route::post('/{hotel}/reviews', [HotelReviewController::class, 'store'])->middleware('auth:sanctum')->name('reviews.store');
});

// Reference data (dropdown options)
Route::prefix('v1')->name('hotel.')->group(function () {
    Route::get('/provinces', [ProvinceController::class, 'index'])->name('provinces.index');
    Route::get('/provinces/{province}', [ProvinceController::class, 'show'])->name('provinces.show');
    
    Route::get('/amenities', [AmenityController::class, 'index'])->name('amenities.index');
    Route::get('/amenities/{amenity}', [AmenityController::class, 'show'])->name('amenities.show');
    
    Route::get('/hotels/categories', [HotelApiController::class, 'categories'])->name('categories');
    Route::get('/hotels/cities', [HotelApiController::class, 'cities'])->name('cities');
});
