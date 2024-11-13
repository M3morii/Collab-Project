<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SubmissionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'task_id' => $this->task_id,
            'user_id' => $this->user_id,
            'task_group_id' => $this->task_group_id,
            'content' => $this->content,
            'score' => $this->score,
            'feedback' => $this->feedback,
            'status' => $this->status,
            'submitted_at' => $this->submitted_at,
            'user' => new UserResource($this->whenLoaded('user')),
            'task_group' => new TaskGroupResource($this->whenLoaded('taskGroup')),
            'attachments' => SubmissionAttachmentResource::collection($this->whenLoaded('attachments')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
