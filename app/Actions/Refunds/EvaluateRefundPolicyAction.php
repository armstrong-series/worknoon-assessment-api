<?php

namespace App\Actions\Refunds;

use App\Models\RefundRequest;
use Carbon\Carbon;

class EvaluateRefundPolicyAction
{
    public function execute(RefundRequest $refundRequest, array $analysis): array
    {
        $order = $refundRequest->order;
        $reasons = [];

        if ($order->ordered_at->lt(
            Carbon::now()->subDays(
                config('refunds.max_age_days')
            )
        )) {
            $reasons[] = 'Order is outside the eligible refund period.';
        }

        if ($order->items->contains('is_final_sale', true)) {
            $reasons[] = 'The order contains a final sale item.';
        }

        if (
            $refundRequest->requested_amount_cents >
            config('refunds.human_review_threshold_cents')
        ) {
            $reasons[] = 'Refund exceeds the automatic approval threshold.';
        }

        if (
            ($analysis['suspicious'] ?? false) === true
        ) {
            $reasons[] = 'The request has been flagged as suspicious.';
        }

        if (
            ($analysis['conflicts_with_order_data'] ?? false) === true
        ) {
            $reasons[] = 'The customer request conflicts with order data.';
        }

        return [
            'eligible' => empty($reasons),
            'reasons' => $reasons,
            'requires_human_review' => $this->requiresHumanReview(
                $refundRequest,
                $analysis,
            ),
        ];
    }

    private function requiresHumanReview(
        RefundRequest $refundRequest,
        array $analysis,
    ): bool {
        return
            $refundRequest->requested_amount_cents >
            config('refunds.human_review_threshold_cents')
            || ($analysis['suspicious'] ?? false)
            || ($analysis['conflicts_with_order_data'] ?? false);
    }
}
