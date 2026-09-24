<?php

namespace App\Services;

use App\Actions\Refunds\GenerateRefundResponseAction as CreateRefundRequestAction;
use App\Actions\Refunds\AnalyzeRefundRequestAction;
use App\Actions\Refunds\EvaluateRefundPolicyAction;
use App\Actions\Refunds\DetermineRefundDecisionAction;
use App\Models\User;
use Illuminate\Support\Facades\DB;


class RefundRequestService
{

    public function __construct(
        private readonly CreateRefundRequestAction $createRefundRequest,
        private readonly AnalyzeRefundRequestAction $analyzeRefundRequest,
        private readonly EvaluateRefundPolicyAction $evaluateRefundPolicy,
        private readonly DetermineRefundDecisionAction $determineRefundDecision,
    ) {}


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
