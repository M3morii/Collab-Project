<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Classes extends Model
{
    protected $fillable = [
        'name',
        'description',
        'teacher_id',
        'academic_year',
        'semester',
        'status',
        'kkm_score'
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'class_user', 'class_id', 'user_id')
                    ->where('role', 'student');
    }
}