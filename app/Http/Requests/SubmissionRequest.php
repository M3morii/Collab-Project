<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Task;

class SubmissionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $task = Task::findOrFail($this->task_id);
        
        $rules = [
            'task_id' => 'required|exists:tasks,id',
            'content' => 'required|string',
            'attachments.*' => 'nullable|file|max:10240'
        ];

        // Jika task bertipe group, task_group_id wajib diisi
        if ($task->task_type === 'group') {
            $rules['task_group_id'] = 'required|exists:task_groups,id';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'task_group_id.required' => 'For group tasks, you must specify your group.',
            'attachments.*.max' => 'Each attachment must not exceed 10MB.'
        ];
    }
}