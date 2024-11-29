<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\AuthController;
use App\Http\Controllers\API\V1\Teacher\TaskController as TeacherTaskController;
use App\Http\Controllers\API\V1\Teacher\TeacherDashboardController;
use App\Models\Task;
use App\Http\Controllers\API\V1\Admin\ClassManagementController as ClassController;
use App\Http\Controllers\API\V1\Admin\DashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Guest routes

    Route::get('/', function () {
        return redirect()->route('login');
    });

    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    Route::get('/register', function () {
        return view('auth.register');
    })->name('register');

    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');


// Admin routes
    Route::get('/admin/dashboard', function () {
        $tasks = Task::orderBy('created_at', 'desc')->get();
        return view('admin.dashboard', compact('tasks'));
    })->name('admin.dashboard');
    
    Route::get('/admin/classes', [ClassController::class, 'index'])->name('admin.classes.index');
    Route::post('/admin/classes', [ClassController::class, 'store'])->name('admin.classes.store');
    Route::put('/admin/classes/{class}', [ClassController::class, 'update'])->name('admin.classes.update');
    Route::delete('/admin/classes/{class}', [ClassController::class, 'destroy'])->name('admin.classes.destroy');


// Teacher routes
    // Dashboard
    Route::get('/teacher/dashboard', [TeacherDashboardController::class, 'index'])->name('teacher.dashboard');
    
    // // Classes
    // Route::get('/teacher/classes', [TeacherDashboardController::class, 'getAssignedClasses'])->name('teacher.classes');
    
    // Tasks
    Route::get('/teacher/classes/{classId}/tasks', [TeacherTaskController::class, 'index'])->name('teacher.tasks.index');
    Route::get('/teacher/classes/{class}/group', [DashboardController::class, 'group'])->name('teacher.group');
    Route::post('/teacher/classes/{class}/tasks/{task}/groups', [TeacherTaskController::class, 'createGroup'])->name('teacher.tasks.groups.create');
    Route::post('/teacher/classes/{classId}/tasks/{taskId}/groups', [TaskController::class, 'createGroup'])->name('teacher.tasks.groups.create');


// Route untuk student dashboard dan kelas

    // Dashboard
    Route::get('/student/dashboard', function () {
        return view('student.dashboard');
    })->name('student.dashboard');
    
    // Detail Kelas - Perbaiki parameter
    Route::get('/student/classes/{classId}', function ($classId) {
        $class = \App\Models\ClassRoom::findOrFail($classId);
        
        // Pastikan siswa adalah anggota kelas
        if (!$class->students()->where('users.id', auth()->id())->exists()) {
            abort(403, 'Anda bukan anggota kelas ini.');
        }
        
        return view('student.classes.show', compact('class'));
    })->name('student.classes.show');

// Pastikan route redirect setelah login sesuai role
Route::get('/dashboard', function () {
    if (auth()->user()->role === 'student') {
        return redirect()->route('student.dashboard');
    }
    // Handle role lain jika ada
    return redirect('/');
})->middleware(['auth'])->name('dashboard');

