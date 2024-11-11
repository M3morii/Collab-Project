<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'class_id'
    ];

    public function class()
    {
        return $this->belongsTo(Classes::class);
    }

    public function members()
    {
        return $this->hasMany(GroupMember::class);
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'group_members', 'group_id', 'student_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}