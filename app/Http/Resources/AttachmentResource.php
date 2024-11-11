<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class AttachmentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'filename' => $this->filename,
            'file_path' => $this->file_path,
            'url' => url('storage/' . $this->file_path),
            'attachable_type' => $this->attachable_type,
            'attachable_id' => $this->attachable_id,
            'uploaded_by' => new UserResource($this->whenLoaded('uploader')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}