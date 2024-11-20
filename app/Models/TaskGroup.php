<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'name', 
        'description',
        'max_members',
        'created_by',
        'class_id'
    ];

    // Relasi yang benar
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'task_group_members')
                    ->withTimestamps();
    }

    // Tambahkan relasi class
    public function class()
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }
}