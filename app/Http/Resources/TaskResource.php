<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'priority' => $this->priority,
            'deadline' => $this->deadline,
            'creator' => new UserResource($this->whenLoaded('creator')),
            'is_assigned' => true,
            'assignment_message' => 'Anda ditugaskan untuk mengerjakan tugas ini',
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
} 