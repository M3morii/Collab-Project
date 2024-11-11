<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileService
{
    public function uploadFile(UploadedFile $file, string $path): string
    {
        return $file->store($path);
    }

    public function deleteFile(string $path): bool
    {
        return Storage::delete($path);
    }
} 