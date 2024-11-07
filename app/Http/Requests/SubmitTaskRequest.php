<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitTaskRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->role === 'student';
    }

    public function rules()
    {
        return [
            'content' => 'required|string|max:1000',
            'attachments.*' => 'sometimes|file|max:10240' // 10MB max
        ];
    }
} 