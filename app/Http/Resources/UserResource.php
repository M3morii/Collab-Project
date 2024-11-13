<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'avatar' => $this->avatar,
            'phone' => $this->phone,
            'address' => $this->address,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at
        ];
    }
}