<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClassRequest;
use App\Http\Resources\ClassResource;
use App\Models\Classes;
use App\Models\User;
use App\Services\ClassService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    protected $classService;

    public function __construct(ClassService $classService)
    {
        $this->classService = $classService;
    }

    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Classes::class);
        
        $user = User::find(auth()->id());
        $classes = $this->classService->getClassesByRole($user);

        return response()->json([
            'classes' => ClassResource::collection($classes)
        ]);
    }

    public function store(ClassRequest $request): JsonResponse
    {
        $this->authorize('create', Classes::class);
        
        $class = $this->classService->create($request->validated());

        return response()->json([
            'message' => 'Class created successfully',
            'class' => new ClassResource($class)
        ], 201);
    }

    public function show(Classes $class): JsonResponse
    {
        $this->authorize('view', $class);

        return response()->json([
            'class' => new ClassResource($class->load(['teacher', 'users']))
        ]);
    }

    public function update(ClassRequest $request, Classes $class): JsonResponse
    {
        $this->authorize('update', $class);

        $class = $this->classService->update($class, $request->validated());

        return response()->json([
            'message' => 'Class updated successfully',
            'class' => new ClassResource($class)
        ]);
    }

    public function destroy(Classes $class): JsonResponse
    {
        $this->authorize('delete', $class);

        $class->delete();

        return response()->json([
            'message' => 'Class deleted successfully'
        ]);
    }

    public function getAvailableStudents(Classes $class): JsonResponse
    {
        $this->authorize('manageStudents', $class);

        $students = $this->classService->getAvailableStudents($class);

        return response()->json([
            'students' => $students
        ]);
    }

    public function addStudents(Request $request, Classes $class): JsonResponse
    {
        $this->authorize('manageStudents', $class);

        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:users,id,role,student'
        ]);

        $addedCount = $this->classService->addStudents($class, $request->student_ids);

        return response()->json([
            'message' => "{$addedCount} students added to class successfully"
        ]);
    }

    public function removeStudent(Classes $class, User $student): JsonResponse
    {
        $this->authorize('manageStudents', $class);

        if ($student->role !== 'student') {
            return response()->json([
                'message' => 'User is not a student'
            ], 422);
        }

        $this->classService->removeStudent($class, $student);

        return response()->json([
            'message' => 'Student removed from class successfully'
        ]);
    }
} 