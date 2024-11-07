<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'content',
        'task_id',
        'user_id'
    ];

    protected $casts = [
        'deleted_at' => 'datetime'
    ];

    // User yang membuat komentar
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Task yang dikomentari
    public function task()
    {
        return $this->belongsTo(Task::class);
    }
} 