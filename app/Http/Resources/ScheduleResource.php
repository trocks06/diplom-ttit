<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ScheduleResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'doctor_id' => $this->doctor_id,
            'start_time' => $this->start_time->format('d.m.Y H:i'),
            'end_time' => $this->end_time->format('d.m.Y H:i'),
            'is_booked' => $this->appointments->isNotEmpty() ? 'Занят' : 'Незанят',
        ];
    }
}
