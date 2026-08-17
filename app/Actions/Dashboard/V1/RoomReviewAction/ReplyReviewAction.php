<?php

namespace Modules\Hotel\Actions\Dashboard\V1\RoomReviewAction;

use Modules\Hotel\Models\RoomReview;

class ReplyReviewAction
{
    public function execute(RoomReview $review, string $reply): RoomReview
    {
        $review->addReply($reply);

        return $review;
    }
}
