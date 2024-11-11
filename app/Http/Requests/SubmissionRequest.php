<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmissionRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->isStudent();
    }

    public function rules()
    {
        return [
            'task_id' => 'required|exists:tasks,id',
            'group_id' => 'required|exists:groups,id',
            'description' => 'required|string',
            'attachments.*' => 'sometimes|file|max:10240' // 10MB max
        ];
    }

    public function messages()
    {
        return [
            'description.required' => 'Deskripsi jawaban wajib diisi',
            'attachments.*.max' => 'Ukuran file maksimal 10MB'
        ];
    }
}