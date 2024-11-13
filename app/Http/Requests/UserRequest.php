<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->user?->id,
            'password' => $this->isMethod('POST') ? 'required|min:8' : 'nullable|min:8',
            'role' => 'required|in:admin,teacher,student',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'is_active' => 'boolean'
        ];
    }
} 