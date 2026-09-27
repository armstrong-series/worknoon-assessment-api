<?php

namespace App\Services;

use App\Actions\Refunds\GenerateRefundResponseAction as CreateRefundRequestAction;
use App\Actions\Refunds\AnalyzeRefundRequestAction;
use App\Actions\Refunds\EvaluateRefundPolicyAction;
use App\Actions\Refunds\DetermineRefundDecisionAction;
use App\Actions\Refunds\FetchRefundRequestsAction;
use App\Actions\Refunds\FetchRefundRequestAction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\RefundRequest;
use App\Actions\Refunds\DecideRefundRequestAction;


class RefundRequestService
{

    public function __construct(
        private readonly CreateRefundRequestAction $createRefundRequest,
        private readonly AnalyzeRefundRequestAction $analyzeRefundRequest,
        private readonly EvaluateRefundPolicyAction $evaluateRefundPolicy,
        private readonly DetermineRefundDecisionAction $determineRefundDecision,
        private readonly FetchRefundRequestsAction $fetchRefundRequests,
        private readonly FetchRefundRequestAction $fetchRefundRequest,
        private readonly DecideRefundRequestAction $decideRefundRequestAction
    ) {}



    public function fetchRefundRequests(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->fetchRefundRequests->execute($filters, $perPage);
    }


    public function fetchRefundRequest(RefundRequest $refundRequest): RefundRequest
    {
        return $this->fetchRefundRequest->execute($refundRequest);
    }

    public function decideRefundRequest(string $refundRequestId, array $attributes)
    {
        return DB::transaction(function () use ($refundRequestId, $attributes) {

            return $this->decideRefundRequestAction->execute(
                $refundRequestId,
                $attributes,
            );
        });
    }


    public function submit(User $user, array $data)
    {

        return DB::transaction(function () use ($user, $data) {

            $refundRequest = $this->createRefundRequest->execute(
                $user,
                $data,
            );



            $analysis = $this->analyzeRefundRequest->execute(
                $refundRequest,
            );



            $policyResult = $this->evaluateRefundPolicy->execute(
                $refundRequest,
                $analysis,
            );


            $decision = $this->determineRefundDecision->execute(
                $refundRequest,
                $analysis,
                $policyResult,
            );


            $refundRequest->update(
                [
                    'ai_analysis'     => $analysis,
                    'decision'        => $decision['decision'],
                    'reason_code'     => $decision['reason_code'],
                    'decision_reason' => $decision['reason'],
                    'status'          => 'processed',
                    'policy_version'  => config('refunds.policy_version'),
                ]
            );


            return $refundRequest->fresh(
                [
                    'user',
                    'customer',
                    'order.items',
                ]
            );
        });
    }
}
