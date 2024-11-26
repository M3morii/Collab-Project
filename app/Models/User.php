<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name', 'email', 'password', 'role',
        'avatar', 'phone', 'address', 'is_active'
    ];

    protected $hidden = ['password', 'remember_token'];

    // Ganti Classes menjadi ClassRoom
    public function teachingClasses()
    {
        return $this->hasMany(ClassRoom::class, 'teacher_id');
    }

    // Ganti Classes menjadi ClassRoom
    public function classes()
    {
        return $this->belongsToMany(ClassRoom::class, 'class_users', 'user_id', 'class_id')
                    ->withPivot('role', 'status')
                    ->withTimestamps();
    }

    // Relasi lainnya tetap sama
    public function createdTasks()
    {
        return $this->hasMany(Task::class, 'created_by');
    }

    public function createdTaskGroups()
    {
        return $this->hasMany(TaskGroup::class, 'created_by');
    }

    public function taskGroups()
    {
        return $this->belongsToMany(TaskGroup::class, 'task_group_members')
                    ->withTimestamps();
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class, 'user_id');
    }

    public function taskAttachments()
    {
        return $this->hasMany(TaskAttachment::class, 'uploaded_by');
    }

    public function submissionAttachments()
    {
        return $this->hasMany(SubmissionAttachment::class, 'uploaded_by');
    }
}