<?php

namespace App\Contracts;

use App\Models\RefundRequest;

interface RefundRequestAnalyzer
{

    public function analyze(RefundRequest $refundRequest): array;
}
