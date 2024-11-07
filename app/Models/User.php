<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'avatar'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime'
    ];

    // Tasks yang dibuat user
    public function createdTasks()
    {
        return $this->hasMany(Task::class, 'created_by_id');
    }
    

    // Tasks yang ditugaskan ke user
    public function assignedTasks()
    {
        return $this->belongsToMany(Task::class, 'task_assignments', 'user_id', 'task_id')
            ->withTimestamps();
    }

    // Komentar yang dibuat user
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    // Notifikasi user
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    // File yang diupload user
    public function uploadedAttachments()
    {
        return $this->hasMany(Attachment::class, 'uploaded_by_id');
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isStudent()
    {
        return $this->role === 'student';
    }

    // Add submission relationship
    public function submissions()
    {
        return $this->hasMany(TaskSubmission::class, 'student_id');
    }

    public function reviewedSubmissions()
    {
        return $this->hasMany(TaskSubmission::class, 'reviewed_by_id');
    }
}
