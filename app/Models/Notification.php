<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'content',
        'is_read',
        'user_id',
        'task_id',
        'data'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'data' => 'array'
    ];

    // User penerima notifikasi
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Task terkait notifikasi
    public function task()
    {
        return $this->belongsTo(Task::class);
    }
} 