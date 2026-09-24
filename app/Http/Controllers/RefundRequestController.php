<?php

namespace App\Http\Controllers;

use App\Services\RefundRequestService;
use App\Http\Requests\CreateRefundRequest;


class RefundRequestController extends Controller
{

    public function __construct(
        private readonly RefundRequestService $refundRequestService
    ) {}

    public function createRefundRequest(CreateRefundRequest $request)
    {

        $refundRequest = $this->refundRequestService->submit(
            $request->user(),
            $request->validated()
        );

        return worknoonResponse(
            $refundRequest,
            201,
            'Refund request submitted successfully!',
            true,
            app('url')->current(),
            [],
            'refund-requests',
            [
                $refundRequest->user,
                $refundRequest->customer,
                $refundRequest->order,
            ],
        );
    }
}
