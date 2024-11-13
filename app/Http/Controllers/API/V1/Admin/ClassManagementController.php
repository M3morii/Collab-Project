<?php

namespace App\Http\Controllers\API\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ClassRequest;
use App\Http\Resources\ClassResource;
use App\Models\Classes;
use App\Services\ClassManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClassManagementController extends Controller
{
    protected $classManagementService;

    public function __construct(ClassManagementService $classManagementService)
    {
        $this->middleware('role:admin');
        $this->classManagementService = $classManagementService;
    }

    public function store(ClassRequest $request): JsonResponse
    {
        $class = $this->classManagementService->createClass($request->validated());
        
        return response()->json([
            'message' => 'Class created successfully',
            'class' => new ClassResource($class)
        ], 201);
    }

    public function assignTeacher(Classes $class, Request $request): JsonResponse
    {
        $request->validate(['teacher_id' => 'required|exists:users,id']);
        
        $class = $this->classManagementService->assignTeacher($class, $request->teacher_id);
        
        return response()->json([
            'message' => 'Teacher assigned successfully',
            'class' => new ClassResource($class)
        ]);
    }
} 