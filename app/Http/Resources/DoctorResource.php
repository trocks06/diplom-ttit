<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DoctorResource extends JsonResource
{
    public function toArray($request): array
    {
        $avgRating = $this->averageRating();

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'user_name' => $this->whenLoaded('user', fn() =>
            trim($this->user->lastname . ' ' . $this->user->firstname . ' ' . $this->user->patronymic)
            ),
            'user_email' => $this->whenLoaded('user', fn() => $this->user->email),
            'user_phone' => $this->whenLoaded('user', fn() => $this->user->phone),
            'license' => $this->license,
            'specializations' => SpecializationResource::collection($this->whenLoaded('specializations')),
            'rating' => $this->reviewsCount() > 0 ? round($avgRating, 1) : "Отсутствует",
            'reviews_count' => $this->reviewsCount(),
            'created_at' => $this->created_at?->format('d.m.Y H:i:s'),
            'updated_at' => $this->updated_at?->format('d.m.Y H:i:s'),
        ];
    }
}
