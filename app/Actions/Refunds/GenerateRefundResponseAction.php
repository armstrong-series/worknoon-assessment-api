<?php

namespace App\Actions\Refunds;

use App\Models\Order;
use App\Models\RefundRequest;
use App\Models\User;
use Illuminate\Validation\ValidationException;


class GenerateRefundResponseAction
{

    public function execute(User $user, array $data): RefundRequest
    {


        $customer = $user->customer;


        if (! $customer) {
            throw ValidationException::withMessages(
                [
                    'customer' => ['The authenticated user does not have a customer profile.'],
                ]
            );
        }


        $order = Order::query()
            ->with(['customer', 'items'])
            ->whereKey($data['order_id'])->where('customer_id', $customer->id)->first();



        if (!$order) {
            throw ValidationException::withMessages(
                [
                    'order_id' => [
                        'The order does not belong to the authenticated customer.',
                    ],
                ]
            );
        }

        $existingRefund = RefundRequest::query()->where('order_id', $data['order_id'])->exists();

        if ($existingRefund) {
            throw ValidationException::withMessages(
                [
                    'order_id' => [
                        'A refund request has already been submitted for this order.',
                    ],
                ]
            );
        }


        return RefundRequest::create(
            [
                'user_id'       => $user->id,
                'customer_id'   => $customer->id,
                'order_id'      => $order->id,
                'ai_model'      => config('refunds.ai.model'),
                'ai_provider'   => config('refunds.ai.provider'),
                'reason'        => $data['reason'],
                'requested_amount_cents' => $data['requested_amount_cents'],
                'status'        => 'pending',
            ]
        )->load(
            [
                'user',
                'customer',
                'order.items',
            ]
        );
    }
}
