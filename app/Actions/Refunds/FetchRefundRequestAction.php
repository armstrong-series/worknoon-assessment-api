<?php

namespace App\Actions\Refunds;

use App\Models\RefundRequest;


class FetchRefundRequestAction
{

    public function execute(RefundRequest $refundRequest): RefundRequest
    {
        return $refundRequest->load(
            [
                'user',
                'customer',
                'order.items',
            ]
        );
    }
}
