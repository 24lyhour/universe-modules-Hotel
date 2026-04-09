<?php

namespace Modules\Hotel\Http\Controllers\Dashboard\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Momentum\Modal\Modal;
use Modules\Hotel\Actions\Dashboard\V1\HotelAction\BulkDeleteHotelsAction;
use Modules\Hotel\Actions\Dashboard\V1\HotelAction\CreateHotelAction;
use Modules\Hotel\Actions\Dashboard\V1\HotelAction\DeleteHotelAction;
use Modules\Hotel\Actions\Dashboard\V1\HotelAction\GetHotelCreateDataAction;
use Modules\Hotel\Actions\Dashboard\V1\HotelAction\GetHotelEditDataAction;
use Modules\Hotel\Actions\Dashboard\V1\HotelAction\GetHotelIndexDataAction;
use Modules\Hotel\Actions\Dashboard\V1\HotelAction\GetHotelShowDataAction;
use Modules\Hotel\Actions\Dashboard\V1\HotelAction\ToggleHotelFeaturedAction;
use Modules\Hotel\Actions\Dashboard\V1\HotelAction\UpdateHotelAction;
use Modules\Hotel\Actions\Dashboard\V1\HotelAction\UpdateHotelStatusAction;
use Modules\Hotel\Enums\HotelStatusEnum;
use Modules\Hotel\Http\Requests\Dashboard\V1\HotelRequest\BulkDeleteHotelsRequest;
use Modules\Hotel\Http\Requests\Dashboard\V1\HotelRequest\StoreHotelRequest;
use Modules\Hotel\Http\Requests\Dashboard\V1\HotelRequest\UpdateHotelRequest;
use Modules\Hotel\Http\Resources\HotelResource;
use Modules\Hotel\Models\Hotel;

class HotelController extends Controller
{
    public function index(Request $request, GetHotelIndexDataAction $action): Response
    {
        $filters = $request->only(['search', 'status', 'province', 'city', 'category', 'star_rating', 'is_featured', 'min_price', 'max_price']);

        return Inertia::render('hotel::Dashboard/V1/Hotel/Index', $action->execute(15, $filters));
    }

    public function create(GetHotelCreateDataAction $action): Modal
    {
        return Inertia::modal('hotel::Dashboard/V1/Hotel/Create', $action->execute())
            ->baseRoute('hotel.hotels.index');
    }

    public function store(StoreHotelRequest $request, CreateHotelAction $action): RedirectResponse
    {
        $action->execute($request->validated());

        return redirect()
            ->route('hotel.hotels.index')
            ->with('success', 'Hotel created successfully.');
    }

    public function show(Hotel $hotel, GetHotelShowDataAction $action): Response
    {
        return Inertia::render('hotel::Dashboard/V1/Hotel/Show', $action->execute($hotel));
    }

    public function edit(Hotel $hotel, GetHotelEditDataAction $action): Modal
    {
        return Inertia::modal('hotel::Dashboard/V1/Hotel/Edit', $action->execute($hotel))
            ->baseRoute('hotel.hotels.index');
    }

    public function confirmDelete(Hotel $hotel, GetHotelShowDataAction $action): Modal
    {
        return Inertia::modal('hotel::Dashboard/V1/Hotel/Delete', $action->execute($hotel))
            ->baseRoute('hotel.hotels.index');
    }

    public function update(UpdateHotelRequest $request, Hotel $hotel, UpdateHotelAction $action): RedirectResponse
    {
        $action->execute($hotel, $request->validated());

        return redirect()
            ->route('hotel.hotels.index')
            ->with('success', 'Hotel updated successfully.');
    }

    public function destroy(Hotel $hotel, DeleteHotelAction $action): RedirectResponse
    {
        $action->execute($hotel);

        return redirect()
            ->route('hotel.hotels.index')
            ->with('success', 'Hotel deleted successfully.');
    }

    public function toggleFeatured(Hotel $hotel, ToggleHotelFeaturedAction $action): RedirectResponse
    {
        $action->execute($hotel);

        return redirect()->back()->with('success', 'Hotel featured status updated.');
    }

    public function updateStatus(Request $request, Hotel $hotel, UpdateHotelStatusAction $action): RedirectResponse
    {
        $status = HotelStatusEnum::from($request->input('status'));
        $action->execute($hotel, $status);

        return redirect()->back()->with('success', 'Hotel status updated.');
    }

    // Trash

    public function trash(): Response
    {
        $hotels = Hotel::onlyTrashed()->with(['category', 'user'])->latest('deleted_at')->paginate(15);

        return Inertia::render('hotel::Dashboard/V1/Hotel/Trash', [
            'hotels' => HotelResource::collection($hotels)->response()->getData(true),
        ]);
    }

    public function restore(string $uuid): RedirectResponse
    {
        $hotel = Hotel::onlyTrashed()->where('uuid', $uuid)->first();

        if ($hotel) {
            $hotel->restore();
        }

        return redirect()->back()->with('success', 'Hotel restored successfully.');
    }

    public function forceDelete(string $uuid): RedirectResponse
    {
        $hotel = Hotel::onlyTrashed()->where('uuid', $uuid)->first();

        if ($hotel) {
            $hotel->forceDelete();
        }

        return redirect()->back()->with('success', 'Hotel permanently deleted.');
    }

    // Bulk operations

    public function confirmBulkDelete(): Modal
    {
        return Inertia::modal('hotel::Dashboard/V1/Hotel/ConfirmBulkDelete', [
            'uuids' => request('uuids', []),
        ])->baseRoute('hotel.hotels.index');
    }

    public function bulkDelete(BulkDeleteHotelsRequest $request, BulkDeleteHotelsAction $action): RedirectResponse
    {
        $action->execute($request->validated()['uuids']);

        return redirect()
            ->route('hotel.hotels.index')
            ->with('success', 'Selected hotels deleted.');
    }

    public function bulkRestore(Request $request): RedirectResponse
    {
        $uuids = $request->input('uuids', []);
        Hotel::onlyTrashed()->whereIn('uuid', $uuids)->restore();

        return redirect()->back()->with('success', 'Selected hotels restored.');
    }

    public function emptyTrash(): RedirectResponse
    {
        Hotel::onlyTrashed()->forceDelete();

        return redirect()->back()->with('success', 'Trash emptied successfully.');
    }
}
