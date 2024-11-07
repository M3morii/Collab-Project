<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\TaskController;
use App\Http\Controllers\API\TaskSubmissionController;
use App\Http\Controllers\API\AttachmentController;


// Public routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Protected routes
Route::middleware(['auth:sanctum'])->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    
    // Admin routes
    Route::middleware(['role:admin'])->prefix('admin')->group(function () {
        Route::get('/tasks', [TaskController::class, 'index']);
        Route::get('/tasks/{task}', [TaskController::class, 'show']);
        Route::post('/tasks', [TaskController::class, 'store']);
        Route::patch('/tasks/{task}', [TaskController::class, 'update']);
        Route::delete('/tasks/{task}', [TaskController::class, 'destroy']);
        Route::post('/tasks/{task}/assign', [TaskController::class, 'assign']);
        Route::get('/tasks/{task}/submissions', [TaskSubmissionController::class, 'index']);
        Route::post('/submissions/{submission}/review', [TaskSubmissionController::class, 'review']);
    });

    // Student routes
    Route::middleware(['role:student'])->prefix('student')->group(function () {
        Route::get('/assigned-tasks', [TaskController::class, 'assignedTasks']);
        Route::get('/tasks/{task}/my-submission', [TaskSubmissionController::class, 'mySubmission']);
        Route::get('/tasks/{task}', [TaskController::class, 'show']);
        Route::post('/tasks/{task}/submit', [TaskSubmissionController::class, 'store']);
    });

    // Attachments (accessible by both roles)
    Route::post('/tasks/{task}/attachments', [AttachmentController::class, 'store']);
    Route::delete('/attachments/{attachment}', [AttachmentController::class, 'destroy'])
        ->middleware('can:delete,attachment');
    Route::get('/attachments/{attachment}/download', [AttachmentController::class, 'download'])
        ->middleware('can:view,attachment');
});
