<?php

namespace App\Actions\Refunds;

use App\Models\RefundRequest;
use App\Notifications\RefundApprovedNotification;
use Illuminate\Validation\ValidationException;
use App\Enums\RefundDecision;

class DecideRefundRequestAction
{


    public function execute(string $refundRequestId, array $attributes): RefundRequest
    {
        $refundRequest = RefundRequest::query()
            ->with(
                [
                    'user',
                    'customer',
                    'order.items',
                ]
            )->find($refundRequestId);

        if (!$refundRequest) {
            throw ValidationException::withMessages([
                'refund_request' => [
                    'The refund request could not be found.',
                ],
            ]);
        }

        if ($refundRequest->status->value !== 'processed') {
            throw ValidationException::withMessages([
                'status' => [
                    'Only processed refund requests can be approved or denied.',
                ],
            ]);
        }

        $decision = $attributes['decision'];

        $refundRequest->update(
            [
                'decision' => $decision,
                'reason_code' => $decision ===  RefundDecision::APPROVED->value
                    ? 'approved_by_support'
                    : 'denied_by_support',
                'decision_reason' => $decision ===  RefundDecision::APPROVED->value
                    ? 'Refund approved by support.'
                    : 'Refund denied by support.',
            ]
        );

        $refundRequest->refresh()->load(
            [
                'user',
                'customer',
                'order.items',
            ]
        );

        if ($decision === RefundDecision::APPROVED->value) {
            $refundRequest->user->notify(
                new RefundApprovedNotification($refundRequest)
            );
        }

        return $refundRequest;
    }
}
