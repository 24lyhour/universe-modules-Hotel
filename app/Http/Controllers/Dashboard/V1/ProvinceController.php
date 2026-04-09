<?php

namespace Modules\Hotel\Http\Controllers\Dashboard\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Momentum\Modal\Modal;
use Modules\Hotel\Actions\Dashboard\V1\ProvinceAction\CreateProvinceAction;
use Modules\Hotel\Actions\Dashboard\V1\ProvinceAction\DeleteProvinceAction;
use Modules\Hotel\Actions\Dashboard\V1\ProvinceAction\GetProvinceEditDataAction;
use Modules\Hotel\Actions\Dashboard\V1\ProvinceAction\GetProvinceIndexDataAction;
use Modules\Hotel\Actions\Dashboard\V1\ProvinceAction\ToggleProvinceStatusAction;
use Modules\Hotel\Actions\Dashboard\V1\ProvinceAction\UpdateProvinceAction;
use Modules\Hotel\Http\Requests\Dashboard\V1\ProvinceRequest\StoreProvinceRequest;
use Modules\Hotel\Http\Requests\Dashboard\V1\ProvinceRequest\UpdateProvinceRequest;
use Modules\Hotel\Models\Province;

class ProvinceController extends Controller
{
    public function index(Request $request, GetProvinceIndexDataAction $action): Response
    {
        $perPage = $request->input('per_page', 10);
        $filters = $request->only(['search', 'is_active', 'region']);

        return Inertia::render('hotel::Dashboard/V1/Province/Index', $action->execute($perPage, $filters));
    }

    public function create(): Modal
    {
        return Inertia::modal('hotel::Dashboard/V1/Province/Create')
            ->baseRoute('hotel.provinces.index');
    }

    public function store(StoreProvinceRequest $request, CreateProvinceAction $action): RedirectResponse
    {
        $action->execute($request->validated());

        return redirect()
            ->route('hotel.provinces.index')
            ->with('success', 'Province created successfully.');
    }

    public function edit(Province $province, GetProvinceEditDataAction $action): Modal
    {
        return Inertia::modal('hotel::Dashboard/V1/Province/Edit', $action->execute($province))
            ->baseRoute('hotel.provinces.index');
    }

    public function confirmDelete(Province $province, GetProvinceEditDataAction $action): Modal
    {
        return Inertia::modal('hotel::Dashboard/V1/Province/Delete', $action->execute($province))
            ->baseRoute('hotel.provinces.index');
    }

    public function update(UpdateProvinceRequest $request, Province $province, UpdateProvinceAction $action): RedirectResponse
    {
        $action->execute($province, $request->validated());

        return redirect()
            ->route('hotel.provinces.index')
            ->with('success', 'Province updated successfully.');
    }

    public function destroy(Province $province, DeleteProvinceAction $action): RedirectResponse
    {
        $action->execute($province);

        return redirect()
            ->route('hotel.provinces.index')
            ->with('success', 'Province deleted successfully.');
    }

    public function toggleActive(Province $province, ToggleProvinceStatusAction $action): RedirectResponse
    {
        $action->execute($province);

        return redirect()->back()->with('success', 'Province status updated.');
    }
}
