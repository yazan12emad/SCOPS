<?php
namespace App\Support;

use Cloudinary\Cloudinary;

class CloudinaryUploader
{
    public static function upload(string $filePath, array $options = [])
    {
        $cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                'api_key'    => env('CLOUDINARY_API_KEY'),
                'api_secret' => env('CLOUDINARY_API_SECRET'),
            ],
        ]);

        return $cloudinary->uploadApi()->upload($filePath, $options);
    }
}
