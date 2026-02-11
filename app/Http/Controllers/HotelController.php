<?php

namespace Modules\Hotel\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Hotel\Http\Requests\StoreHotelRequest;
use Modules\Hotel\Http\Requests\UpdateHotelRequest;
use Modules\Hotel\Services\HotelService;

class HotelController extends Controller
{
    public function __construct(
        protected HotelService $hotelService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $hotels = $this->hotelService->getPaginatedHotels();

        return Inertia::render('hotel::Index', [
            'hotels' => $hotels,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('hotel::Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreHotelRequest $request): RedirectResponse
    {
        $this->hotelService->createHotel($request->validated());

        return redirect()
            ->route('hotel.index')
            ->with('success', 'Hotel created successfully.');
    }

    /**
     * Show the specified resource.
     */
    public function show(int $id): Response
    {
        $hotel = $this->hotelService->getHotelById($id);

        if (! $hotel) {
            abort(404);
        }

        return Inertia::render('hotel::Show', [
            'hotel' => $hotel,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): Response
    {
        $hotel = $this->hotelService->getHotelById($id);

        if (! $hotel) {
            abort(404);
        }

        return Inertia::render('hotel::Edit', [
            'hotel' => $hotel,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateHotelRequest $request, int $id): RedirectResponse
    {
        $updated = $this->hotelService->updateHotel($id, $request->validated());

        if (! $updated) {
            abort(404);
        }

        return redirect()
            ->route('hotel.index')
            ->with('success', 'Hotel updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $deleted = $this->hotelService->deleteHotel($id);

        if (! $deleted) {
            abort(404);
        }

        return redirect()
            ->route('hotel.index')
            ->with('success', 'Hotel deleted successfully.');
    }
}
