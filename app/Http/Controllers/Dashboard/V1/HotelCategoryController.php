<?php

namespace Modules\Hotel\Http\Controllers\Dashboard\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Based\Momentum\Modal;
use Modules\Hotel\Http\Resources\HotelCategoryResource;
use Modules\Hotel\Models\HotelCategory;
use Modules\Hotel\Services\HotelCategoryService;

class HotelCategoryController extends Controller
{
    public function __construct(
        protected HotelCategoryService $categoryService
    ) {}

    public function index(): Response
    {
        $filters = request()->only(['search', 'is_active']);
        $categories = $this->categoryService->paginate(15, $filters);

        return Inertia::render('hotel::Dashboard/V1/HotelCategory/Index', [
            'categories' => HotelCategoryResource::collection($categories)->response()->getData(true),
            'filters' => $filters,
        ]);
    }

    public function create(): Modal
    {
        return Inertia::modal('hotel::Dashboard/V1/HotelCategory/Create')
            ->baseRoute('hotel.categories.index');
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

        $this->categoryService->create($validated);

        return redirect()
            ->route('hotel.categories.index')
            ->with('success', 'Category created successfully.');
    }

    public function edit(HotelCategory $category): Modal
    {
        return Inertia::modal('hotel::Dashboard/V1/HotelCategory/Edit', [
            'category' => new HotelCategoryResource($category),
        ])->baseRoute('hotel.categories.index');
    }

    public function update(HotelCategory $category): RedirectResponse
    {
        $validated = request()->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:50'],
            'group' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $this->categoryService->update($category, $validated);

        return redirect()
            ->route('hotel.categories.index')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(HotelCategory $category): RedirectResponse
    {
        $this->categoryService->delete($category);

        return redirect()
            ->route('hotel.categories.index')
            ->with('success', 'Category deleted successfully.');
    }

    public function toggleActive(HotelCategory $category): RedirectResponse
    {
        $this->categoryService->toggleActive($category);

        return redirect()->back()->with('success', 'Category status updated.');
    }

    // Trash

    public function trash(): Response
    {
        $categories = $this->categoryService->getTrashed();

        return Inertia::render('hotel::Dashboard/V1/HotelCategory/Trash', [
            'categories' => HotelCategoryResource::collection($categories)->response()->getData(true),
        ]);
    }

    public function restore(string $uuid): RedirectResponse
    {
        $this->categoryService->restore($uuid);

        return redirect()->back()->with('success', 'Category restored.');
    }

    public function forceDelete(string $uuid): RedirectResponse
    {
        $this->categoryService->forceDelete($uuid);

        return redirect()->back()->with('success', 'Category permanently deleted.');
    }

    public function bulkDelete(): RedirectResponse
    {
        $uuids = request('uuids', []);
        $this->categoryService->bulkDelete($uuids);

        return redirect()
            ->route('hotel.categories.index')
            ->with('success', 'Selected categories deleted.');
    }
}
