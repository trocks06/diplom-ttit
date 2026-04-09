<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StatusResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "status_name" => $this->status_name,
            "created_at" => $this->created_at->format('d.m.Y H:i:s'),
            "updated_at" => $this->updated_at->format('d.m.Y H:i:s'),
        ];
    }
}
