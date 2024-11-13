<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Pagination\LengthAwarePaginator;

class UserManagementService
{
    public function getAllUsers(array $filters): LengthAwarePaginator
    {
        $query = User::query();

        // Filter by role
        if (!empty($filters['role'])) {
            $query->where('role', $filters['role']);
        }

        // Filter by status
        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        // Search by name or email
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                  ->orWhere('email', 'like', "%{$filters['search']}%");
            });
        }

        return $query->latest()->paginate(10);
    }

    public function getTeachers()
    {
        return User::where('role', 'teacher')
                  ->where('is_active', true)
                  ->get();
    }

    public function createTeacher(array $data): User
    {
        $data['role'] = 'teacher';
        $data['password'] = Hash::make($data['password']);
        
        return User::create($data);
    }

    public function updateUserStatus(User $user, bool $status): User
    {
        $user->update(['is_active' => $status]);
        return $user->fresh();
    }
} 