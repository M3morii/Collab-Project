<?php

namespace App\Http\Controllers\API\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\ClassResource;

class ClassManagementController extends Controller
{
    public function index()
    {
        $classes = ClassRoom::with(['teacher', 'students'])->paginate(10);
        return ClassResource::collection($classes);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'kkm_score' => 'required|integer|min:0|max:100',
            'academic_year' => 'required|string',
            'semester' => 'required|in:1,2',
            'status' => 'required|in:active,inactive'
        ]);

        $class = ClassRoom::create($validated);

        return new ClassResource($class);
    }

    public function assignTeacher(Request $request, ClassRoom $class)
    {
        $validated = $request->validate([
            'teacher_id' => 'required|exists:users,id'
        ]);

        $teacher = User::findOrFail($validated['teacher_id']);
        if ($teacher->role !== 'teacher') {
            return response()->json(['message' => 'User is not a teacher'], 422);
        }

        $class->update(['teacher_id' => $teacher->id]);
        $class->students()->attach($teacher->id, [
            'role' => 'teacher',
            'status' => 'active'
        ]);

        return new ClassResource($class->load('teacher'));
    }

    public function assignStudents(Request $request, ClassRoom $class)
    {
        $validated = $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:users,id'
        ]);

        $students = User::whereIn('id', $validated['student_ids'])
                       ->where('role', 'student')
                       ->get();

        foreach ($students as $student) {
            $class->students()->attach($student->id, [
                'role' => 'student',
                'status' => 'active'
            ]);
        }

        return new ClassResource($class->load('students'));
    }

    /**
     * Display the specified class.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $class = ClassRoom::with(['teacher', 'students'])->findOrFail($id);
            
            return response()->json([
                'message' => 'Class retrieved successfully',
                'data' => new ClassResource($class)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Class not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified class.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $class = ClassRoom::findOrFail($id);

            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'description' => 'sometimes|nullable|string',
                'teacher_id' => 'sometimes|exists:users,id',
                'kkm_score' => 'sometimes|integer|min:0|max:100',
                'academic_year' => 'sometimes|string',
                'semester' => 'sometimes|in:1,2',
                'status' => 'sometimes|in:active,inactive'
            ]);

            // Jika ada perubahan teacher_id, validasi role teacher
            if (isset($validated['teacher_id'])) {
                $teacher = User::findOrFail($validated['teacher_id']);
                if ($teacher->role !== 'teacher') {
                    return response()->json([
                        'message' => 'Selected user is not a teacher'
                    ], 422);
                }
            }

            $class->update($validated);

            // Update teacher di class_users jika teacher_id berubah
            if (isset($validated['teacher_id'])) {
                // Hapus teacher lama dari class_users
                $class->students()->where('role', 'teacher')->detach();
                
                // Tambah teacher baru ke class_users
                $class->students()->attach($validated['teacher_id'], [
                    'role' => 'teacher',
                    'status' => 'active'
                ]);
            }

            return response()->json([
                'message' => 'Class updated successfully',
                'data' => new ClassResource($class->fresh(['teacher', 'students']))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update class',
                'error' => $e->getMessage()
            ], $e instanceof ModelNotFoundException ? 404 : 500);
        }
    }
} 