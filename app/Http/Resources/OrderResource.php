<?php

namespace App\Http\Resources;

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
            'id' => $this->id,
            'order_code' => $this->order_code,
            'user_id' => $this->user_id,
            'user' => new UserResource($this->whenLoaded('user')),
            'selling_product_id' => $this->selling_product_id,
            'selling_product' => new SellingProductResource($this->whenLoaded('selling_product')),
            'seller_id' => $this->seller_id,
            'seller' => new UserResource($this->whenLoaded('seller')),
            'quantity' => $this->quantity,
            'total_price' => $this->total_price,
            'payment_id' => $this->payment_id,
            // 'payment' => new UserResource($this->whenLoaded('payment')),
            'payment_screenshot' => $this->payment_screenshot ? url('/storage/payments/' . $this->payment_screenshot) : null,
            'status' => $this->status,
            'note' => $this->note
        ];
    }
}
