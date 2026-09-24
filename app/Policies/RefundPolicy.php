<?php

namespace App\Policies;

final class RefundPolicy
{
    public function maximumRefundAgeDays(): int
    {
        return config('refunds.max_age_days', 30);
    }

    public function humanReviewThreshold(): int
    {
        return config('refunds.human_review_threshold', 50000);
    }
}
