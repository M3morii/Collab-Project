<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->isTeacher();
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'group_id' => 'required|exists:groups,id',
            'due_date' => 'required|date|after:today',
            'attachments.*' => 'sometimes|file|max:10240', // 10MB max
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Judul tugas wajib diisi',
            'description.required' => 'Deskripsi tugas wajib diisi',
            'group_id.required' => 'Kelompok wajib dipilih',
            'due_date.required' => 'Tanggal deadline wajib diisi',
            'due_date.after' => 'Tanggal deadline harus setelah hari ini',
            'attachments.*.max' => 'Ukuran file maksimal 10MB'
        ];
    }
}