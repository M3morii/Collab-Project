<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    protected $fillable = [
        'task_id',
        'user_id', 
        'task_group_id',
        'content',
        'score',
        'feedback',
        'status',
        'submitted_at'
    ];

    protected $dates = [
        'submitted_at',
        'created_at',
        'updated_at'
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'user_id')->where('role', 'student');
    }

    public function taskGroup()
    {
        return $this->belongsTo(TaskGroup::class);
    }

    public function attachments()
    {
        return $this->hasMany(SubmissionAttachment::class);
    }
}