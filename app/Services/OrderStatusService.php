<?php

namespace App\Services;

use App\Models\Order;
use App\OrderStatus;
use Illuminate\Validation\ValidationException;

class OrderStatusService
{
    /**
     * Create a new class instance.
     */
    public function statusValidation(Order $order, OrderStatus $newStatus): void
    {
        $oldStatus = $order->order_status;

        if ($oldStatus === OrderStatus::Completed) {
            abort(response()->json([
                'message' => 'Completed orders can not change status.'
            ], 422));
        }

        if ($newStatus === OrderStatus::Cancelled && $oldStatus !== OrderStatus::Pending) {
            abort(response()->json([
                'message' => 'Only pending orders can be cancelled.'
            ], 422));
        }
    }
}
