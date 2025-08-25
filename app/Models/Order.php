<?php

namespace App\Models;

use App\OrderStatus;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'customer_name',
        'order_items',
        'order_status',
        'total_amount'
    ];

    protected $casts = [
        'order_items'  => 'array',
        'order_status' => OrderStatus::class,
        'total_amount' => 'decimal:2',
    ];
}
