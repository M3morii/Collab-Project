<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubmissionAttachment extends Model
{
    protected $fillable = [
        'submission_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'uploaded_by'
    ];

    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}