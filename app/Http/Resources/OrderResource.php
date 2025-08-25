<?php

namespace App\Http\Resources;

use App\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'user_id'       => $this->user_id,
            'customer_name' => $this->customer_name,
            'order_items'   => $this->order_items,
            'order_status'  => $this->order_status->value,
            'total_amount'  => (float)$this->total_amount,
            'created_at'    => $this->created_at,
        ];
    }
}
