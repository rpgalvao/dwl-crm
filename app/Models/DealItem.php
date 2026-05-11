<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DealItem extends Model
{
    protected $fillable = ['deal_id', 'product_id', 'quantity', 'unit_price'];

    public function deal(): BelongsTo
    {
        return $this->belongsTo(Deal::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
