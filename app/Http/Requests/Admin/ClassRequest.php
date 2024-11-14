<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ClassRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'teacher_id' => 'required|exists:users,id,role,teacher,is_active,1',
            'academic_year' => 'required|string|max:9', // Format: 2023/2024
            'semester' => 'required|integer|in:1,2',
            'status' => 'required|in:active,inactive',
            'kkm_score' => 'required|integer|min:0|max:100'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama kelas wajib diisi',
            'teacher_id.required' => 'Guru wajib dipilih',
            'teacher_id.exists' => 'Guru yang dipilih tidak valid atau tidak aktif',
            'academic_year.required' => 'Tahun akademik wajib diisi',
            'academic_year.max' => 'Format tahun akademik tidak valid (contoh: 2023/2024)',
            'semester.required' => 'Semester wajib dipilih',
            'semester.in' => 'Semester hanya bisa 1 atau 2',
            'status.required' => 'Status kelas wajib dipilih',
            'status.in' => 'Status hanya bisa active atau inactive',
            'kkm_score.required' => 'KKM wajib diisi',
            'kkm_score.min' => 'KKM minimal 0',
            'kkm_score.max' => 'KKM maksimal 100'
        ];
    }
} 