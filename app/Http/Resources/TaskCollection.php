<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class TaskCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection,
            'meta' => [
                'total_tasks' => $this->collection->count(),
                'pending_tasks' => $this->collection->where('status', 'draft')->count(),
                'published_tasks' => $this->collection->where('status', 'published')->count(),
                'closed_tasks' => $this->collection->where('status', 'closed')->count(),
            ]
        ];
    }
} 