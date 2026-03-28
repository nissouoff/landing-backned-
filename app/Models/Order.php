<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    protected $fillable = [
        'landing_id',
        'product_id',
        'product_name',
        'product_price',
        'product_photo',
        'customer_name',
        'customer_firstname',
        'customer_phone',
        'wilaya',
        'commune',
        'address',
        'delivery_type',
        'is_verified',
        'status',
        'notes',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
    ];

    public function landing(): BelongsTo
    {
        return $this->belongsTo(Landing::class);
    }
}
