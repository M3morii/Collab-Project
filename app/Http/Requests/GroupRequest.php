<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GroupRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->isTeacher();
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'class_id' => 'required|exists:classes,id',
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:users,id,role,student',
            'leader_id' => 'required|exists:users,id,role,student'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Nama kelompok wajib diisi',
            'class_id.required' => 'Kelas wajib dipilih',
            'student_ids.required' => 'Anggota kelompok wajib dipilih',
            'leader_id.required' => 'Ketua kelompok wajib dipilih'
        ];
    }
}
