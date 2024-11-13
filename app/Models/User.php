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

    // Classes yang diajar (sebagai teacher)
    public function teachingClasses()
    {
        return $this->hasMany(Classes::class, 'teacher_id');
    }

    // Classes yang diikuti (many-to-many)
    public function classes()
    {
        return $this->belongsToMany(Classes::class, 'class_users')
                    ->withPivot('role', 'status')
                    ->withTimestamps();
    }

    // Tasks yang dibuat (sebagai teacher)
    public function createdTasks()
    {
        return $this->hasMany(Task::class, 'created_by');
    }

    // Task Groups yang dibuat
    public function createdTaskGroups()
    {
        return $this->hasMany(TaskGroup::class, 'created_by');
    }

    // Keanggotaan dalam task groups
    public function taskGroups()
    {
        return $this->belongsToMany(TaskGroup::class, 'task_group_members')
                    ->withTimestamps();
    }

    // Submissions yang dibuat
    public function submissions()
    {
        return $this->hasMany(Submission::class, 'user_id');
    }

    // Uploaded attachments
    public function taskAttachments()
    {
        return $this->hasMany(TaskAttachment::class, 'uploaded_by');
    }

    public function submissionAttachments()
    {
        return $this->hasMany(SubmissionAttachment::class, 'uploaded_by');
    }
}