<?php

namespace Modules\Hotel\Http\Controllers\Dashboard\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Momentum\Modal\Modal;
use Modules\Hotel\Actions\Dashboard\V1\RoomPoliciesAction\BulkDeleteRoomPoliciesAction;
use Modules\Hotel\Actions\Dashboard\V1\RoomPoliciesAction\CreateRoomPolicyAction;
use Modules\Hotel\Actions\Dashboard\V1\RoomPoliciesAction\DeleteRoomPolicyAction;
use Modules\Hotel\Actions\Dashboard\V1\RoomPoliciesAction\GetRoomPolicyEditDataAction;
use Modules\Hotel\Actions\Dashboard\V1\RoomPoliciesAction\GetRoomPolicyIndexDataAction;
use Modules\Hotel\Actions\Dashboard\V1\RoomPoliciesAction\ToggleRoomPolicyStatusAction;
use Modules\Hotel\Actions\Dashboard\V1\RoomPoliciesAction\UpdateRoomPolicyAction;
use Modules\Hotel\Http\Requests\Dashboard\V1\RoomPolicyRequest\BulkDeleteRoomPoliciesRequest;
use Modules\Hotel\Http\Requests\Dashboard\V1\RoomPolicyRequest\StoreRoomPolicyRequest;
use Modules\Hotel\Http\Requests\Dashboard\V1\RoomPolicyRequest\UpdateRoomPolicyRequest;
use Modules\Hotel\Http\Resources\RoomPolicyResource;
use Modules\Hotel\Models\RoomPolicies;

class RoomPolicyController extends Controller
{
    public function index(Request $request, GetRoomPolicyIndexDataAction $action): Response
    {
        $perPage = $request->input('per_page', 10);
        $filters = $request->only(['search', 'is_active']);

        return Inertia::render('hotel::Dashboard/V1/RoomPolicies/Index', $action->execute($perPage, $filters));
    }

    public function create(): Modal
    {
        return Inertia::modal('hotel::Dashboard/V1/RoomPolicies/Create')
            ->baseRoute('hotel.room-policies.index');
    }

    public function store(StoreRoomPolicyRequest $request, CreateRoomPolicyAction $action): RedirectResponse
    {
        $action->execute($request->validated());

        return redirect()
            ->route('hotel.room-policies.index')
            ->with('success', 'Room policy created successfully.');
    }

    public function edit(RoomPolicies $policy, GetRoomPolicyEditDataAction $action): Modal
    {
        return Inertia::modal('hotel::Dashboard/V1/RoomPolicies/Edit', $action->execute($policy))
            ->baseRoute('hotel.room-policies.index');
    }

    public function confirmDelete(RoomPolicies $policy, GetRoomPolicyEditDataAction $action): Modal
    {
        return Inertia::modal('hotel::Dashboard/V1/RoomPolicies/Delete', $action->execute($policy))
            ->baseRoute('hotel.room-policies.index');
    }

    public function update(UpdateRoomPolicyRequest $request, RoomPolicies $policy, UpdateRoomPolicyAction $action): RedirectResponse
    {
        $action->execute($policy, $request->validated());

        return redirect()
            ->route('hotel.room-policies.index')
            ->with('success', 'Room policy updated successfully.');
    }

    public function destroy(RoomPolicies $policy, DeleteRoomPolicyAction $action): RedirectResponse
    {
        $action->execute($policy);

        return redirect()
            ->route('hotel.room-policies.index')
            ->with('success', 'Room policy deleted successfully.');
    }

    public function toggleActive(RoomPolicies $policy, ToggleRoomPolicyStatusAction $action): RedirectResponse
    {
        $action->execute($policy);

        return redirect()->back()->with('success', 'Room policy status updated.');
    }

    // Trash

    public function trash(): Response
    {
        $policies = RoomPolicies::onlyTrashed()->latest('deleted_at')->paginate(15);

        return Inertia::render('hotel::Dashboard/V1/RoomPolicies/Trash', [
            'policies' => RoomPolicyResource::collection($policies)->response()->getData(true),
        ]);
    }

    public function restore(string $uuid): RedirectResponse
    {
        $policy = RoomPolicies::onlyTrashed()->where('uuid', $uuid)->first();

        if ($policy) {
            $policy->restore();
        }

        return redirect()->back()->with('success', 'Room policy restored.');
    }

    public function forceDelete(string $uuid): RedirectResponse
    {
        $policy = RoomPolicies::onlyTrashed()->where('uuid', $uuid)->first();

        if ($policy) {
            $policy->forceDelete();
        }

        return redirect()->back()->with('success', 'Room policy permanently deleted.');
    }

    public function bulkDelete(BulkDeleteRoomPoliciesRequest $request, BulkDeleteRoomPoliciesAction $action): RedirectResponse
    {
        $action->execute($request->validated()['uuids']);

        return redirect()
            ->route('hotel.room-policies.index')
            ->with('success', 'Selected policies deleted.');
    }
}
