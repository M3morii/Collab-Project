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
            'type' => $this->type,
            'max_members' => $this->max_members,
            'due_date' => $this->due_date,
            'attachment_path' => $this->attachment_path,
            'submissions' => SubmissionResource::collection($this->whenLoaded('submissions')),
            'groups' => TaskGroupResource::collection($this->whenLoaded('groups')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}