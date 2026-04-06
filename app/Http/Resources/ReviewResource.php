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
            // Комментарий только в show или если это не список всех отзывов
            'comment' => $this->when(!$request->routeIs('reviews.index'), $this->comment),
            'patient_name' => $this->appointment->user->first_name . ' ' . $this->appointment->user->last_name,
            'doctor_name' => $this->appointment->schedule->doctor->first_name . ' ' . $this->appointment->schedule->doctor->last_name,
            "created_at" => $this->created_at->format('d.m.Y H:i'),
            "updated_at" => $this->updated_at->format('d.m.Y H:i'),
        ];
    }
}
