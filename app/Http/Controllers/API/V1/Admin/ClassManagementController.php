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

    public function assignTeacher(Request $request, $classId)
    {
        try {
            $validated = $request->validate([
                'teacher_id' => 'required|exists:users,id'
            ]);

            // Cari class berdasarkan ID
            $class = ClassRoom::findOrFail($classId);

            // Cari dan validasi teacher
            $teacher = User::findOrFail($validated['teacher_id']);
            if ($teacher->role !== 'teacher') {
                return response()->json([
                    'message' => 'User is not a teacher'
                ], 422);
            }

            // Cek apakah kelas sudah punya teacher
            if ($class->teacher_id) {
                return response()->json([
                    'message' => 'Class already has a teacher assigned. Please remove current teacher first.'
                ], 422);
            }

            // Update teacher_id di tabel classes
            $class->update(['teacher_id' => $teacher->id]);

            // Attach teacher ke class_users
            $class->students()->attach($teacher->id, [
                'role' => 'teacher',
                'status' => 'active'
            ]);

            return response()->json([
                'message' => 'Teacher assigned successfully',
                'data' => new ClassResource($class->fresh(['teacher']))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to assign teacher',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function assignStudents(Request $request, $classId)
    {
        try {
            $validated = $request->validate([
                'student_ids' => 'required|array',
                'student_ids.*' => 'exists:users,id'
            ]);

            // Cari class berdasarkan ID
            $class = ClassRoom::findOrFail($classId);

            // Validasi bahwa semua ID adalah student yang valid
            $students = User::whereIn('id', $validated['student_ids'])
                           ->where('users.role', 'student')
                           ->get();

            if ($students->count() !== count($validated['student_ids'])) {
                return response()->json([
                    'message' => 'Some of the provided IDs are not valid students'
                ], 422);
            }

            // Cek apakah ada student yang sudah terdaftar di kelas
            $existingStudents = $class->students()
                                     ->where('class_users.role', 'student')
                                     ->whereIn('class_users.user_id', $validated['student_ids'])
                                     ->pluck('class_users.user_id');

            if ($existingStudents->isNotEmpty()) {
                return response()->json([
                    'message' => 'Some students are already assigned to this class',
                    'existing_student_ids' => $existingStudents
                ], 422);
            }

            // Attach students ke class
            foreach ($students as $student) {
                $class->students()->attach($student->id, [
                    'role' => 'student',
                    'status' => 'active'
                ]);
            }

            return response()->json([
                'message' => 'Students assigned successfully',
                'data' => new ClassResource($class->fresh(['students']))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to assign students',
                'error' => $e->getMessage()
            ], 500);
        }
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

    public function removeTeacher(Request $request, $classId)
    {
        try {
            $class = ClassRoom::findOrFail($classId);
            
            if (!$class->teacher_id) {
                return response()->json([
                    'message' => 'Class does not have a teacher assigned'
                ], 422);
            }

            // Hapus teacher dari class_users
            $class->students()->where('role', 'teacher')->detach();
            
            // Set teacher_id menjadi null di tabel classes
            $class->update(['teacher_id' => null]);

            return response()->json([
                'message' => 'Teacher removed successfully from class',
                'data' => new ClassResource($class->fresh(['teacher', 'students']))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to remove teacher',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function removeStudents(Request $request, $classId)
    {
        try {
            $validated = $request->validate([
                'student_ids' => 'required|array',
                'student_ids.*' => 'exists:users,id'
            ]);

            $class = ClassRoom::findOrFail($classId);
            $class->students()->detach($validated['student_ids']);

            return response()->json([
                'message' => 'Students removed successfully from class',
                'data' => new ClassResource($class->fresh(['teacher', 'students']))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to remove students',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 