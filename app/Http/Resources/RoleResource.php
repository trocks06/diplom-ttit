<?php

namespace App\Http\Resources;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
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
            "role_name" => $this->role_name,
            "created_at" => $this->created_at->format('d.m.Y H:i:s'),
            "updated_at" => $this->updated_at->format('d.m.Y H:i:s'),
        ];
    }
}
