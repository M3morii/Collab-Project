<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id', 'user_id', 'task_group_id',
        'content', 'score', 'feedback', 'status',
        'submitted_at'
    ];

    protected $casts = [
        'submitted_at' => 'datetime'
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
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