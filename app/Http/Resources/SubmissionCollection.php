<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class SubmissionCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection,
            'meta' => [
                'total_submissions' => $this->collection->count(),
                'graded_submissions' => $this->collection->where('status', 'graded')->count(),
                'pending_submissions' => $this->collection->where('status', 'submitted')->count(),
                'average_score' => $this->collection->avg('score')
            ]
        ];
    }
} 