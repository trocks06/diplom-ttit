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
            "patronymic" => $this->patronymic ?? "Отсутствует",
            "phone" => $this->phone,
            "email" => $this->email,
            "email_verified" => $this->email_verified_at ? "Верифицирован" : "Неверифицирован",
            "avatar" => $this->avatar ?? "Отсутствует",
            "role_id" => $this->role_id,
            "role_name" => $this->role->role_name,
            "patient" => new PatientResource($this->whenLoaded('patient')),
            "created_at" => $this->created_at->format('d.m.Y H:i:s'),
            "updated_at" => $this->updated_at->format('d.m.Y H:i:s'),
        ];
    }
}
