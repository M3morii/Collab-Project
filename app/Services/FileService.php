<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileService
{
    /**
     * Upload file (versi singkat untuk submission dan task)
     *
     * @param UploadedFile $file
     * @param string $path
     * @return string
     */
    public function upload(UploadedFile $file, string $path): string
    {
        $fileName = Str::random(40) . '.' . $file->getClientOriginalExtension();
        return $file->storeAs($path, $fileName);
    }

    /**
     * Delete file
     *
     * @param string|null $path
     * @return bool
     */
    public function delete(?string $path): bool
    {
        if ($path && Storage::exists($path)) {
            return Storage::delete($path);
        }
        return false;
    }

    /**
     * Download file dengan custom filename
     *
     * @param string $path
     * @param string|null $customName
     * @return mixed
     */
    public function download(string $path, ?string $customName = null)
    {
        if (Storage::exists($path)) {
            return Storage::download($path, $customName);
        }
        return null;
    }

    /**
     * Upload file dengan validasi (untuk task attachments)
     *
     * @param UploadedFile $file
     * @param string $path
     * @param array $allowedTypes
     * @param int $maxSize
     * @return array
     */
    public function uploadWithValidation(UploadedFile $file, string $path, array $allowedTypes = [], int $maxSize = 10240): array
    {
        // Validasi file
        if (!empty($allowedTypes) && !in_array($file->getClientMimeType(), $allowedTypes)) {
            throw new \Exception('Invalid file type');
        }

        if ($file->getSize() > $maxSize * 1024) {
            throw new \Exception('File size exceeds maximum limit');
        }

        // Upload file
        $filePath = $this->upload($file, $path);

        return [
            'file_path' => $filePath,
            'file_name' => $file->getClientOriginalName(),
            'file_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize()
        ];
    }

    /**
     * Validasi multiple files
     *
     * @param array $files
     * @param array $allowedTypes
     * @param int $maxSize
     * @return bool
     */
    public function validateMultipleFiles(array $files, array $allowedTypes = [], int $maxSize = 10240): bool
    {
        foreach ($files as $file) {
            if (!$file instanceof UploadedFile) {
                return false;
            }

            if (!empty($allowedTypes) && !in_array($file->getClientMimeType(), $allowedTypes)) {
                return false;
            }

            if ($file->getSize() > $maxSize * 1024) {
                return false;
            }
        }

        return true;
    }
}