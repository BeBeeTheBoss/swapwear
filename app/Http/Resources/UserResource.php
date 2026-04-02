<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = parent::toArray($request);

        $data['image'] = $data['image'] ? url('storage/profile_images/' . $data['image']) : null;
        $data['nrc_front_image'] = $data['nrc_front_image'] ? url('storage/nrc_images/' . $data['nrc_front_image']) : null;
        $data['nrc_back_image'] = $data['nrc_back_image'] ? url('storage/nrc_images/' . $data['nrc_back_image']) : null;

        return $data;
    }
}
