<?php

namespace App\Http\Controllers;

use App\Services\RefundRequestService;
use App\Http\Requests\CreateRefundRequest;
use App\Http\Requests\FetchRefundRequestsRequest;
use App\Models\RefundRequest;
use App\Http\Requests\DecideRefundRequest;



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

    public function fetchRefundRequests(FetchRefundRequestsRequest $request)
    {
        authorizedRole(
            ['admin', 'support']
        );

        $refundRequests = $this->refundRequestService->fetchRefundRequests(
            $request->validated('filter', []),
            (int) $request->validated('per_page', 15),
        );


        $included = $refundRequests->getCollection()
            ->flatMap(function ($refundRequest) {
                return [
                    $refundRequest->user,
                    $refundRequest->customer,
                    $refundRequest->order,
                ];
            })
            ->filter()
            ->unique(
                fn($model) =>
                get_class($model) . ':' . $model->getKey()
            )
            ->values()
            ->all();

        return worknoonResponse(
            $refundRequests->items(),
            200,
            'Refund requests fetched successfully!',
            true,
            app('url')->current(),
            [],
            'refund-requests',
            $included,
            [
                'current_page' => $refundRequests->currentPage(),
                'per_page'     => $refundRequests->perPage(),
                'total'        => $refundRequests->total(),
                'last_page'    => $refundRequests->lastPage(),
            ],
        );
    }


    public function fetchRefundRequest(RefundRequest $refundRequest)
    {
        authorizedRole(['admin', 'support']);
        $refundRequest = $this->refundRequestService->fetchRefundRequest($refundRequest);
        $included = [
            $refundRequest->user,
            $refundRequest->customer,
            $refundRequest->order,
            ...$refundRequest->order->items->all(),
        ];
        return worknoonResponse(
            $refundRequest,
            200,
            'Refund request fetched successfully!',
            true,
            app('url')->current(),
            [],
            'refund-requests',
            $included,
        );
    }


    public function decideRefundRequest(DecideRefundRequest $request, string $refundRequestId)
    {
        authorizedRole(['admin', 'support']);

        $refundRequest = $this->refundRequestService->decideRefundRequest(
            $refundRequestId,
            $request->validatedAttributes(),
        );

        return worknoonResponse(
            $refundRequest,
            200,
            $refundRequest->decision->value === 'approved'
                ? 'Refund request approved successfully.'
                : 'Refund request denied successfully.',
            true,
            app('url')->current(),
            [],
            'refund-requests',
        );
    }
}
