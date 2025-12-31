<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class OcrService
{
    /**
     * Extract text from an image or PDF using Tesseract if available.
     * Returns null if OCR is unavailable.
     *
     * @param string $absolutePath
     * @return string|null
     */
    public static function extractText(string $absolutePath): ?string
    {
        if (!is_file($absolutePath)) {
            Log::warning('OCR: file not found', ['path' => $absolutePath]);
            return null;
        }

        // Check for tesseract binary
        $tesseract = trim((string) shell_exec('command -v tesseract'));
        if ($tesseract === '') {
            Log::warning('OCR: tesseract not installed');
            return null;
        }

        $outputFile = tempnam(sys_get_temp_dir(), 'ocr_');
        if ($outputFile === false) {
            Log::error('OCR: failed to create temp file');
            return null;
        }

        // Run tesseract (auto-detect language; default to English)
        $cmd = sprintf('%s %s %s -l eng 2>&1', escapeshellcmd($tesseract), escapeshellarg($absolutePath), escapeshellarg($outputFile));
        $result = shell_exec($cmd);

        $txtPath = $outputFile . '.txt';
        $text = is_file($txtPath) ? file_get_contents($txtPath) : '';

        // Cleanup temp files
        @unlink($outputFile);
        @unlink($txtPath);

        if ($text === '') {
            Log::warning('OCR: no text extracted', ['path' => $absolutePath, 'stderr' => $result]);
            return null;
        }

        return $text;
    }
}
