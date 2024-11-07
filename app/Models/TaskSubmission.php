<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TaskSubmission extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'task_id',
        'student_id',
        'content',
        'status',
        'feedback',
        'score',
        'submitted_at',
        'reviewed_at',
        'reviewed_by_id'
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'score' => 'integer',
        'deleted_at' => 'datetime'
    ];

    // Relationships
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by_id');
    }

    public function attachments()
    {
        return $this->hasMany(SubmissionAttachment::class, 'task_submission_id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'submitted');
    }

    public function scopeReviewed($query)
    {
        return $query->where('status', 'reviewed');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    // Methods
    public function markAsReviewed($feedback, $score, $reviewerId)
    {
        $this->update([
            'status' => 'reviewed',
            'feedback' => $feedback,
            'score' => $score,
            'reviewed_at' => now(),
            'reviewed_by_id' => $reviewerId
        ]);
    }

    public function markAsRejected($feedback, $reviewerId)
    {
        $this->update([
            'status' => 'rejected',
            'feedback' => $feedback,
            'reviewed_at' => now(),
            'reviewed_by_id' => $reviewerId
        ]);
    }

    // Add accessor for status label
    public function getStatusLabelAttribute()
    {
        return ucfirst($this->status);
    }

    // Add method to check if can be reviewed
    public function canBeReviewed()
    {
        return $this->status === 'submitted';
    }
} 