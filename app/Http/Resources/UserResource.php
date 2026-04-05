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
        return [
            "id" => $this->id,
            "firstname" => $this->firstname,
            "lastname" => $this->lastname,
            "patronymic" => $this->patronymic,
            "phone" => $this->phone,
            "email" => $this->email,
            "avatar" => $this->avatar,
            "verified" => $this->verified,
            "role_id" => $this->role_id,
            "role_name" => $this->role->role_name,
            "patient" => new PatientResource($this->whenLoaded('patient')),
            "created_at" => $this->created_at->format('d.m.Y H:i:s'),
            "updated_at" => $this->updated_at->format('d.m.Y H:i:s'),
        ];
    }
}
