<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TeacherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->role === 'admin';
    }

    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'is_active' => 'boolean'
        ];

        // Jika creating new teacher
        if ($this->isMethod('post')) {
            $rules['email'] = 'required|string|email|unique:users,email';
            $rules['password'] = 'required|string|min:8|confirmed';
        }

        // Jika updating existing teacher
        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $rules['email'] = [
                'required',
                'string',
                'email',
                Rule::unique('users')->ignore($this->teacher)
            ];
            $rules['password'] = 'nullable|string|min:8|confirmed';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'Email sudah digunakan',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'password.min' => 'Password minimal 8 karakter'
        ];
    }
} 