<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'class_id' => $this->class_id,
            'title' => $this->title,
            'description' => $this->description,
            'start_date' => $this->start_date,
            'deadline' => $this->deadline,
            'task_type' => $this->task_type,
            'max_score' => $this->max_score,
            'weight_percentage' => $this->weight_percentage,
            'status' => $this->status,
            'created_by' => new UserResource($this->whenLoaded('creator')),
            'task_groups' => TaskGroupResource::collection($this->whenLoaded('taskGroups')),
            'attachments' => TaskAttachmentResource::collection($this->whenLoaded('attachments')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}