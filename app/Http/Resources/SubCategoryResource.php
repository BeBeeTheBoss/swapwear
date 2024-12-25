<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubCategoryResource extends JsonResource
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
            'main_category_id' => $this->main_category_id,
            'main_category' => new MainCategoryResource($this->whenLoaded('main_category')),
            'name' => $this->name,
            'icon' => url('storage/icons/' . $this->icon),
            'purchase_count' => $this->purchase_count,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
