<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class FileStorageService
{
    /**
     * Store a file in public storage (e.g., product images)
     *
     * @param UploadedFile $file
     * @param string $directory
     * @return string File path
     */
    public static function storePublic(UploadedFile $file, string $directory = 'images'): string
    {
        $filename = self::generateUniqueFilename($file);
        $path = $file->storeAs($directory, $filename, 'public');
        
        return $path;
    }

    /**
     * Store a file in private storage
     *
     * @param UploadedFile $file
     * @param string $disk (invoices, shipping, warehouse, private)
     * @param string $directory
     * @return string File path
     */
    public static function storePrivate(UploadedFile $file, string $disk = 'private', string $directory = ''): string
    {
        $filename = self::generateUniqueFilename($file);
        $path = $file->storeAs($directory, $filename, $disk);
        
        return $path;
    }

    /**
     * Store invoice PDF for an order
     *
     * @param UploadedFile|string $file File or content
     * @param int $orderId
     * @return string File path
     */
    public static function storeInvoice($file, int $orderId): string
    {
        $filename = "invoice-{$orderId}-" . now()->format('Ymd-His') . '.pdf';
        
        if ($file instanceof UploadedFile) {
            return $file->storeAs('', $filename, 'invoices');
        }
        
        // Store from content (string)
        Storage::disk('invoices')->put($filename, $file);
        return $filename;
    }

    /**
     * Store shipping label
     *
     * @param UploadedFile|string $file
     * @param int $orderId
     * @return string File path
     */
    public static function storeShippingLabel($file, int $orderId): string
    {
        $filename = "shipping-label-{$orderId}-" . now()->format('Ymd-His') . '.pdf';
        
        if ($file instanceof UploadedFile) {
            return $file->storeAs('', $filename, 'shipping');
        }
        
        Storage::disk('shipping')->put($filename, $file);
        return $filename;
    }

    /**
     * Store warehouse document
     *
     * @param UploadedFile $file
     * @param string $type (packing-slip, manifest, etc.)
     * @return string File path
     */
    public static function storeWarehouseDocument(UploadedFile $file, string $type = 'document'): string
    {
        $filename = $type . '-' . now()->format('Ymd-His') . '.' . $file->getClientOriginalExtension();
        return $file->storeAs('', $filename, 'warehouse');
    }

    /**
     * Retrieve a file from storage
     *
     * @param string $path
     * @param string $disk
     * @return string|null File contents
     */
    public static function get(string $path, string $disk = 'public'): ?string
    {
        if (!Storage::disk($disk)->exists($path)) {
            return null;
        }
        
        return Storage::disk($disk)->get($path);
    }

    /**
     * Get public URL for a file
     *
     * @param string $path
     * @return string
     */
    public static function getPublicUrl(string $path): string
    {
        return asset('storage/' . $path);
    }

    /**
     * Check if file exists
     *
     * @param string $path
     * @param string $disk
     * @return bool
     */
    public static function exists(string $path, string $disk = 'public'): bool
    {
        return Storage::disk($disk)->exists($path);
    }

    /**
     * Delete a file
     *
     * @param string $path
     * @param string $disk
     * @return bool
     */
    public static function delete(string $path, string $disk = 'public'): bool
    {
        if (self::exists($path, $disk)) {
            return Storage::disk($disk)->delete($path);
        }
        
        return false;
    }

    /**
     * Download a private file (for authorized users)
     *
     * @param string $path
     * @param string $disk
     * @param string|null $downloadName
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public static function download(string $path, string $disk = 'private', ?string $downloadName = null)
    {
        $filePath = Storage::disk($disk)->path($path);
        $name = $downloadName ?? basename($path);
        
        return response()->download($filePath, $name);
    }

    /**
     * Get file size in bytes
     *
     * @param string $path
     * @param string $disk
     * @return int
     */
    public static function size(string $path, string $disk = 'public'): int
    {
        return Storage::disk($disk)->size($path);
    }

    /**
     * Get file last modified timestamp
     *
     * @param string $path
     * @param string $disk
     * @return int
     */
    public static function lastModified(string $path, string $disk = 'public'): int
    {
        return Storage::disk($disk)->lastModified($path);
    }

    /**
     * Generate unique filename
     *
     * @param UploadedFile $file
     * @return string
     */
    protected static function generateUniqueFilename(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        $basename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $sanitized = Str::slug($basename);
        
        return $sanitized . '-' . Str::random(8) . '.' . $extension;
    }

    /**
     * List all files in a directory
     *
     * @param string $directory
     * @param string $disk
     * @return array
     */
    public static function listFiles(string $directory = '', string $disk = 'public'): array
    {
        return Storage::disk($disk)->files($directory);
    }

    /**
     * Create directory if not exists
     *
     * @param string $directory
     * @param string $disk
     * @return bool
     */
    public static function ensureDirectoryExists(string $directory, string $disk = 'public'): bool
    {
        if (!Storage::disk($disk)->exists($directory)) {
            return Storage::disk($disk)->makeDirectory($directory);
        }
        
        return true;
    }
}
