<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $authUser = $request->user();
        $canSeeHiddenFields = $authUser && $authUser->role?->role_name !== 'Пациент';
        return [
            'id' => $this->when($canSeeHiddenFields, $this->id),
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'patronymic' => $this->patronymic ?? 'Отсутствует',
            'phone' => $this->phone,
            'email' => $this->email,
            'email_verified' => $this->email_verified_at ? 'Верифицирован' : 'Не верифицирован',
            'avatar' => $this->avatar,
            'role_id' => $this->role_id,
            'role_name' => $this->role->role_name,
            'patient' => new PatientResource($this->whenLoaded('patient')),
            'doctor' => new DoctorResource($this->whenLoaded('doctor')),
            'created_at' => $this->when($canSeeHiddenFields, function () {
                return $this->created_at?->format('d.m.Y H:i:s');
            }),
            'updated_at' => $this->when($canSeeHiddenFields, function () {
                return $this->updated_at?->format('d.m.Y H:i:s');
            }),
        ];
    }
}
