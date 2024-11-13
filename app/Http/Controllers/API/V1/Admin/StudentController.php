<?php

namespace App\Http\Controllers\API\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StudentRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->middleware('role:admin');
        $this->userService = $userService;
    }

    public function index(Request $request): JsonResponse
    {
        $students = $this->userService->getStudents($request->all());

        return response()->json([
            'students' => UserResource::collection($students)
        ]);
    }

    public function show(User $student): JsonResponse
    {
        if ($student->role !== 'student') {
            return response()->json(['message' => 'User is not a student'], 404);
        }

        return response()->json([
            'student' => new UserResource($student->load(['classes', 'submissions']))
        ]);
    }

    public function update(StudentRequest $request, User $student): JsonResponse
    {
        if ($student->role !== 'student') {
            return response()->json(['message' => 'User is not a student'], 404);
        }

        $student = $this->userService->updateStudent($student, $request->validated());

        return response()->json([
            'message' => 'Student updated successfully',
            'student' => new UserResource($student)
        ]);
    }

    public function studentStats(User $student): JsonResponse
    {
        if ($student->role !== 'student') {
            return response()->json(['message' => 'User is not a student'], 404);
        }

        $stats = $this->userService->getStudentStats($student);

        return response()->json([
            'stats' => $stats
        ]);
    }
} 