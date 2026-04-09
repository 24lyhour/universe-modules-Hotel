<?php

namespace Modules\Hotel\Http\Controllers\Dashboard\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Momentum\Modal\Modal;
use Modules\Hotel\Enums\RoomStatusEnum;
use Modules\Hotel\Http\Requests\StoreRoomRequest;
use Modules\Hotel\Http\Requests\UpdateRoomRequest;
use Modules\Hotel\Http\Resources\RoomResource;
use Modules\Hotel\Models\Hotel;
use Modules\Hotel\Models\Room;
use Modules\Hotel\Services\RoomService;

class RoomController extends Controller
{
    public function __construct(
        protected RoomService $roomService
    ) {}

    public function allRooms(): Response
    {
        $filters = request()->only(['search', 'status', 'hotel', 'is_available']);
        $query = Room::query()->with('hotel');

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('room_type', 'like', "%{$search}%")
                    ->orWhere('room_number', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['hotel'])) {
            $query->where('hotel_id', $filters['hotel']);
        }

        if (isset($filters['is_available']) && $filters['is_available'] !== '') {
            $query->where('is_available', $filters['is_available']);
        }

        $rooms = $query->latest()->paginate(15);
        $hotels = Hotel::orderBy('name')->get(['id', 'uuid', 'name']);

        return Inertia::render('hotel::Dashboard/V1/Room/Index', [
            'hotel' => null,
            'rooms' => RoomResource::collection($rooms)->response()->getData(true),
            'filters' => $filters,
            'statuses' => RoomStatusEnum::options(),
            'hotels' => $hotels,
            'stats' => [
                'total' => Room::count(),
                'active' => Room::where('status', 'active')->count(),
                'available' => Room::where('is_available', true)->count(),
                'maintenance' => Room::where('status', 'maintenance')->count(),
                'trashed' => Room::onlyTrashed()->count(),
            ],
        ]);
    }

    public function index(Hotel $hotel): Response
    {
        $filters = request()->only(['search', 'status', 'room_type', 'is_available', 'min_price', 'max_price']);
        $rooms = $this->roomService->paginate($hotel, 15, $filters);

        return Inertia::render('hotel::Dashboard/V1/Room/Index', [
            'hotel' => $hotel->only(['id', 'uuid', 'name']),
            'rooms' => RoomResource::collection($rooms)->response()->getData(true),
            'filters' => $filters,
            'statuses' => RoomStatusEnum::options(),
        ]);
    }

    public function create(Hotel $hotel): Modal
    {
        return Inertia::modal('hotel::Dashboard/V1/Room/Create', [
            'hotel' => $hotel->only(['id', 'uuid', 'name']),
            'statuses' => RoomStatusEnum::options(),
        ])->baseRoute('hotel.hotels.rooms.index', ['hotel' => $hotel]);
    }

    public function store(StoreRoomRequest $request, Hotel $hotel): RedirectResponse
    {
        $this->roomService->create($hotel, $request->validated());

        return redirect()
            ->route('hotel.hotels.rooms.index', ['hotel' => $hotel])
            ->with('success', 'Room created successfully.');
    }

    public function show(Hotel $hotel, Room $room): Response
    {
        $room->load('hotel');

        return Inertia::render('hotel::Dashboard/V1/Room/Show', [
            'hotel' => $hotel->only(['id', 'uuid', 'name']),
            'room' => (new RoomResource($room))->resolve(),
        ]);
    }

    public function edit(Hotel $hotel, Room $room): Modal
    {
        return Inertia::modal('hotel::Dashboard/V1/Room/Edit', [
            'hotel' => $hotel->only(['id', 'uuid', 'name']),
            'room' => (new RoomResource($room))->resolve(),
            'statuses' => RoomStatusEnum::options(),
        ])->baseRoute('hotel.hotels.rooms.index', ['hotel' => $hotel]);
    }

    public function update(UpdateRoomRequest $request, Hotel $hotel, Room $room): RedirectResponse
    {
        $this->roomService->update($room, $request->validated());

        return redirect()
            ->route('hotel.hotels.rooms.index', ['hotel' => $hotel])
            ->with('success', 'Room updated successfully.');
    }

    public function destroy(Hotel $hotel, Room $room): RedirectResponse
    {
        $this->roomService->delete($room);

        return redirect()
            ->route('hotel.hotels.rooms.index', ['hotel' => $hotel])
            ->with('success', 'Room deleted successfully.');
    }

    public function toggleAvailability(Hotel $hotel, Room $room): RedirectResponse
    {
        $this->roomService->toggleAvailability($room);

        return redirect()->back()->with('success', 'Room availability updated.');
    }

    // Trash

    public function trash(Hotel $hotel): Response
    {
        $rooms = $this->roomService->getTrashed($hotel);

        return Inertia::render('hotel::Dashboard/V1/Room/Trash', [
            'hotel' => $hotel->only(['id', 'uuid', 'name']),
            'rooms' => RoomResource::collection($rooms)->response()->getData(true),
        ]);
    }

    public function restore(Hotel $hotel, string $uuid): RedirectResponse
    {
        $this->roomService->restore($uuid);

        return redirect()->back()->with('success', 'Room restored successfully.');
    }

    public function forceDelete(Hotel $hotel, string $uuid): RedirectResponse
    {
        $this->roomService->forceDelete($uuid);

        return redirect()->back()->with('success', 'Room permanently deleted.');
    }

    public function bulkDelete(Hotel $hotel): RedirectResponse
    {
        $uuids = request('uuids', []);
        $this->roomService->bulkDelete($uuids);

        return redirect()
            ->route('hotel.hotels.rooms.index', ['hotel' => $hotel])
            ->with('success', 'Selected rooms deleted.');
    }

    // Standalone Trash (all rooms)

    public function createRoom(): Modal
    {
        $hotels = Hotel::orderBy('name')->get(['id', 'uuid', 'name']);

        return Inertia::modal('hotel::Dashboard/V1/Room/Create', [
            'hotel' => null,
            'hotels' => $hotels,
            'statuses' => RoomStatusEnum::options(),
        ])->baseRoute('hotel.rooms.index');
    }

    public function storeRoom(StoreRoomRequest $request): RedirectResponse
    {
        $hotel = Hotel::where('uuid', $request->input('hotel_uuid'))->firstOrFail();
        $this->roomService->create($hotel, $request->validated());

        return redirect()
            ->route('hotel.rooms.index')
            ->with('success', 'Room created successfully.');
    }

    public function allTrashed(): Response
    {
        $rooms = Room::onlyTrashed()->with('hotel')->latest('deleted_at')->paginate(15);

        return Inertia::render('hotel::Dashboard/V1/Room/Trash', [
            'hotel' => null,
            'rooms' => RoomResource::collection($rooms)->response()->getData(true),
        ]);
    }

    public function restoreRoom(string $uuid): RedirectResponse
    {
        $this->roomService->restore($uuid);

        return redirect()->back()->with('success', 'Room restored.');
    }

    public function forceDeleteRoom(string $uuid): RedirectResponse
    {
        $this->roomService->forceDelete($uuid);

        return redirect()->back()->with('success', 'Room permanently deleted.');
    }
}
