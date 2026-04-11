<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $authUser = $request->user();
        $canSeeHiddenFields = $authUser && $authUser->role?->role_name !== 'Пациент';
        return [
            'id' => $this->when($canSeeHiddenFields, $this->id),
            'user_id' => $this->when($canSeeHiddenFields, $this->user_id),
            'user_name' => $this->whenLoaded('user', fn() =>
            trim($this->user->lastname . ' ' . $this->user->firstname . ' ' . $this->user->patronymic)
            ),
            "address" => $this->address ?? "Отсутствует",
            "gender" => $this->gender,
            "allergies" => $this->allergies ?? "Отсутствует",
            "chronic_diseases" => $this->chronic_diseases ?? "Отсутствует",
            "birth_date" => $this->birth_date?->format('d.m.Y'),
            'created_at' => $this->when($canSeeHiddenFields, function () {
                return $this->created_at?->format('d.m.Y H:i:s');
            }),
            'updated_at' => $this->when($canSeeHiddenFields, function () {
                return $this->updated_at?->format('d.m.Y H:i:s');
            }),
        ];
    }
}
