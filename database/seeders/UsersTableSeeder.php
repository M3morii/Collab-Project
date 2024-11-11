<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        // Create Admin
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'is_active' => true
        ]);

        // Create Teachers
        $teachers = [
            [
                'name' => 'Guru IPA',
                'email' => 'guru.ipa@example.com',
                'password' => Hash::make('teacher123'),
                'role' => 'teacher',
                'is_active' => true
            ],
            [
                'name' => 'Guru Matematika',
                'email' => 'guru.mtk@example.com',
                'password' => Hash::make('teacher123'),
                'role' => 'teacher',
                'is_active' => true
            ],
            // Tambah guru lain sesuai kebutuhan
        ];

        foreach ($teachers as $teacher) {
            User::create($teacher);
        }
    }
} 