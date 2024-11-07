<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SubmissionAttachment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'task_submission_id',
        'filename',
        'file_path',
        'file_type',
        'file_size'
    ];

    protected $casts = [
        'file_size' => 'integer',
        'deleted_at' => 'datetime'
    ];

    public function submission()
    {
        return $this->belongsTo(TaskSubmission::class, 'task_submission_id');
    }
} 