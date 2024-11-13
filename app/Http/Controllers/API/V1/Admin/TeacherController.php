<?php

namespace App\Http\Controllers\API\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TeacherRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->middleware('role:admin');
        $this->userService = $userService;
    }

    public function index(Request $request): JsonResponse
    {
        $teachers = $this->userService->getTeachers($request->all());

        return response()->json([
            'teachers' => UserResource::collection($teachers)
        ]);
    }

    public function store(TeacherRequest $request): JsonResponse
    {
        $teacher = $this->userService->createTeacher($request->validated());

        return response()->json([
            'message' => 'Teacher created successfully',
            'teacher' => new UserResource($teacher)
        ], 201);
    }

    public function show(User $teacher): JsonResponse
    {
        if ($teacher->role !== 'teacher') {
            return response()->json(['message' => 'User is not a teacher'], 404);
        }

        return response()->json([
            'teacher' => new UserResource($teacher->load(['classes']))
        ]);
    }

    public function update(TeacherRequest $request, User $teacher): JsonResponse
    {
        if ($teacher->role !== 'teacher') {
            return response()->json(['message' => 'User is not a teacher'], 404);
        }

        $teacher = $this->userService->updateTeacher($teacher, $request->validated());

        return response()->json([
            'message' => 'Teacher updated successfully',
            'teacher' => new UserResource($teacher)
        ]);
    }

    public function destroy(User $teacher): JsonResponse
    {
        if ($teacher->role !== 'teacher') {
            return response()->json(['message' => 'User is not a teacher'], 404);
        }

        $this->userService->deleteTeacher($teacher);

        return response()->json([
            'message' => 'Teacher deleted successfully'
        ]);
    }

    public function teacherStats(User $teacher): JsonResponse
    {
        if ($teacher->role !== 'teacher') {
            return response()->json(['message' => 'User is not a teacher'], 404);
        }

        $stats = $this->userService->getTeacherStats($teacher);

        return response()->json([
            'stats' => $stats
        ]);
    }
} 