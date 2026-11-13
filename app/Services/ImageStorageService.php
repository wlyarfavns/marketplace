<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ImageStorageService
{
    /**
     * Store an uploaded image safely in both storage/app/public and public/storage.
     * Automatically optimizes and downscales massive photos if GD is available.
     *
     * @param UploadedFile|null $file
     * @param string $folder Subfolder e.g. 'products', 'shoes', 'brands', 'banners'
     * @param int $maxDimension Max width/height in pixels for auto-resizing
     * @return string|null Relative path e.g. 'products/abc123xyz.jpg'
     */
    public static function storeImage(?UploadedFile $file, string $folder = 'products', int $maxDimension = 1600): ?string
    {
        if (! $file || ! $file->isValid()) {
            return null;
        }

        try {
            $folder = trim($folder, '/');
            $extension = strtolower($file->getClientOriginalExtension() ?: 'jpg');
            
            // Normalize common extension aliases
            if ($extension === 'jpeg') {
                $extension = 'jpg';
            }

            $fileName = Str::random(40) . '.' . $extension;
            $relativePath = $folder . '/' . $fileName;

            // Target directories
            $storagePublicDir = storage_path('app/public/' . $folder);
            $publicStorageDir = public_path('storage/' . $folder);

            // Ensure directories exist with writable permissions
            if (! File::isDirectory($storagePublicDir)) {
                File::makeDirectory($storagePublicDir, 0775, true, true);
            }
            if (! File::isDirectory($publicStorageDir)) {
                File::makeDirectory($publicStorageDir, 0775, true, true);
            }

            $storageFullPath = $storagePublicDir . DIRECTORY_SEPARATOR . $fileName;
            $publicFullPath = $publicStorageDir . DIRECTORY_SEPARATOR . $fileName;

            $tempPath = $file->getRealPath();

            // Attempt GD optimization for large JPEGs, PNGs, and WEBPs
            $optimized = false;
            if (extension_loaded('gd') && in_array($extension, ['jpg', 'png', 'webp']) && file_exists($tempPath)) {
                $optimized = self::optimizeAndSaveWithGd($tempPath, $storageFullPath, $extension, $maxDimension);
                if ($optimized && file_exists($storageFullPath)) {
                    // Copy optimized file to public/storage
                    @copy($storageFullPath, $publicFullPath);
                }
            }

            // Fallback: If not optimized with GD, perform direct file stream copy
            if (! $optimized || ! file_exists($storageFullPath)) {
                @copy($tempPath, $storageFullPath);
                @copy($tempPath, $publicFullPath);
            }

            // Ensure file exists in at least one destination
            if (file_exists($storageFullPath) || file_exists($publicFullPath)) {
                return $relativePath;
            }

            return null;
        } catch (\Throwable $e) {
            Log::error("[ImageStorageService] Upload failed for folder '{$folder}': " . $e->getMessage(), [
                'exception' => $e,
            ]);

            return null;
        }
    }

    /**
     * Delete an image file from both storage/app/public and public/storage.
     * Never deletes default assets inside public/images/.
     */
    public static function deleteImage(?string $path): void
    {
        if (empty($path)) {
            return;
        }

        $cleanPath = ltrim($path, '/');

        // Never delete default seeded assets
        if (str_starts_with($cleanPath, 'images/')) {
            return;
        }

        // Strip leading storage/ prefix if present
        if (str_starts_with($cleanPath, 'storage/')) {
            $cleanPath = substr($cleanPath, 8);
        }

        try {
            $storageFile = storage_path('app/public/' . $cleanPath);
            if (file_exists($storageFile) && is_file($storageFile)) {
                @unlink($storageFile);
            }

            $publicFile = public_path('storage/' . $cleanPath);
            if (file_exists($publicFile) && is_file($publicFile)) {
                @unlink($publicFile);
            }
        } catch (\Throwable $e) {
            // Silently ignore deletion errors
        }
    }

    /**
     * Downscale and optimize an image using PHP GD to save disk space and bandwidth.
     */
    protected static function optimizeAndSaveWithGd(string $sourcePath, string $destinationPath, string $extension, int $maxDimension): bool
    {
        try {
            [$origWidth, $origHeight, $imageType] = @getimagesize($sourcePath);
            if (! $origWidth || ! $origHeight) {
                return false;
            }

            // Create GD source resource
            $srcImg = match ($imageType) {
                IMAGETYPE_JPEG => @imagecreatefromjpeg($sourcePath),
                IMAGETYPE_PNG => @imagecreatefrompng($sourcePath),
                IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($sourcePath) : null,
                default => null,
            };

            if (! $srcImg) {
                return false;
            }

            // Calculate resized dimensions
            $newWidth = $origWidth;
            $newHeight = $origHeight;

            if ($origWidth > $maxDimension || $origHeight > $maxDimension) {
                if ($origWidth >= $origHeight) {
                    $newWidth = $maxDimension;
                    $newHeight = (int) round(($origHeight / $origWidth) * $maxDimension);
                } else {
                    $newHeight = $maxDimension;
                    $newWidth = (int) round(($origWidth / $origHeight) * $maxDimension);
                }
            }

            $dstImg = imagecreatetruecolor($newWidth, $newHeight);

            // Preserve transparency for PNG and WEBP
            if (in_array($extension, ['png', 'webp'])) {
                imagealphablending($dstImg, false);
                imagesavealpha($dstImg, true);
                $transparent = imagecolorallocatealpha($dstImg, 255, 255, 255, 127);
                imagefilledrectangle($dstImg, 0, 0, $newWidth, $newHeight, $transparent);
            }

            imagecopyresampled($dstImg, $srcImg, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);

            // Save optimized destination image
            $saved = false;
            if ($extension === 'jpg') {
                $saved = @imagejpeg($dstImg, $destinationPath, 85);
            } elseif ($extension === 'png') {
                $saved = @imagepng($dstImg, $destinationPath, 6);
            } elseif ($extension === 'webp' && function_exists('imagewebp')) {
                $saved = @imagewebp($dstImg, $destinationPath, 85);
            }

            imagedestroy($srcImg);
            imagedestroy($dstImg);

            return $saved;
        } catch (\Throwable $e) {
            return false;
        }
    }
}
