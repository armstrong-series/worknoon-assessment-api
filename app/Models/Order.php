<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;



class Order extends Model
{
    use HasUuids;

    protected $fillable = [
        'customer_id',
        'order_number',
        'total_amount_cents',
        'currency',
        'status',
        'ordered_at',
    ];

    protected function casts(): array
    {
        return [
            'total_amount_cents' => 'integer',
            'ordered_at'         => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function refundRequests(): HasMany
    {
        return $this->hasMany(RefundRequest::class);
    }
}
