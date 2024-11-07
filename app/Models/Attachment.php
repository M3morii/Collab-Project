<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attachment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'filename',
        'file_path',
        'file_type',
        'file_size',
        'task_id',
        'uploaded_by_id'
    ];

    protected $casts = [
        'file_size' => 'integer',
        'deleted_at' => 'datetime'
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by_id');
    }
} 