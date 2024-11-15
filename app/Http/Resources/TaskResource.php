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
            'class_id' => $this->class_id,
            'task_type' => $this->task_type,
            'deadline' => $this->deadline,
            'submissions' => SubmissionResource::collection($this->whenLoaded('submissions')),
            'groups' => TaskGroupResource::collection($this->whenLoaded('groups')),
        ];
    }
}