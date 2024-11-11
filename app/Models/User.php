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
        'is_active'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Teacher: kelas yang diajar
    public function teacherClasses()
    {
        return $this->hasMany(Classes::class, 'teacher_id');
    }

    // Student: keanggotaan kelompok
    public function groupMembers()
    {
        return $this->hasMany(GroupMember::class, 'student_id');
    }

    // Student: kelompok yang diikuti
    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_members', 'student_id', 'group_id');
    }

    // Teacher: tugas yang dibuat
    public function createdTasks()
    {
        return $this->hasMany(Task::class, 'created_by_id');
    }

    // File yang diupload
    public function uploadedAttachments()
    {
        return $this->hasMany(Attachment::class, 'uploaded_by_id');
    }

    // Role checks
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isTeacher()
    {
        return $this->role === 'teacher';
    }

    public function isStudent()
    {
        return $this->role === 'student';
    }
}