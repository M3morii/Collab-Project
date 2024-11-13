<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'class_id' => 'required|exists:classes,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date|after_or_equal:today',
            'deadline' => 'required|date|after:start_date',
            'task_type' => 'required|in:individual,group',
            'max_score' => 'required|integer|min:0|max:100',
            'weight_percentage' => 'required|integer|min:0|max:100',
            'status' => 'required|in:draft,published,closed',
            'attachments.*' => 'nullable|file|max:10240' // max 10MB
        ];
    }

    public function messages()
    {
        return [
            'deadline.after' => 'The deadline must be after the start date.',
            'weight_percentage.max' => 'The weight percentage cannot be greater than 100.',
            'attachments.*.max' => 'Each attachment must not exceed 10MB.'
        ];
    }
}