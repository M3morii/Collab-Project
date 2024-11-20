<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\{
    AuthController,
    ClassController,
    TaskAttachmentController,
    SubmissionAttachmentController,
    Teacher\SubmissionController,
    Admin\DashboardController,
    Admin\UserManagementController,
    Admin\ClassManagementController,
    Teacher\TaskGroupController,
    Teacher\TeacherDashboardController,
    Teacher\TaskController,
    Student\StudentTaskController,
    Student\StudentDashboardController
};

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::prefix('v1')->group(function () {
    // Auth Routes
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
});

// Protected routes
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    // Common routes (accessible by all authenticated users)
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('profile', [AuthController::class, 'profile']);
});
    // Student Routes
    Route::prefix('v1/student')->middleware(['auth:sanctum', 'role:student'])->group(function () {
        // View Classes & Tasks
        Route::get('dashboard/overview', [StudentDashboardController::class, 'overview']);

        Route::get('tasks', [StudentTaskController::class, 'index']);
        Route::get('tasks/{taskId}', [StudentTaskController::class, 'show']);
        Route::get('classes/{classId}/tasks', [StudentTaskController::class, 'getTasksByClass']);
        Route::get('tasks/{taskId}/detail', [StudentTaskController::class, 'getTaskDetail']);
        Route::get('tasks/{taskId}/attachments/{attachmentId}/download', [StudentTaskController::class, 'downloadAttachment'])
            ->name('student.task.download-attachment');
    });

// Admin Routes
Route::prefix('v1/admin')->middleware(['auth:sanctum', 'role:admin'])->group(function () {
    // Dashboard Overview
    Route::get('dashboard/overview', [DashboardController::class, 'overview']);
    
    // User Management
    Route::apiResource('users', UserManagementController::class);
    Route::patch('users/{user}/status', [UserManagementController::class, 'updateStatus']);
    Route::get('users/teachers', [UserManagementController::class, 'getTeachers']);
    Route::get('users/students', [UserManagementController::class, 'getStudents']);
    
    // Class Management
    Route::get('classes', [ClassManagementController::class, 'index']);
    Route::post('classes', [ClassManagementController::class, 'store']);
    Route::get('classes/{classId}', [ClassManagementController::class, 'show']);
    Route::put('classes/{classId}', [ClassManagementController::class, 'update']);
    Route::delete('classes/{classId}', [ClassManagementController::class, 'destroy']);
    Route::post('classes/{classId}/assign-teacher', [ClassManagementController::class, 'assignTeacher']);
    Route::post('classes/{classId}/assign-students', [ClassManagementController::class, 'assignStudents']);
    Route::delete('classes/{classId}/teacher', [ClassManagementController::class, 'removeTeacher']);
    Route::post('classes/{classId}/students/remove', [ClassManagementController::class, 'removeStudents']);
    Route::get('classes/{classId}/available-students', [ClassManagementController::class, 'getAvailableStudents']);
    
    // Statistics & Reports
    Route::get('submissions/stats', [SubmissionController::class, 'adminStats']);
});

// Teacher Routes
Route::prefix('v1/teacher')->middleware(['auth:sanctum', 'role:teacher'])->group(function () {
    // Classes
    Route::get('classes', [TeacherDashboardController::class, 'getAssignedClasses']);
    
    // Tasks
    Route::get('classes/{classId}/tasks', [TaskController::class, 'index']);
    Route::post('classes/{classId}/tasks', [TaskController::class, 'store']);
    Route::get('classes/{classId}/tasks/{taskId}', [TaskController::class, 'show']);
    Route::put('classes/{classId}/tasks/{taskId}', [TaskController::class, 'update']);
    Route::delete('classes/{classId}/tasks/{taskId}', [TaskController::class, 'destroy']);
    
    // Task Groups
    Route::get('classes/{classId}/tasks/{taskId}/groups', [TaskGroupController::class, 'index']);
    Route::post('classes/{classId}/tasks/{taskId}/groups', [TaskGroupController::class, 'store']);
    
    // Task Attachments
    Route::get('classes/{classId}/tasks/{taskId}/attachments', [TaskAttachmentController::class, 'index']);
    Route::post('classes/{classId}/tasks/{taskId}/attachments', [TaskAttachmentController::class, 'store']);
    Route::delete('classes/{classId}/tasks/{taskId}/attachments/{attachmentId}', [TaskAttachmentController::class, 'destroy']);
    
    // Submissions & Grading
    Route::get('submissions', [SubmissionController::class, 'index']);
    Route::get('submissions/{submission}', [SubmissionController::class, 'show']);
    Route::post('submissions/{submission}/grade', [SubmissionController::class, 'grade']);
    Route::get('tasks/{task}/submissions', [SubmissionController::class, 'taskSubmissions']);
});
// Public registration (student only)
Route::post('v1/register', [AuthController::class, 'register']);
