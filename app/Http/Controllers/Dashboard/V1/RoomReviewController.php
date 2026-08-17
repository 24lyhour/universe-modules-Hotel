<?php

namespace Modules\Hotel\Http\Controllers\Dashboard\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Momentum\Modal\Modal;
use Modules\Hotel\Actions\Dashboard\V1\RoomReviewAction\DeleteReviewAction;
use Modules\Hotel\Actions\Dashboard\V1\RoomReviewAction\GetRoomReviewIndexDataAction;
use Modules\Hotel\Actions\Dashboard\V1\RoomReviewAction\ReplyReviewAction;
use Modules\Hotel\Http\Resources\RoomReviewResource;
use Modules\Hotel\Models\RoomReview;

class RoomReviewController extends Controller
{
    public function index(Request $request, GetRoomReviewIndexDataAction $action): Response
    {
        $perPage = $request->input('per_page', 10);
        $filters = $request->only(['search', 'is_active', 'rating', 'room']);

        return Inertia::render('hotel::Dashboard/V1/RoomReview/Index', $action->execute($perPage, $filters));
    }

    public function show(RoomReview $review): Response
    {
        $review->load(['room.hotel', 'customer']);

        return Inertia::render('hotel::Dashboard/V1/RoomReview/Show', [
            'review' => (new RoomReviewResource($review))->resolve(),
        ]);
    }

    public function reply(Request $request, RoomReview $review, ReplyReviewAction $action): RedirectResponse
    {
        $validated = $request->validate([
            'reply' => ['required', 'string', 'max:2000'],
        ]);

        $action->execute($review, $validated['reply']);

        return redirect()->back()->with('success', 'Reply sent.');
    }

    public function toggleActive(Request $request, RoomReview $review): RedirectResponse
    {
        $validated = $request->validate([
            'is_active' => ['required', 'boolean'],
        ]);

        $review->update(['is_active' => $validated['is_active']]);

        return redirect()->back()->with('success', 'Review status updated.');
    }

    public function confirmDelete(RoomReview $review): Modal
    {
        $review->load(['room.hotel', 'customer']);

        return Inertia::modal('hotel::Dashboard/V1/RoomReview/Delete', [
            'review' => (new RoomReviewResource($review))->resolve(),
        ])->baseRoute('hotel.room-reviews.index');
    }

    public function destroy(RoomReview $review, DeleteReviewAction $action): RedirectResponse
    {
        $action->execute($review);

        return redirect()
            ->route('hotel.room-reviews.index')
            ->with('success', 'Review deleted.');
    }

    // Trash

    public function trash(): Response
    {
        $reviews = RoomReview::onlyTrashed()->with(['room.hotel', 'customer'])->latest('deleted_at')->paginate(15);

        return Inertia::render('hotel::Dashboard/V1/RoomReview/Trash', [
            'reviews' => RoomReviewResource::collection($reviews)->response()->getData(true),
        ]);
    }

    public function restore(string $uuid): RedirectResponse
    {
        $review = RoomReview::onlyTrashed()->where('uuid', $uuid)->first();
        if ($review) $review->restore();

        return redirect()->back()->with('success', 'Review restored.');
    }

    public function forceDelete(string $uuid): RedirectResponse
    {
        $review = RoomReview::onlyTrashed()->where('uuid', $uuid)->first();
        if ($review) $review->forceDelete();

        return redirect()->back()->with('success', 'Review permanently deleted.');
    }
}
