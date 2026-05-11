<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = ['name', 'sku', 'price', 'description', 'is_active'];

    public function dealItems(): HasMany
    {
        return $this->hasMany(DealItem::class);
    }
}
