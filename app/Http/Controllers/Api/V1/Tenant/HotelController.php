<?php

namespace Modules\Hotel\Http\Controllers\Api\V1\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\Hotel\Models\Hotel;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tenant-scoped Hotel API.
 *
 * Hotels are owned by a single user (`user_id`) — this is the simplest
 * tenancy model in the project. Each authenticated user can manage only
 * the hotels they personally own.
 */
class HotelController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $hotels = Hotel::query()
            ->where('user_id', $request->user()->id)
            ->with(['hotelCategory'])
            ->latest()
            ->paginate((int) $request->input('per_page', 15));

        return response()->json($hotels);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'hotel_category_id' => ['nullable', 'integer', 'exists:hotel_categories,id'],
            'description' => ['nullable', 'string'],
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:120'],
            'country' => ['nullable', 'string', 'max:120'],
            'province_id' => ['nullable', 'integer', 'exists:provinces,id'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'star_rating' => ['nullable', 'integer', 'between:1,5'],
            'price_level' => ['nullable', 'string', 'in:budget,economy,mid_range,upscale,luxury'],
            'min_price' => ['nullable', 'numeric', 'min:0'],
            'max_price' => ['nullable', 'numeric', 'min:0', 'gte:min_price'],
            'currency' => ['nullable', 'string', 'size:3'],
            'featured_image' => ['nullable', 'string'],
            'logo_url' => ['nullable', 'string'],
            'gallery' => ['nullable', 'array'],
            'amenities' => ['nullable', 'array'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'status' => ['nullable', 'string', 'in:active,inactive'],
            'is_featured' => ['nullable', 'boolean'],
        ]);

        $userId = $request->user()->id;

        $hotel = Hotel::create(array_merge($validated, [
            'user_id' => $userId,
            'created_by' => $userId,
            'slug' => Str::slug($validated['name']),
            'status' => $validated['status'] ?? 'active',
            'price_per_night' => $validated['min_price'] ?? 0,
        ]));

        return response()->json(['data' => $hotel->load('hotelCategory')], Response::HTTP_CREATED);
    }

    public function show(Request $request, Hotel $hotel): JsonResponse
    {
        $this->assertOwner($request, $hotel);

        return response()->json([
            'data' => $hotel->load(['hotelCategory', 'rooms']),
        ]);
    }

    public function update(Request $request, Hotel $hotel): JsonResponse
    {
        $this->assertOwner($request, $hotel);

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'hotel_category_id' => ['nullable', 'integer', 'exists:hotel_categories,id'],
            'description' => ['nullable', 'string'],
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:120'],
            'country' => ['nullable', 'string', 'max:120'],
            'province_id' => ['nullable', 'integer', 'exists:provinces,id'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'star_rating' => ['nullable', 'integer', 'between:1,5'],
            'price_level' => ['nullable', 'string', 'in:budget,economy,mid_range,upscale,luxury'],
            'min_price' => ['nullable', 'numeric', 'min:0'],
            'max_price' => ['nullable', 'numeric', 'min:0', 'gte:min_price'],
            'currency' => ['nullable', 'string', 'size:3'],
            'featured_image' => ['nullable', 'string'],
            'logo_url' => ['nullable', 'string'],
            'gallery' => ['nullable', 'array'],
            'amenities' => ['nullable', 'array'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'status' => ['sometimes', 'string', 'in:active,inactive'],
            'is_featured' => ['nullable', 'boolean'],
        ]);

        $payload = array_merge($validated, ['updated_by' => $request->user()->id]);
        if (isset($validated['name'])) {
            $payload['slug'] = Str::slug($validated['name']);
        }

        $hotel->update($payload);

        return response()->json(['data' => $hotel->fresh()->load('hotelCategory')]);
    }

    public function destroy(Request $request, Hotel $hotel): JsonResponse
    {
        $this->assertOwner($request, $hotel);

        $hotel->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    protected function assertOwner(Request $request, Hotel $hotel): void
    {
        if ((int) $hotel->user_id !== (int) $request->user()->id) {
            abort(Response::HTTP_NOT_FOUND);
        }
    }
}
