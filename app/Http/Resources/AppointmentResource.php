<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status->status_name ?? 'Неизвестен',
            'patient' => $this->patient->user->firstname . ' ' . $this->patient->user->lastname,
            'doctor' => $this->schedule->doctor->user->firstname . ' ' . $this->schedule->doctor->user->lastname,
            'time' => [
                'start' => $this->schedule->start_time->format('d.m.Y H:i'),
                'end' => $this->schedule->end_time->format('H:i'),
            ],
            'has_medical_record' => $this->medical_record !== null,
        ];
    }
}
