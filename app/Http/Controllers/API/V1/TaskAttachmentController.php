<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaskAttachmentResource;
use App\Models\Task;
use App\Models\TaskAttachment;
use Illuminate\Http\Request;
use App\Services\FileService;

class TaskAttachmentController extends Controller
{
    protected $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    public function store(Request $request, Task $task)
    {
        $this->authorize('update', $task);

        $request->validate([
            'attachments.*' => 'required|file|max:10240' // max 10MB
        ]);

        $attachments = [];
        foreach ($request->file('attachments') as $file) {
            $attachments[] = $task->attachments()->create([
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $this->fileService->upload($file, 'tasks'),
                'file_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
                'uploaded_by' => auth()->id()
            ]);
        }

        return TaskAttachmentResource::collection(collect($attachments));
    }

    public function destroy(TaskAttachment $attachment)
    {
        $this->authorize('delete', $attachment);

        // Delete file from storage
        $this->fileService->delete($attachment->file_path);
        
        $attachment->delete();

        return response()->json(['message' => 'Attachment deleted successfully']);
    }

    public function download(TaskAttachment $attachment)
    {
        $this->authorize('view', $attachment);

        return $this->fileService->download($attachment->file_path, $attachment->file_name);
    }
}