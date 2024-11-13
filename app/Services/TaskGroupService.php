<?php

namespace App\Services;

use App\Models\Task;
use App\Models\TaskGroup;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class TaskGroupService
{
    public function create(Task $task, array $data): TaskGroup
    {
        return DB::transaction(function () use ($task, $data) {
            $group = $task->taskGroups()->create([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'max_members' => $data['max_members'],
                'created_by' => auth()->id()
            ]);

            if (!empty($data['member_ids'])) {
                $group->members()->attach($data['member_ids']);
            }

            return $group;
        });
    }
} 