<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Http\Resources\UserResource;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'student',
            'is_active' => true
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Pendaftaran berhasil',
            'user' => new UserResource($user),
            'token' => $token
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Kredensial yang diberikan tidak sesuai.'],
            ]);
        }

        if (!$user->is_active) {
            throw ValidationException::withMessages([
                'email' => ['Akun ini telah dinonaktifkan.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        // Redirect berdasarkan role
        $redirectTo = $user->role === 'admin' ? '/admin/dashboard' : '/dashboard';

        return response()->json([
            'message' => 'Login berhasil',
            'user' => new UserResource($user),
            'token' => $token,
            'redirect_to' => $redirectTo
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil'
        ]);
    }

    public function me(Request $request)
    {
        return new UserResource($request->user());
    }

    public function users()
    {
        $this->authorize('viewAny', User::class);
        
        $users = User::all();
        
        return UserResource::collection($users);
    }

    public function toggleActive(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $user->update([
            'is_active' => !$user->is_active
        ]);

        return new UserResource($user);
    }
}