<?php

namespace Modules\Hotel\Http\Controllers\Dashboard\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Momentum\Modal\Modal;
use Modules\Hotel\Actions\Dashboard\V1\AmenitiesAction\BulkDeleteAmenitiesAction;
use Modules\Hotel\Actions\Dashboard\V1\AmenitiesAction\CreateAmenityAction;
use Modules\Hotel\Actions\Dashboard\V1\AmenitiesAction\DeleteAmenityAction;
use Modules\Hotel\Actions\Dashboard\V1\AmenitiesAction\GetAmenityEditDataAction;
use Modules\Hotel\Actions\Dashboard\V1\AmenitiesAction\GetAmenityIndexDataAction;
use Modules\Hotel\Actions\Dashboard\V1\AmenitiesAction\ToggleAmenityStatusAction;
use Modules\Hotel\Actions\Dashboard\V1\AmenitiesAction\UpdateAmenityAction;
use Modules\Hotel\Http\Requests\Dashboard\V1\AmenityRequest\BulkDeleteAmenitiesRequest;
use Modules\Hotel\Http\Requests\Dashboard\V1\AmenityRequest\StoreAmenityRequest;
use Modules\Hotel\Http\Requests\Dashboard\V1\AmenityRequest\UpdateAmenityRequest;
use Modules\Hotel\Http\Resources\AmenityResource;
use Modules\Hotel\Models\Amenity;

class AmenityController extends Controller
{
    public function index(Request $request, GetAmenityIndexDataAction $action): Response
    {
        $perPage = $request->input('per_page', 10);
        $filters = $request->only(['search', 'is_active', 'group']);

        return Inertia::render('hotel::Dashboard/V1/Amenity/Index', $action->execute($perPage, $filters));
    }

    public function create(): Modal
    {
        return Inertia::modal('hotel::Dashboard/V1/Amenity/Create')
            ->baseRoute('hotel.amenities.index');
    }

    public function store(StoreAmenityRequest $request, CreateAmenityAction $action): RedirectResponse
    {
        $action->execute($request->validated());

        return redirect()
            ->route('hotel.amenities.index')
            ->with('success', 'Amenity created successfully.');
    }

    public function edit(Amenity $amenity, GetAmenityEditDataAction $action): Modal
    {
        return Inertia::modal('hotel::Dashboard/V1/Amenity/Edit', $action->execute($amenity))
            ->baseRoute('hotel.amenities.index');
    }

    public function confirmDelete(Amenity $amenity, GetAmenityEditDataAction $action): Modal
    {
        return Inertia::modal('hotel::Dashboard/V1/Amenity/Delete', $action->execute($amenity))
            ->baseRoute('hotel.amenities.index');
    }

    public function update(UpdateAmenityRequest $request, Amenity $amenity, UpdateAmenityAction $action): RedirectResponse
    {
        $action->execute($amenity, $request->validated());

        return redirect()
            ->route('hotel.amenities.index')
            ->with('success', 'Amenity updated successfully.');
    }

    public function destroy(Amenity $amenity, DeleteAmenityAction $action): RedirectResponse
    {
        $action->execute($amenity);

        return redirect()
            ->route('hotel.amenities.index')
            ->with('success', 'Amenity deleted successfully.');
    }

    public function toggleActive(Amenity $amenity, ToggleAmenityStatusAction $action): RedirectResponse
    {
        $action->execute($amenity);

        return redirect()->back()->with('success', 'Amenity status updated.');
    }

    // Trash

    public function trash(): Response
    {
        $amenities = Amenity::onlyTrashed()->latest('deleted_at')->paginate(15);

        return Inertia::render('hotel::Dashboard/V1/Amenity/Trash', [
            'amenities' => AmenityResource::collection($amenities)->response()->getData(true),
        ]);
    }

    public function restore(string $uuid): RedirectResponse
    {
        $amenity = Amenity::onlyTrashed()->where('uuid', $uuid)->first();

        if ($amenity) {
            $amenity->restore();
        }

        return redirect()->back()->with('success', 'Amenity restored.');
    }

    public function forceDelete(string $uuid): RedirectResponse
    {
        $amenity = Amenity::onlyTrashed()->where('uuid', $uuid)->first();

        if ($amenity) {
            $amenity->forceDelete();
        }

        return redirect()->back()->with('success', 'Amenity permanently deleted.');
    }

    public function bulkDelete(BulkDeleteAmenitiesRequest $request, BulkDeleteAmenitiesAction $action): RedirectResponse
    {
        $action->execute($request->validated()['uuids']);

        return redirect()
            ->route('hotel.amenities.index')
            ->with('success', 'Selected amenities deleted.');
    }
}
