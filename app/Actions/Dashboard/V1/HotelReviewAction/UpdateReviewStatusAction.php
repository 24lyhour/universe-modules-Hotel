<?php

namespace Modules\Hotel\Actions\Dashboard\V1\HotelReviewAction;

use Modules\Hotel\Enums\HotelReviewEnum;
use Modules\Hotel\Models\HotelReview;

class UpdateReviewStatusAction
{
    public function execute(HotelReview $review, HotelReviewEnum $status): HotelReview
    {
        $review->status = $status;
        $review->save();

        return $review;
    }
}
