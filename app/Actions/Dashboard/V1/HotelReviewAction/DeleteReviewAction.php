<?php

namespace Modules\Hotel\Actions\Dashboard\V1\HotelReviewAction;

use Modules\Hotel\Models\HotelReview;

class DeleteReviewAction
{
    public function execute(HotelReview $review): bool
    {
        return $review->delete();
    }
}
