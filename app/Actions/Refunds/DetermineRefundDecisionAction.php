<?php

namespace App\Actions\Refunds;

use App\Enums\RefundDecision;
use App\Models\RefundRequest;

class DetermineRefundDecisionAction
{

    public function execute(RefundRequest $refundRequest, array $analysis, array $policyResult): array
    {
        if ($policyResult['requires_human_review']) {
            return [
                'decision' => RefundDecision::ESCALATED->value,
                'reason_code' => 'human_review_required',
                'reason' => implode(
                    ' ',
                    $policyResult['reasons']
                ),
            ];
        }

        if (! $policyResult['eligible']) {
            return [
                'decision' => RefundDecision::DENIED->value,
                'reason_code' => 'policy_not_satisfied',
                'reason' => implode(
                    ' ',
                    $policyResult['reasons']
                ),
            ];
        }

        return [
            'decision'    => RefundDecision::APPROVED->value,
            'reason_code' => 'policy_eligible',
            'reason'      => 'The refund request satisfies the configured refund policy.',
        ];
    }
}
