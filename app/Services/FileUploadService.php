<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileUploadService
{
    protected array $uploadedPaths = [];

    public function uploadMultiple(array $files, string $path): array
    {
        $paths = [];

        foreach ($files as $file) {
            /** @var UploadedFile $file */
            $storedPath = $file->store($path, 'public');
            $paths[] = $storedPath;
            $this->uploadedPaths[] = $storedPath;
        }

        return $paths;
    }

    public function rollbackUploads(): void
    {
        foreach ($this->uploadedPaths as $path) {
            Storage::disk('public')->delete($path);
        }

        $this->uploadedPaths = [];
    }
}
