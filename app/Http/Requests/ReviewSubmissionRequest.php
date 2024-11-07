<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReviewSubmissionRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->role === 'admin';
    }

    public function rules()
    {
        return [
            'status' => 'required|in:reviewed,rejected',
            'feedback' => 'required|string|max:1000',
            'score' => 'required_if:status,reviewed|nullable|integer|min:0|max:100'
        ];
    }
} 