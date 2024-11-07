<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AttachmentController extends Controller
{
    public function index(Task $task)
    {
        return response()->json($task->attachments()->with('uploadedBy')->get());
    }

    public function store(Request $request, Task $task)
    {
        $request->validate([
            'file' => 'required|file|max:10240' // max 10MB
        ]);

        $file = $request->file('file');
        $path = $file->store('attachments', 'public');

        $attachment = $task->attachments()->create([
            'filename' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
            'uploaded_by_id' => auth()->id()
        ]);

        return response()->json($attachment->load('uploadedBy'), 201);
    }

    public function destroy(Attachment $attachment)
    {
        $this->authorize('delete', $attachment);

        Storage::disk('public')->delete($attachment->file_path);
        $attachment->delete();

        return response()->json(['message' => 'Attachment deleted successfully']);
    }

    public function download(Attachment $attachment)
    {
        return Storage::disk('public')->download(
            $attachment->file_path, 
            $attachment->filename
        );
    }
} 