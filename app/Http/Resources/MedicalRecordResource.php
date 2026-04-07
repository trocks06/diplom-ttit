<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MedicalRecordResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'appointment' => new AppointmentResource($this->whenLoaded('appointment')),
            'diagnosis' => $this->diagnosis,
            'treatment' => $this->treatment,
            'notes' => $this->notes,
            'files' => MedicalFileResource::collection($this->whenLoaded('medical_files')),
            'created_at' => $this->created_at->format('d.m.Y H:i:s'),
            'updated_at' => $this->updated_at->format('d.m.Y H:i:s'),
        ];
    }
}
