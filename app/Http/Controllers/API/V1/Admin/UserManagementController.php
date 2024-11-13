<?php

namespace App\Http\Controllers\API\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TeacherRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    protected $userManagementService;

    public function __construct(UserManagementService $userManagementService)
    {
        $this->middleware('role:admin');
        $this->userManagementService = $userManagementService;
    }

    // List semua users dengan filter
    public function index(Request $request): JsonResponse
    {
        $users = $this->userManagementService->getAllUsers($request->all());
        return response()->json([
            'users' => UserResource::collection($users)
        ]);
    }

    // List teachers
    public function listTeachers(): JsonResponse
    {
        $teachers = $this->userManagementService->getTeachers();
        return response()->json([
            'teachers' => UserResource::collection($teachers)
        ]);
    }

    // Create teacher
    public function createTeacher(TeacherRequest $request): JsonResponse
    {
        $teacher = $this->userManagementService->createTeacher($request->validated());
        return response()->json([
            'message' => 'Teacher created successfully',
            'teacher' => new UserResource($teacher)
        ], 201);
    }

    // Update user status (active/inactive)
    public function updateStatus(User $user, Request $request): JsonResponse
    {
        $request->validate(['is_active' => 'required|boolean']);
        
        $user = $this->userManagementService->updateUserStatus($user, $request->is_active);
        
        return response()->json([
            'message' => 'User status updated successfully',
            'user' => new UserResource($user)
        ]);
    }
} 