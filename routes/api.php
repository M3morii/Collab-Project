<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\{
    AuthController,
    EmailVerificationController,
    Teacher\SubmissionController,
    Admin\DashboardController,
    Admin\UserManagementController,
    Admin\ClassManagementController,
    Teacher\TaskGroupController,
    Teacher\TeacherDashboardController,
    Teacher\TaskController,
    Student\StudentTaskController,
    Student\StudentDashboardController,
    Student\StudentSubmissionController
};

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::prefix('v1')->group(function () {
    // Auth Routes yang tidak memerlukan verifikasi
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
});

// Protected routes yang membutuhkan verifikasi
Route::prefix('v1')->middleware(['auth:sanctum'])->group(function () {
    // Email verification routes
    Route::get('email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->name('verification.verify')
        ->middleware(['signed'])
        ->withoutMiddleware(['auth:sanctum']);
        
    Route::post('email/verification-notification', [EmailVerificationController::class, 'sendVerificationEmail'])
        ->middleware('throttle:6,1');

    // Route lainnya yang membutuhkan verifikasi
    Route::middleware('verified')->group(function () {
        // Common routes
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('profile', [AuthController::class, 'profile']);
        Route::post('profile/update', [AuthController::class, 'updateProfile']);

        // Student Routes
        Route::prefix('student')->middleware(['role:student'])->group(function () {
            // View Classes & Tasks
            Route::get('dashboard/overview', [StudentDashboardController::class, 'overview']);

            Route::get('tasks', [StudentTaskController::class, 'index']);
            Route::get('tasks/{taskId}', [StudentTaskController::class, 'show']);
            Route::get('classes/{classId}/tasks', [StudentTaskController::class, 'getTasksByClass']);
            Route::get('tasks/{taskId}/detail', [StudentTaskController::class, 'getTaskDetail']);
            Route::get('tasks/{taskId}/attachments/{attachmentId}/download', [StudentTaskController::class, 'downloadAttachment'])
                ->name('student.task.download-attachment');
            Route::get('tasks/{taskId}/group-members', [StudentTaskController::class, 'getGroupMembers']);
            Route::post('tasks/{taskId}/submissions', [StudentSubmissionController::class, 'store']);
        });

        // Admin Routes
        Route::prefix('admin')->middleware(['role:admin'])->group(function () {
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
        Route::prefix('teacher')->middleware(['role:teacher'])->group(function () {
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
            Route::get('classes/{classId}/students', [TaskGroupController::class, 'getClassStudents']);
            // Submissions & Grading
            Route::get('submissions', [SubmissionController::class, 'index']);
            Route::get('submissions/{submission}', [SubmissionController::class, 'show']);
            Route::post('submissions/{submission}/grade', [SubmissionController::class, 'grade']);
            Route::get('tasks/{task}/submissions', [SubmissionController::class, 'taskSubmissions']);
        });
    });
});

Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('reset-password', [AuthController::class, 'resetPassword']);

