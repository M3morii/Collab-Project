<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Comment;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index(Task $task)
    {
        return response()->json($task->comments()->with('user')->latest()->get());
    }

    public function store(Request $request, Task $task)
    {
        $request->validate([
            'content' => 'required|string'
        ]);

        $comment = $task->comments()->create([
            'content' => $request->content,
            'user_id' => auth()->id()
        ]);

        // Notify task creator and assignees
        $usersToNotify = $task->assignees->push($task->creator);
        foreach ($usersToNotify as $user) {
            if ($user->id !== auth()->id()) {
                $this->notificationService->createCommentNotification($user, $task, $comment);
            }
        }

        return response()->json($comment->load('user'), 201);
    }

    public function update(Request $request, Comment $comment)
    {
        $this->authorize('update', $comment);

        $request->validate([
            'content' => 'required|string'
        ]);

        $comment->update($request->only('content'));

        return response()->json($comment->load('user'));
    }

    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);
        
        $comment->delete();
        return response()->json(['message' => 'Comment deleted successfully']);
    }
} 