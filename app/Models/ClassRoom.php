<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClassRoom extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'classes';

    protected $fillable = [
        'name',
        'description',
        'teacher_id',
        'kkm_score',
        'academic_year',
        'semester',
        'status'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'kkm_score' => 'integer'
    ];

    /**
     * Get the teacher that owns the class.
     */
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Get the students for the class.
     */
    public function students()
    {
        return $this->belongsToMany(User::class, 'class_users', 'class_id', 'user_id')
                    ->withPivot('role', 'status')
                    ->withTimestamps();
    }

    /**
     * Scope a query to only include active classes.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
} 