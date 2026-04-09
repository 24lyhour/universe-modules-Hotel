<?php

namespace Modules\Hotel\Http\Controllers\Dashboard\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Momentum\Modal\Modal;
use Modules\Hotel\Enums\HotelStatusEnum;
use Modules\Hotel\Http\Requests\StoreHotelRequest;
use Modules\Hotel\Http\Requests\UpdateHotelRequest;
use Modules\Hotel\Http\Resources\HotelResource;
use Modules\Hotel\Models\Hotel;
use Modules\Hotel\Models\HotelCategory;
use Modules\Hotel\Models\Province;
use Modules\Hotel\Services\HotelService;

class HotelController extends Controller
{
    public function __construct(
        protected HotelService $hotelService
    ) {}

    public function index(): Response
    {
        $filters = request()->only(['search', 'status', 'province', 'city', 'category', 'star_rating', 'is_featured', 'min_price', 'max_price']);
        $hotels = $this->hotelService->paginate(15, $filters);
        $stats = $this->hotelService->getStats();

        return Inertia::render('hotel::Dashboard/V1/Hotel/Index', [
            'hotels' => HotelResource::collection($hotels)->response()->getData(true),
            'stats' => $stats,
            'filters' => $filters,
            'categories' => HotelCategory::active()->orderBy('sort_order')->get(['id', 'uuid', 'name']),
            'provinces' => Province::active()->orderBy('sort_order')->get(['id', 'uuid', 'name', 'name_kh', 'code']),
            'statuses' => HotelStatusEnum::options(),
        ]);
    }

    public function create(): Modal
    {
        return Inertia::modal('hotel::Dashboard/V1/Hotel/Create', [
            'categories' => HotelCategory::active()->orderBy('sort_order')->get(['id', 'uuid', 'name']),
            'provinces' => Province::active()->orderBy('sort_order')->get(['id', 'uuid', 'name', 'name_kh', 'code']),
            'statuses' => HotelStatusEnum::options(),
        ])->baseRoute('hotel.hotels.index');
    }

    public function store(StoreHotelRequest $request): RedirectResponse
    {
        $this->hotelService->create($request->validated());

        return redirect()
            ->route('hotel.hotels.index')
            ->with('success', 'Hotel created successfully.');
    }

    public function show(Hotel $hotel): Response
    {
        $hotel->load(['category', 'province', 'user', 'rooms', 'createdBy', 'updatedBy']);

        return Inertia::render('hotel::Dashboard/V1/Hotel/Show', [
            'hotel' => new HotelResource($hotel),
        ]);
    }

    public function edit(Hotel $hotel): Modal
    {
        $hotel->load(['category', 'province']);

        return Inertia::modal('hotel::Dashboard/V1/Hotel/Edit', [
            'hotel' => new HotelResource($hotel),
            'categories' => HotelCategory::active()->orderBy('sort_order')->get(['id', 'uuid', 'name']),
            'provinces' => Province::active()->orderBy('sort_order')->get(['id', 'uuid', 'name', 'name_kh', 'code']),
            'statuses' => HotelStatusEnum::options(),
        ])->baseRoute('hotel.hotels.index');
    }

    public function update(UpdateHotelRequest $request, Hotel $hotel): RedirectResponse
    {
        $this->hotelService->update($hotel, $request->validated());

        return redirect()
            ->route('hotel.hotels.index')
            ->with('success', 'Hotel updated successfully.');
    }

    public function destroy(Hotel $hotel): RedirectResponse
    {
        $this->hotelService->delete($hotel);

        return redirect()
            ->route('hotel.hotels.index')
            ->with('success', 'Hotel deleted successfully.');
    }

    public function confirmDelete(Hotel $hotel): Modal
    {
        return Inertia::modal('hotel::Dashboard/V1/Hotel/ConfirmDelete', [
            'hotel' => new HotelResource($hotel),
        ])->baseRoute('hotel.hotels.index');
    }

    public function toggleFeatured(Hotel $hotel): RedirectResponse
    {
        $this->hotelService->toggleFeatured($hotel);

        return redirect()->back()->with('success', 'Hotel featured status updated.');
    }

    public function updateStatus(Hotel $hotel): RedirectResponse
    {
        $status = HotelStatusEnum::from(request('status'));
        $this->hotelService->updateStatus($hotel, $status);

        return redirect()->back()->with('success', 'Hotel status updated.');
    }

    // Trash management

    public function trash(): Response
    {
        $hotels = $this->hotelService->getTrashed();

        return Inertia::render('hotel::Dashboard/V1/Hotel/Trash', [
            'hotels' => HotelResource::collection($hotels)->response()->getData(true),
        ]);
    }

    public function restore(string $uuid): RedirectResponse
    {
        $this->hotelService->restore($uuid);

        return redirect()->back()->with('success', 'Hotel restored successfully.');
    }

    public function forceDelete(string $uuid): RedirectResponse
    {
        $this->hotelService->forceDelete($uuid);

        return redirect()->back()->with('success', 'Hotel permanently deleted.');
    }

    // Bulk operations

    public function confirmBulkDelete(): Modal
    {
        return Inertia::modal('hotel::Dashboard/V1/Hotel/ConfirmBulkDelete', [
            'uuids' => request('uuids', []),
        ])->baseRoute('hotel.hotels.index');
    }

    public function bulkDelete(): RedirectResponse
    {
        $uuids = request('uuids', []);
        $this->hotelService->bulkDelete($uuids);

        return redirect()
            ->route('hotel.hotels.index')
            ->with('success', 'Selected hotels deleted.');
    }

    public function bulkRestore(): RedirectResponse
    {
        $uuids = request('uuids', []);
        $this->hotelService->bulkRestore($uuids);

        return redirect()->back()->with('success', 'Selected hotels restored.');
    }

    public function emptyTrash(): RedirectResponse
    {
        $this->hotelService->emptyTrash();

        return redirect()->back()->with('success', 'Trash emptied successfully.');
    }
}
