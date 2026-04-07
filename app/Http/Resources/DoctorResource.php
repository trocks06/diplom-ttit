<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DoctorResource extends JsonResource
{
    public function toArray($request)
    {
        $avgRating = $this->averageRating();

        return [
            'id' => $this->id,
            'user' => new UserResource($this->whenLoaded('user')),
            'license' => $this->license,
            'specializations' => SpecializationResource::collection($this->whenLoaded('specializations')),
            'rating' => round($avgRating, 1),
            'reviews_count' => $this->reviewsCount(),
            'created_at' => $this->created_at->format('d.m.Y H:i:s'),
            'updated_at' => $this->updated_at->format('d.m.Y H:i:s'),
        ];
    }
}
