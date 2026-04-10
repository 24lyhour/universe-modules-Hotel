<?php

namespace Modules\Hotel\Actions\Dashboard\V1\HotelReviewAction;

use Modules\Hotel\Models\HotelReview;

class ReplyReviewAction
{
    public function execute(HotelReview $review, string $reply): HotelReview
    {
        $review->addReply($reply);

        return $review;
    }
}
