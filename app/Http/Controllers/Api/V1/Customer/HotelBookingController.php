<?php

namespace Modules\Hotel\Http\Controllers\Api\V1\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Hotel\Http\Resources\Api\Customer\V1\HotelResource;
use Modules\Hotel\Http\Resources\Api\Customer\V1\ProvinceResource;
use Modules\Hotel\Http\Resources\Api\Customer\V1\HotelCategoryResource;
use Modules\Hotel\Models\Hotel;
use Modules\Hotel\Models\Province;
use Modules\Hotel\Models\HotelCategory;

class HotelBookingController extends Controller
{
    /**
     * Get data for the hotel booking home page
     */
    public function home(): JsonResponse
    {
        // 1. Popular Destinations (Provinces with most hotels)
        $popularDestinations = Province::active()
            ->withCount('hotels')
            ->orderByDesc('hotels_count')
            ->limit(6)
            ->get();

        // 2. Featured Hotels
        $featuredHotels = Hotel::active()
            ->featured()
            ->with(['category', 'province'])
            ->latest()
            ->limit(10)
            ->get();

        // 3. Recommended (For now, let's use high rated or most reviewed)
        $recommendedHotels = Hotel::active()
            ->where('star_rating', '>=', 4)
            ->with(['category', 'province'])
            ->latest()
            ->limit(10)
            ->get();

        // 4. Deals (Hotels with discount or lowest price)
        // Note: You might want to add a discount column later
        $deals = Hotel::active()
            ->with(['category', 'province'])
            ->orderBy('price_per_night', 'asc')
            ->limit(10)
            ->get();

        // 5. Categories (Property Types)
        $categories = HotelCategory::active()
            ->withCount('hotels')
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'data' => [
                'popular_destinations' => ProvinceResource::collection($popularDestinations),
                'featured_hotels' => HotelResource::collection($featuredHotels),
                'recommended_hotels' => HotelResource::collection($recommendedHotels),
                'deals' => HotelResource::collection($deals),
                'categories' => HotelCategoryResource::collection($categories),
            ]
        ]);
    }

    /**
     * Search hotels with advanced filters
     */
    public function search(): JsonResponse
    {
        $query = Hotel::active()->with(['category', 'province']);

        // Filter by keyword
        if (request()->has('q')) {
            $q = request('q');
            $query->where(function($sq) use ($q) {
                $sq->where('name', 'like', "%$q%")
                  ->orWhere('city', 'like', "%$q%")
                  ->orWhere('address', 'like', "%$q%");
            });
        }

        // Filter by Province
        if (request()->has('province_id')) {
            $query->where('province_id', request('province_id'));
        }

        // Filter by Category
        if (request()->has('category_id')) {
            $query->where('hotel_category_id', request('category_id'));
        }

        // Price Range
        if (request()->has('min_price')) {
            $query->where('price_per_night', '>=', request('min_price'));
        }
        if (request()->has('max_price')) {
            $query->where('price_per_night', '<=', request('max_price'));
        }

        // Amenities (if many-to-many relationship exists)
        if (request()->has('amenities')) {
            $amenityIds = explode(',', request('amenities'));
            $query->whereHas('amenities', function($aq) use ($amenityIds) {
                $aq->whereIn('amenities.id', $amenityIds);
            });
        }

        // Sorting
        $sort = request('sort', 'recommended');
        switch ($sort) {
            case 'price_low':
                $query->orderBy('price_per_night', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price_per_night', 'desc');
                break;
            case 'rating':
                $query->orderByDesc('star_rating');
                break;
            default:
                $query->latest();
                break;
        }

        $perPage = request('per_page', 15);
        $hotels = $query->paginate($perPage);

        return HotelResource::collection($hotels)->response();
    }
}
