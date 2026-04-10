<?php

namespace Modules\Hotel\Http\Controllers\Dashboard\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Momentum\Modal\Modal;
use Modules\Hotel\Actions\Dashboard\V1\HotelReviewAction\DeleteReviewAction;
use Modules\Hotel\Actions\Dashboard\V1\HotelReviewAction\GetHotelReviewIndexDataAction;
use Modules\Hotel\Actions\Dashboard\V1\HotelReviewAction\ReplyReviewAction;
use Modules\Hotel\Actions\Dashboard\V1\HotelReviewAction\UpdateReviewStatusAction;
use Modules\Hotel\Enums\HotelReviewEnum;
use Modules\Hotel\Http\Resources\HotelReviewResource;
use Modules\Hotel\Models\HotelReview;

class HotelReviewController extends Controller
{
    public function index(Request $request, GetHotelReviewIndexDataAction $action): Response
    {
        $perPage = $request->input('per_page', 10);
        $filters = $request->only(['search', 'status', 'rating', 'hotel']);

        return Inertia::render('hotel::Dashboard/V1/HotelReview/Index', $action->execute($perPage, $filters));
    }

    public function show(HotelReview $review): Response
    {
        $review->load(['hotel', 'user']);

        return Inertia::render('hotel::Dashboard/V1/HotelReview/Show', [
            'review' => (new HotelReviewResource($review))->resolve(),
        ]);
    }

    public function reply(Request $request, HotelReview $review, ReplyReviewAction $action): RedirectResponse
    {
        $validated = $request->validate([
            'reply' => ['required', 'string', 'max:2000'],
        ]);

        $action->execute($review, $validated['reply']);

        return redirect()->back()->with('success', 'Reply sent.');
    }

    public function updateStatus(Request $request, HotelReview $review, UpdateReviewStatusAction $action): RedirectResponse
    {
        $status = HotelReviewEnum::from($request->input('status'));
        $action->execute($review, $status);

        return redirect()->back()->with('success', 'Review status updated.');
    }

    public function confirmDelete(HotelReview $review): Modal
    {
        $review->load(['hotel', 'user']);

        return Inertia::modal('hotel::Dashboard/V1/HotelReview/Delete', [
            'review' => (new HotelReviewResource($review))->resolve(),
        ])->baseRoute('hotel.reviews.index');
    }

    public function destroy(HotelReview $review, DeleteReviewAction $action): RedirectResponse
    {
        $action->execute($review);

        return redirect()
            ->route('hotel.reviews.index')
            ->with('success', 'Review deleted.');
    }

    // Trash

    public function trash(): Response
    {
        $reviews = HotelReview::onlyTrashed()->with(['hotel', 'user'])->latest('deleted_at')->paginate(15);

        return Inertia::render('hotel::Dashboard/V1/HotelReview/Trash', [
            'reviews' => HotelReviewResource::collection($reviews)->response()->getData(true),
        ]);
    }

    public function restore(string $uuid): RedirectResponse
    {
        $review = HotelReview::onlyTrashed()->where('uuid', $uuid)->first();
        if ($review) $review->restore();

        return redirect()->back()->with('success', 'Review restored.');
    }

    public function forceDelete(string $uuid): RedirectResponse
    {
        $review = HotelReview::onlyTrashed()->where('uuid', $uuid)->first();
        if ($review) $review->forceDelete();

        return redirect()->back()->with('success', 'Review permanently deleted.');
    }
}
