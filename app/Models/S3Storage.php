<?php

namespace App\Models;

use Illuminate\Support\Facades\Storage;

/**
 * Handles storing and retrieving of S3 uploads and presigned links
 */
class S3Storage
{
    const TIMEOUT = 60;

    /**
     * @param string $fileName
     * @param mixed $content
     * @param string $visibility
     */
    public static function uploadPhoto(string $fileName, mixed $content, string $visibility = 'public'): void
    {
        Storage::put($fileName, $content, $visibility);
    }

    /**
     * @param string $imageName
     * @return string
     */
    public static function getTemporaryUrl(string $imageName): string
    {
        return Storage::temporaryUrl(
            $imageName,
            now()->addMinutes(self::TIMEOUT)
        );
    }

    /**
     * @param string $imageName
     * @return string
     */
    public static function getTemporaryUploadUrl(string $imageName): mixed
    {
        $response = Storage::temporaryUploadUrl(
            $imageName,
            now()->addMinutes(self::TIMEOUT)
        );

        return $response['url'];
    }
}
