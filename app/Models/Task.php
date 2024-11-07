<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'status',
        'priority',
        'deadline',
        'created_by_id'
    ];

    protected $casts = [
        'deadline' => 'datetime'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function assignees()
    {
        return $this->belongsToMany(User::class, 'task_assignments', 'task_id', 'user_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function submissions()
    {
        return $this->hasMany(TaskSubmission::class);
    }

    public function pendingSubmissions()
    {
        return $this->submissions()->pending();
    }

    public function reviewedSubmissions()
    {
        return $this->submissions()->reviewed();
    }

    public function isAssignedTo($user)
    {
        return $this->assignees()
            ->where('task_assignments.user_id', $user->id)
            ->exists();
    }
} 