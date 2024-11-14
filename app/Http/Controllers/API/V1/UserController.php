<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
        $this->middleware('role:admin');
    }

    // Method untuk admin membuat teacher
    public function createTeacher(UserRequest $request): JsonResponse
    {
        $teacher = $this->authService->createTeacher($request->validated());

        return response()->json([
            'message' => 'Teacher account created successfully',
            'user' => new UserResource($teacher)
        ], 201);
    }

    // ... other CRUD methods ...
} 