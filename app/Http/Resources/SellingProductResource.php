<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SellingProductResource extends JsonResource
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
            'user_id' => $this->user_id,
            'user' => new UserResource($this->whenLoaded('user')),
            'sub_category_id' => $this->sub_category_id,
            'sub_category' => new SubCategoryResource($this->whenLoaded('sub_category')),
            'name' => $this->name,
            'description' => $this->description,
            'condition' => $this->condition,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'status' => $this->selling,
            'is_active' => $this->is_active,
            'images' => SellingProductImageResource::collection($this->images),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
