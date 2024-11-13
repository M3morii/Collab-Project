<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClassRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'teacher_id' => 'required|exists:users,id',
            'kkm_score' => 'required|integer|min:0|max:100',
            'academic_year' => 'required|string|max:9', // Format: 2023/2024
            'semester' => 'required|in:1,2',
            'status' => 'required|in:active,inactive'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Nama kelas wajib diisi',
            'name.max' => 'Nama kelas maksimal 255 karakter',
            'description.max' => 'Deskripsi maksimal 1000 karakter',
            'teacher_id.required' => 'Guru wajib dipilih',
            'teacher_id.exists' => 'Guru tidak valid',
            'kkm_score.required' => 'KKM wajib diisi',
            'kkm_score.integer' => 'KKM harus berupa angka',
            'kkm_score.min' => 'KKM minimal 0',
            'kkm_score.max' => 'KKM maksimal 100',
            'academic_year.required' => 'Tahun akademik wajib diisi',
            'academic_year.max' => 'Tahun akademik maksimal 9 karakter',
            'semester.required' => 'Semester wajib diisi',
            'semester.in' => 'Semester tidak valid',
            'status.required' => 'Status wajib diisi',
            'status.in' => 'Status tidak valid'
        ];
    }
}
