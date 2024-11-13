<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SubmissionAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'submission_id',
        'uploaded_by_id',
        'filename',
        'file_path'
    ];

    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by_id');
    }
}