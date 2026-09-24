<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{

    use HasUuids;

    protected $fillable = [
        'order_id',
        'product_name',
        'quantity',
        'unit_price_cents',
        'is_final_sale',
    ];

    protected function casts(): array
    {
        return [
            'quantity'         => 'integer',
            'unit_price_cents' => 'integer',
            'is_final_sale'    => 'boolean',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
