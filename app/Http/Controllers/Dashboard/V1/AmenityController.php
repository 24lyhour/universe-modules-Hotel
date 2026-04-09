<?php

namespace Modules\Hotel\Http\Controllers\Dashboard\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Momentum\Modal\Modal;
use Modules\Hotel\Http\Resources\AmenityResource;
use Modules\Hotel\Models\Amenity;
use Modules\Hotel\Services\AmenityService;

class AmenityController extends Controller
{
    public function __construct(
        protected AmenityService $amenityService
    ) {}

    public function index(Request $request): Response
    {
        $perPage = $request->input('per_page', 10);
        $filters = $request->only(['search', 'is_active', 'group']);
        $amenities = $this->amenityService->paginate($perPage, $filters);

        return Inertia::render('hotel::Dashboard/V1/Amenity/Index', [
            'amenities' => AmenityResource::collection($amenities)->response()->getData(true),
            'filters' => $filters,
            'stats' => [
                'total' => \Modules\Hotel\Models\Amenity::count(),
                'active' => \Modules\Hotel\Models\Amenity::where('is_active', true)->count(),
                'inactive' => \Modules\Hotel\Models\Amenity::where('is_active', false)->count(),
                'trashed' => \Modules\Hotel\Models\Amenity::onlyTrashed()->count(),
            ],
        ]);
    }

    public function create(): Modal
    {
        return Inertia::modal('hotel::Dashboard/V1/Amenity/Create')
            ->baseRoute('hotel.amenities.index');
    }

    public function store(): RedirectResponse
    {
        $validated = request()->validate([
            'name' => ['required', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:50'],
            'group' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $this->amenityService->create($validated);

        return redirect()
            ->route('hotel.amenities.index')
            ->with('success', 'Amenity created successfully.');
    }

    public function edit(Amenity $amenity): Modal
    {
        return Inertia::modal('hotel::Dashboard/V1/Amenity/Edit', [
            'amenity' => new AmenityResource($amenity),
        ])->baseRoute('hotel.amenities.index');
    }

    public function update(Amenity $amenity): RedirectResponse
    {
        $validated = request()->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:50'],
            'group' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $this->amenityService->update($amenity, $validated);

        return redirect()
            ->route('hotel.amenities.index')
            ->with('success', 'Amenity updated successfully.');
    }

    public function destroy(Amenity $amenity): RedirectResponse
    {
        $this->amenityService->delete($amenity);

        return redirect()
            ->route('hotel.amenities.index')
            ->with('success', 'Amenity deleted successfully.');
    }

    public function toggleActive(Amenity $amenity): RedirectResponse
    {
        $this->amenityService->toggleActive($amenity);

        return redirect()->back()->with('success', 'Amenity status updated.');
    }

    // Trash

    public function trash(): Response
    {
        $amenities = $this->amenityService->getTrashed();

        return Inertia::render('hotel::Dashboard/V1/Amenity/Trash', [
            'amenities' => AmenityResource::collection($amenities)->response()->getData(true),
        ]);
    }

    public function restore(string $uuid): RedirectResponse
    {
        $this->amenityService->restore($uuid);

        return redirect()->back()->with('success', 'Amenity restored.');
    }

    public function forceDelete(string $uuid): RedirectResponse
    {
        $this->amenityService->forceDelete($uuid);

        return redirect()->back()->with('success', 'Amenity permanently deleted.');
    }

    public function bulkDelete(): RedirectResponse
    {
        $uuids = request('uuids', []);
        $this->amenityService->bulkDelete($uuids);

        return redirect()
            ->route('hotel.amenities.index')
            ->with('success', 'Selected amenities deleted.');
    }
}
