<?php

namespace App\Http\Controllers\API\V1\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClassResource;
use App\Models\Classes;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class ClassController extends Controller
{
    public function index(): JsonResponse
    {
        $teacher = auth()->user();
        
        $classes = Classes::where('teacher_id', $teacher->id)
                         ->with(['teacher', 'users'])
                         ->get();

        return response()->json([
            'classes' => ClassResource::collection($classes)
        ]);
    }
} 