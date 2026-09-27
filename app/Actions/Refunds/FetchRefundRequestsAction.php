<?php

namespace App\Actions\Refunds;

use App\Models\RefundRequest;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class FetchRefundRequestsAction
{


    public function execute(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = RefundRequest::query()
            ->with(
                [
                    'user',
                    'customer',
                    'order.items',
                ]
            );

        queryFilter(
            $query,
            $filters,
            [
                'status' => 'status',
                'decision' => 'decision',
                'reason' => 'reason',
            ],
        );

        return $query
            ->latest()
            ->paginate($perPage);
    }
}
