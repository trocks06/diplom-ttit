<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            'user' => new UserResource($this->whenLoaded('user')),
            "address" => $this->address ?? "Отсутствует",
            "gender" => $this->gender,
            "allergies" => $this->allergies ?? "Отсутствует",
            "chronic_diseases" => $this->chronic_diseases ?? "Отсутствует",
            "birth_date" => $this->birth_date->format('d.m.Y'),
            "created_at" => $this->created_at->format('d.m.Y H:i:s'),
            "updated_at" => $this->updated_at->format('d.m.Y H:i:s'),
        ];
    }
}
