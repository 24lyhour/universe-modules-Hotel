<?php

use Illuminate\Support\Facades\Route;
use Modules\Hotel\Http\Controllers\Dashboard\V1\AmenityController;
use Modules\Hotel\Http\Controllers\Dashboard\V1\HotelCategoryController;
use Modules\Hotel\Http\Controllers\Dashboard\V1\HotelController;
use Modules\Hotel\Http\Controllers\Dashboard\V1\ProvinceController;
use Modules\Hotel\Http\Controllers\Dashboard\V1\RoomController;

Route::middleware(['auth', 'verified', 'auto.permission'])
    ->prefix('dashboard')
    ->group(function () {

        // ---------------------------------------------------------------
        // Hotel Categories
        // ---------------------------------------------------------------
        Route::get('hotel-categories/trash', [HotelCategoryController::class, 'trash'])->name('hotel.categories.trash');
        Route::put('hotel-categories/{uuid}/restore', [HotelCategoryController::class, 'restore'])->name('hotel.categories.restore');
        Route::delete('hotel-categories/{uuid}/force-delete', [HotelCategoryController::class, 'forceDelete'])->name('hotel.categories.force-delete');
        Route::delete('hotel-categories/bulk-delete', [HotelCategoryController::class, 'bulkDelete'])->name('hotel.categories.bulk-delete');
        Route::patch('hotel-categories/{category}/toggle-active', [HotelCategoryController::class, 'toggleActive'])->name('hotel.categories.toggle-active');
        Route::get('hotel-categories/{category}/delete', [HotelCategoryController::class, 'confirmDelete'])->name('hotel.categories.confirm-delete');

        Route::resource('hotel-categories', HotelCategoryController::class)
            ->parameters(['hotel-categories' => 'category'])
            ->names('hotel.categories');

        // ---------------------------------------------------------------
        // Amenities
        // ---------------------------------------------------------------
        Route::get('hotel-amenities/trash', [AmenityController::class, 'trash'])->name('hotel.amenities.trash');
        Route::put('hotel-amenities/{uuid}/restore', [AmenityController::class, 'restore'])->name('hotel.amenities.restore');
        Route::delete('hotel-amenities/{uuid}/force-delete', [AmenityController::class, 'forceDelete'])->name('hotel.amenities.force-delete');
        Route::delete('hotel-amenities/bulk-delete', [AmenityController::class, 'bulkDelete'])->name('hotel.amenities.bulk-delete');
        Route::patch('hotel-amenities/{amenity}/toggle-active', [AmenityController::class, 'toggleActive'])->name('hotel.amenities.toggle-active');
        Route::get('hotel-amenities/{amenity}/delete', [AmenityController::class, 'confirmDelete'])->name('hotel.amenities.confirm-delete');

        Route::resource('hotel-amenities', AmenityController::class)
            ->parameters(['hotel-amenities' => 'amenity'])
            ->names('hotel.amenities');

        // ---------------------------------------------------------------
        // Provinces
        // ---------------------------------------------------------------
        Route::patch('hotel-provinces/{province}/toggle-active', [ProvinceController::class, 'toggleActive'])->name('hotel.provinces.toggle-active');
        Route::get('hotel-provinces/{province}/delete', [ProvinceController::class, 'confirmDelete'])->name('hotel.provinces.confirm-delete');

        Route::resource('hotel-provinces', ProvinceController::class)
            ->parameters(['hotel-provinces' => 'province'])
            ->names('hotel.provinces');

        // ---------------------------------------------------------------
        // Hotels
        // ---------------------------------------------------------------
        // Trash management
        Route::get('hotels/trash', [HotelController::class, 'trash'])->name('hotel.hotels.trash');
        Route::put('hotels/{uuid}/restore', [HotelController::class, 'restore'])->name('hotel.hotels.restore');
        Route::delete('hotels/{uuid}/force-delete', [HotelController::class, 'forceDelete'])->name('hotel.hotels.force-delete');
        Route::delete('hotels/empty-trash', [HotelController::class, 'emptyTrash'])->name('hotel.hotels.empty-trash');

        // Bulk operations
        Route::get('hotels/bulk-delete', [HotelController::class, 'confirmBulkDelete'])->name('hotel.hotels.confirm-bulk-delete');
        Route::delete('hotels/bulk-delete', [HotelController::class, 'bulkDelete'])->name('hotel.hotels.bulk-delete');
        Route::put('hotels/bulk-restore', [HotelController::class, 'bulkRestore'])->name('hotel.hotels.bulk-restore');

        // Toggle & status
        Route::patch('hotels/{hotel}/toggle-featured', [HotelController::class, 'toggleFeatured'])->name('hotel.hotels.toggle-featured');
        Route::patch('hotels/{hotel}/update-status', [HotelController::class, 'updateStatus'])->name('hotel.hotels.update-status');

        // Confirm delete modal
        Route::get('hotels/{hotel}/confirm-delete', [HotelController::class, 'confirmDelete'])->name('hotel.hotels.confirm-delete');

        // Standard CRUD resource
        Route::resource('hotels', HotelController::class)->names('hotel.hotels');

        // ---------------------------------------------------------------
        // Rooms (nested under hotels)
        // ---------------------------------------------------------------
        Route::get('hotels/{hotel}/rooms/trash', [RoomController::class, 'trash'])->name('hotel.hotels.rooms.trash');
        Route::put('hotels/{hotel}/rooms/{uuid}/restore', [RoomController::class, 'restore'])->name('hotel.hotels.rooms.restore');
        Route::delete('hotels/{hotel}/rooms/{uuid}/force-delete', [RoomController::class, 'forceDelete'])->name('hotel.hotels.rooms.force-delete');
        Route::delete('hotels/{hotel}/rooms/bulk-delete', [RoomController::class, 'bulkDelete'])->name('hotel.hotels.rooms.bulk-delete');
        Route::patch('hotels/{hotel}/rooms/{room}/toggle-availability', [RoomController::class, 'toggleAvailability'])->name('hotel.hotels.rooms.toggle-availability');

        Route::resource('hotels.rooms', RoomController::class)->names('hotel.hotels.rooms');
    });
