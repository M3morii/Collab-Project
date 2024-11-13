<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\AuthenticationException;

class AuthService
{
    public function login(array $credentials)
    {
        if (!auth()->attempt($credentials)) {
            throw new AuthenticationException('Invalid credentials');
        }

        $user = auth()->user();
        $token = $user->createToken('auth-token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token
        ];
    }

    public function register(array $data)
    {
        $data['password'] = Hash::make($data['password']);
        
        return User::create($data);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();
    }
} 