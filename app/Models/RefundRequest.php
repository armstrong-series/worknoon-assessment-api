<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\RefundRequestStatus;
use App\Enums\RefundDecision;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RefundRequest extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'customer_id',
        'order_id',
        'reason',
        'requested_amount_cents',
        'status',
        'decision',
        'reason_code',
        'decision_reason',
        'ai_analysis',
        'ai_provider',
        'ai_model',
        'policy_version',
    ];

    protected function casts(): array
    {
        return [
            'requested_amount_cents' => 'integer',
            'ai_analysis'           => 'array',
            'status'                => RefundRequestStatus::class,
            'decision'              => RefundDecision::class,
        ];
    }



    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
