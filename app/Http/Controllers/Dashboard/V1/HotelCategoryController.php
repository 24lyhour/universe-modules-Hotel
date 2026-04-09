<?php

namespace Modules\Hotel\Http\Controllers\Dashboard\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Momentum\Modal\Modal;
use Modules\Hotel\Actions\Dashboard\V1\HotelCategoryAction\BulkDeleteHotelCategoriesAction;
use Modules\Hotel\Actions\Dashboard\V1\HotelCategoryAction\CreateHotelCategoryAction;
use Modules\Hotel\Actions\Dashboard\V1\HotelCategoryAction\DeleteHotelCategoryAction;
use Modules\Hotel\Actions\Dashboard\V1\HotelCategoryAction\GetHotelCategoryEditDataAction;
use Modules\Hotel\Actions\Dashboard\V1\HotelCategoryAction\GetHotelCategoryIndexDataAction;
use Modules\Hotel\Actions\Dashboard\V1\HotelCategoryAction\ToggleHotelCategoryStatusAction;
use Modules\Hotel\Actions\Dashboard\V1\HotelCategoryAction\UpdateHotelCategoryAction;
use Modules\Hotel\Http\Requests\Dashboard\V1\HotelCategoryRequest\BulkDeleteHotelCategoriesRequest;
use Modules\Hotel\Http\Requests\Dashboard\V1\HotelCategoryRequest\StoreHotelCategoryRequest;
use Modules\Hotel\Http\Requests\Dashboard\V1\HotelCategoryRequest\UpdateHotelCategoryRequest;
use Modules\Hotel\Http\Resources\HotelCategoryResource;
use Modules\Hotel\Models\HotelCategory;

class HotelCategoryController extends Controller
{
    public function index(Request $request, GetHotelCategoryIndexDataAction $action): Response
    {
        $perPage = $request->input('per_page', 10);
        $filters = $request->only(['search', 'is_active']);

        return Inertia::render('hotel::Dashboard/V1/HotelCategory/Index', $action->execute($perPage, $filters));
    }

    public function create(): Modal
    {
        return Inertia::modal('hotel::Dashboard/V1/HotelCategory/Create')
            ->baseRoute('hotel.categories.index');
    }

    public function store(StoreHotelCategoryRequest $request, CreateHotelCategoryAction $action): RedirectResponse
    {
        $action->execute($request->validated());

        return redirect()
            ->route('hotel.categories.index')
            ->with('success', 'Category created successfully.');
    }

    public function edit(HotelCategory $category, GetHotelCategoryEditDataAction $action): Modal
    {
        return Inertia::modal('hotel::Dashboard/V1/HotelCategory/Edit', $action->execute($category))
            ->baseRoute('hotel.categories.index');
    }

    public function confirmDelete(HotelCategory $category, GetHotelCategoryEditDataAction $action): Modal
    {
        return Inertia::modal('hotel::Dashboard/V1/HotelCategory/Delete', $action->execute($category))
            ->baseRoute('hotel.categories.index');
    }

    public function update(UpdateHotelCategoryRequest $request, HotelCategory $category, UpdateHotelCategoryAction $action): RedirectResponse
    {
        $action->execute($category, $request->validated());

        return redirect()
            ->route('hotel.categories.index')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(HotelCategory $category, DeleteHotelCategoryAction $action): RedirectResponse
    {
        $action->execute($category);

        return redirect()
            ->route('hotel.categories.index')
            ->with('success', 'Category deleted successfully.');
    }

    public function toggleActive(HotelCategory $category, ToggleHotelCategoryStatusAction $action): RedirectResponse
    {
        $action->execute($category);

        return redirect()->back()->with('success', 'Category status updated.');
    }

    // Trash

    public function trash(): Response
    {
        $categories = HotelCategory::onlyTrashed()->latest('deleted_at')->paginate(15);

        return Inertia::render('hotel::Dashboard/V1/HotelCategory/Trash', [
            'categories' => HotelCategoryResource::collection($categories)->response()->getData(true),
        ]);
    }

    public function restore(string $uuid): RedirectResponse
    {
        $category = HotelCategory::onlyTrashed()->where('uuid', $uuid)->first();

        if ($category) {
            $category->restore();
        }

        return redirect()->back()->with('success', 'Category restored.');
    }

    public function forceDelete(string $uuid): RedirectResponse
    {
        $category = HotelCategory::onlyTrashed()->where('uuid', $uuid)->first();

        if ($category) {
            $category->forceDelete();
        }

        return redirect()->back()->with('success', 'Category permanently deleted.');
    }

    public function bulkDelete(BulkDeleteHotelCategoriesRequest $request, BulkDeleteHotelCategoriesAction $action): RedirectResponse
    {
        $action->execute($request->validated()['uuids']);

        return redirect()
            ->route('hotel.categories.index')
            ->with('success', 'Selected categories deleted.');
    }
}
