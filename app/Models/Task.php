<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'class_id',
        'title',
        'description',
        'start_date',
        'deadline',
        'task_type',
        'max_score',
        'weight_percentage',
        'status',
        'created_by'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'deadline' => 'datetime'
    ];

    public function class()
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function taskGroup()
    {
        return $this->hasMany(TaskGroup::class);
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function attachments()
    {
        return $this->hasMany('App\Models\TaskAttachment');
    }
    
}