<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\AuthenticationException;

class AuthService
{
    public function register(array $data)
    {
        $data['role'] = 'student';
        
        $data['is_active'] = true;
        
        $data['password'] = Hash::make($data['password']);
        
        $user = User::create($data);

        $token = $user->createToken('auth-token')->plainTextToken;

        return [
            'user' => $user,
        ];
    }

    public function createTeacher(array $data)
    {
        $data['role'] = 'teacher';
        $data['is_active'] = true;
        $data['password'] = Hash::make($data['password']);
        
        return User::create($data);
    }

    public function login(array $credentials)
    {
        if (!auth()->attempt($credentials)) {
            throw new AuthenticationException('Invalid credentials');
        }

        $user = auth()->user();

        if (!$user->is_active) {
            auth()->logout();
            throw new AuthenticationException('Account is inactive');
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token
        ];
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();
    }
} 