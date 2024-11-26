<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->authService->register($request->validated());

        return response()->json([
            'message' => 'Registration successful',
            'user' => new UserResource($result['user']),
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login($request->validated());

        return response()->json([
            'message' => 'Login successful',
            'user' => new UserResource($result['user']),
            'token' => $result['token']
        ]);
    }

    public function logout(): JsonResponse
    {
        $this->authService->logout();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

    public function profile(): JsonResponse
    {
        return response()->json([
            'user' => new UserResource(auth()->user())
        ]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $user = auth()->user();
        
        // Jika tidak ada input apapun, kembalikan data user yang ada
        if ($request->all() === []) {
            return response()->json([
                'message' => 'No changes made to profile',
                'user' => new UserResource($user)
            ]);
        }
        
        // Validasi input jika ada
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        try {
            // Filter out null values untuk mempertahankan nilai lama jika tidak diupdate
            $dataToUpdate = array_filter($validated, function ($value) {
                return $value !== null;
            });

            // Handle avatar upload jika ada
            if ($request->hasFile('avatar')) {
                // Hapus avatar lama jika ada
                if ($user->avatar) {
                    Storage::delete($user->avatar);
                }
                
                // Upload avatar baru
                $avatarPath = $request->file('avatar')->store('avatars');
                $dataToUpdate['avatar'] = $avatarPath;
            }

            // Update user data jika ada yang perlu diupdate
            if (!empty($dataToUpdate)) {
                $user->update($dataToUpdate);
            }

            return response()->json([
                'message' => !empty($dataToUpdate) ? 'Profile updated successfully' : 'No changes made to profile',
                'user' => new UserResource($user)
            ]);

        } catch (\Exception $e) {
            // Hapus file yang diupload jika terjadi error
            if (isset($avatarPath)) {
                Storage::delete($avatarPath);
            }

            return response()->json([
                'message' => 'Failed to update profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        $this->authService->forgotPassword($request->email);

        return response()->json([
            'message' => 'Password reset link has been sent to your email'
        ]);
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed'
        ]);

        $this->authService->resetPassword($request->all());

        return response()->json([
            'message' => 'Password has been successfully reset'
        ]);
    }
} 