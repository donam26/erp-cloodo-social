<?php

namespace App\Utilities;

use Illuminate\Support\Facades\Storage;

class ImageUploader
{
    /**
     * Upload base64 image to S3
     *
     * @param string $base64Image
     * @param string $folder
     * @return string URL of uploaded image
     */
    public static function uploadBase64Image($base64Image, $folder = 'images')
    {
        $image = str_replace('data:image/jpeg;base64,', '', $base64Image);
        $image = str_replace(' ', '+', $image);
        $imageBinary = base64_decode($image);
        $fileName = time() . '_' . uniqid() . '.jpg';
        $path = $folder . '/' . $fileName;
        Storage::disk('s3')->put($path, $imageBinary);
        return config('filesystems.disks.s3.url') . '/' . $path;
    }
} 