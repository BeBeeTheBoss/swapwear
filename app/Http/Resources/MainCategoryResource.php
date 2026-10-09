<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MainCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data =  [
            'id' => $this->id,
            'name' => $this->name,
            'icon' => url('storage/icons/' . $this->icon),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'sub_categories_count' => $this->whenCounted('sub_categories'),
            'sub_categories' => SubCategoryResource::collection($this->whenLoaded('sub_categories'))
        ];

        return $data;

    }
}
