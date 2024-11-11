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
            'due_date' => $this->due_date,
            'group' => new GroupResource($this->whenLoaded('group')),
            'creator' => new UserResource($this->whenLoaded('creator')),
            'attachments' => AttachmentResource::collection($this->whenLoaded('attachments')),
            'submission' => new SubmissionResource($this->whenLoaded('submission')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}