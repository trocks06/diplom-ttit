<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
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
            "user_id" => $this->user_id,
            'text' => $this->when(
                $request->routeIs('notifications.show'),
                $this->text
            ),
            "is_read" => $this->is_read ? "Прочитано" : "Непрочитано",
            "created_at" => $this->created_at?->format('d.m.Y H:i'),
        ];
    }
}
