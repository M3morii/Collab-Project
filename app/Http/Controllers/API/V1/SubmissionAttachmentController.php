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

        try {
            // Validasi semua file
            if (!$this->fileService->validateMultipleFiles(
                $request->file('attachments'),
                ['application/pdf', 'application/msword', 'image/jpeg', 'image/png'],
                10240
            )) {
                return response()->json([
                    'message' => 'Invalid file type or size'
                ], 422);
            }

            $attachments = [];
            foreach ($request->file('attachments') as $file) {
                $fileInfo = $this->fileService->uploadWithValidation(
                    $file,
                    'submissions',
                    ['application/pdf', 'application/msword', 'image/jpeg', 'image/png'],
                    10240
                );

                $attachments[] = $submission->attachments()->create([
                    'file_name' => $fileInfo['file_name'],
                    'file_path' => $fileInfo['file_path'],
                    'file_type' => $fileInfo['file_type'],
                    'file_size' => $fileInfo['file_size'],
                    'uploaded_by' => auth()->id()
                ]);
            }

            return SubmissionAttachmentResource::collection(collect($attachments));

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to upload attachments',
                'error' => $e->getMessage()
            ], 500);
        }
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