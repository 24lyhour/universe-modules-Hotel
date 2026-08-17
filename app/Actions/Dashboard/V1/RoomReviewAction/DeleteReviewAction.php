<?php

namespace Modules\Hotel\Actions\Dashboard\V1\RoomReviewAction;

use Modules\Hotel\Models\RoomReview;

class DeleteReviewAction
{
    public function execute(RoomReview $review): bool
    {
        return $review->delete();
    }
}
