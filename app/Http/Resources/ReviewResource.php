<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'rating' => $this->rating,
            'comment' => $this->when(!$request->routeIs('reviews.index'), $this->comment),
            'patient_name' => $this->appointment?->patient?->user
                ? $this->appointment->patient->user->firstname . ' ' . $this->appointment->patient->user->lastname
                : 'Неизвестно',
            'doctor_name' => $this->appointment?->schedule?->doctor?->user
                ? $this->appointment->schedule->doctor->user->firstname . ' ' . $this->appointment->schedule->doctor->user->lastname
                : 'Неизвестно',
            'created_at' => $this->created_at?->format('d.m.Y H:i'),
            'updated_at' => $this->updated_at?->format('d.m.Y H:i'),
        ];
    }
}
