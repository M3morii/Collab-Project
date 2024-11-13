<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\SubmissionAttachmentResource;
use App\Models\Submission;
use App\Models\SubmissionAttachment;
use Illuminate\Http\Request;
use App\Services\FileService;

class SubmissionAttachmentController extends Controller
{
    protected $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    public function store(Request $request, Submission $submission)
    {
        $this->authorize('update', $submission);

        $request->validate([
            'attachments.*' => 'required|file|max:10240' // max 10MB
        ]);

        $attachments = [];
        foreach ($request->file('attachments') as $file) {
            $attachments[] = $submission->attachments()->create([
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $this->fileService->upload($file, 'submissions'),
                'file_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
                'uploaded_by' => auth()->id()
            ]);
        }

        return SubmissionAttachmentResource::collection(collect($attachments));
    }

    public function destroy(SubmissionAttachment $attachment)
    {
        $this->authorize('delete', $attachment);

        // Delete file from storage
        $this->fileService->delete($attachment->file_path);
        
        $attachment->delete();

        return response()->json(['message' => 'Attachment deleted successfully']);
    }

    public function download(SubmissionAttachment $attachment)
    {
        $this->authorize('view', $attachment);

        return $this->fileService->download($attachment->file_path, $attachment->file_name);
    }
}