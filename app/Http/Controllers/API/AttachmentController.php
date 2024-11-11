<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AttachmentController extends Controller
{
    public function download(Attachment $attachment)
    {
        $this->authorize('view', $attachment);

        return Storage::download($attachment->file_path, $attachment->filename);
    }

    public function destroy(Attachment $attachment)
    {
        $this->authorize('delete', $attachment);

        Storage::delete($attachment->file_path);
        $attachment->delete();

        return response()->json(['message' => 'File berhasil dihapus']);
    }
}