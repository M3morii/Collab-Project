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
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'deadline' => 'required|date|after:today',
        'status' => 'in:todo,in_progress,review,done', // Sesuaikan dengan migrasi
        'priority' => 'required|in:low,medium,high',
        'assignees' => 'array|exists:users,id'
    ];
}
} 