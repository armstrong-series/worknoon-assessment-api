<?php

namespace App\Actions\Refunds;

use App\Contracts\RefundRequestAnalyzer;
use App\Models\RefundRequest;

class AnalyzeRefundRequestAction
{

    public function __construct(
        private readonly RefundRequestAnalyzer $analyzer,
    ) {}

    public function execute(RefundRequest $refundRequest): array
    {
        return $this->analyzer->analyze(
            $refundRequest->load(
                [
                    'customer',
                    'order.items',
                ]
            )
        );
    }
}
