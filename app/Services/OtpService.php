<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class OtpService
{
    /**
     * OTP expiry time in minutes
     */
    const DEFAULT_EXPIRY = 10; // 10 minutes
    const LOGIN_EXPIRY = 5;    // 5 minutes for login
    const COD_EXPIRY = 15;     // 15 minutes for COD verification
    const MAX_ATTEMPTS = 3;    // Max verification attempts

    /**
     * Generate a 6-digit OTP
     *
     * @return string
     */
    public static function generateCode(): string
    {
        return str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Store OTP in cache with expiry
     *
     * @param string $identifier Unique identifier (email, phone, etc.)
     * @param string $purpose (login, cod_verification, etc.)
     * @param int $expiryMinutes
     * @return string OTP code
     */
    public static function generate(string $identifier, string $purpose = 'general', int $expiryMinutes = self::DEFAULT_EXPIRY): string
    {
        $code = self::generateCode();
        $cacheKey = self::getCacheKey($identifier, $purpose);
        
        // Store OTP with metadata
        $data = [
            'code' => $code,
            'identifier' => $identifier,
            'purpose' => $purpose,
            'created_at' => now()->toDateTimeString(),
            'attempts' => 0,
        ];
        
        Cache::put($cacheKey, $data, now()->addMinutes($expiryMinutes));
        // Safe logging without exposing OTP code
        Log::info('OTP generated', [
            'purpose' => $purpose,
            'identifier_hash' => hash('sha256', $identifier),
        ]);
        
        return $code;
    }

    /**
     * Verify OTP code
     *
     * @param string $identifier
     * @param string $code
     * @param string $purpose
     * @param bool $deleteAfterVerify Delete OTP after successful verification
     * @return bool
     */
    public static function verify(string $identifier, string $code, string $purpose = 'general', bool $deleteAfterVerify = true): bool
    {
        $cacheKey = self::getCacheKey($identifier, $purpose);
        $data = Cache::get($cacheKey);
        
        if (!$data) {
            return false; // OTP expired or not found
        }
        
        // Increment attempts
        $data['attempts']++;
        
        // Verify code
        if ($data['code'] !== $code) {
            // Preserve original expiry window based on purpose
            $createdAt = \Carbon\Carbon::parse($data['created_at']);
            $expiryMinutes = match($purpose) {
                'login' => self::LOGIN_EXPIRY,
                'cod_verification' => self::COD_EXPIRY,
                default => self::DEFAULT_EXPIRY,
            };
            $expiresAt = $createdAt->copy()->addMinutes($expiryMinutes);
            // If max attempts reached, invalidate; else update attempts
            if ($data['attempts'] >= self::MAX_ATTEMPTS) {
                Cache::forget($cacheKey);
            } else {
                Cache::put($cacheKey, $data, $expiresAt); // update attempts without extending TTL
            }
            return false;
        }
        
        // Valid OTP
        if ($deleteAfterVerify) {
            Cache::forget($cacheKey);
        }
        // Safe logging without exposing OTP code
        Log::info('OTP verified successfully', [
            'purpose' => $purpose,
            'identifier_hash' => hash('sha256', $identifier),
        ]);
        
        return true;
    }

    /**
     * Check if OTP exists for identifier
     *
     * @param string $identifier
     * @param string $purpose
     * @return bool
     */
    public static function exists(string $identifier, string $purpose = 'general'): bool
    {
        return Cache::has(self::getCacheKey($identifier, $purpose));
    }

    /**
     * Get remaining time for OTP in seconds
     *
     * @param string $identifier
     * @param string $purpose
     * @return int|null Seconds remaining or null if not found
     */
    public static function getRemainingTime(string $identifier, string $purpose = 'general'): ?int
    {
        $cacheKey = self::getCacheKey($identifier, $purpose);
        $data = Cache::get($cacheKey);
        
        if (!$data) {
            return null;
        }
        
        $createdAt = \Carbon\Carbon::parse($data['created_at']);
        $expiryMinutes = self::DEFAULT_EXPIRY;
        
        if ($purpose === 'login') {
            $expiryMinutes = self::LOGIN_EXPIRY;
        } elseif ($purpose === 'cod_verification') {
            $expiryMinutes = self::COD_EXPIRY;
        }
        
        $expiresAt = $createdAt->addMinutes($expiryMinutes);
        $remaining = now()->diffInSeconds($expiresAt, false);
        
        return max(0, $remaining);
    }

    /**
     * Delete OTP
     *
     * @param string $identifier
     * @param string $purpose
     * @return bool
     */
    public static function delete(string $identifier, string $purpose = 'general'): bool
    {
        return Cache::forget(self::getCacheKey($identifier, $purpose));
    }

    /**
     * Generate cache key
     *
     * @param string $identifier
     * @param string $purpose
     * @return string
     */
    protected static function getCacheKey(string $identifier, string $purpose): string
    {
        return 'otp:' . $purpose . ':' . hash('sha256', $identifier);
    }

    /**
     * Send OTP via email
     *
     * @param string $email
     * @param string $code
     * @param string $purpose
     * @return bool
     */
    public static function sendViaEmail(string $email, string $code, string $purpose = 'general'): bool
    {
        try {
            // Queue email to avoid blocking request thread
            \Illuminate\Support\Facades\Mail::to($email)->queue(
                new \App\Mail\OtpMail($code, $purpose)
            );
            return true;
        } catch (\Exception $e) {
            Log::error('OTP Email send failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send OTP via SMS (Mock implementation)
     *
     * @param string $phone
     * @param string $code
     * @return bool
     */
    public static function sendViaSms(string $phone, string $code): bool
    {
        // Mock SMS sending
        // In production, integrate with SMS gateway (Twilio, Nexmo, etc.)
        // Safe logging without exposing OTP code
        Log::info('Mock SMS sent', [
            'phone' => $phone,
        ]);
        
        // For testing, you can also log to a file
        if (config('app.debug')) {
            $logFile = storage_path('logs/sms-mock.log');
            $message = date('Y-m-d H:i:s') . " | {$phone} | OTP sent\n";
            file_put_contents($logFile, $message, FILE_APPEND);
        }
        
        return true; // Always return true in mock mode
    }

    /**
     * Format time remaining as human-readable
     *
     * @param int $seconds
     * @return string
     */
    public static function formatTimeRemaining(int $seconds): string
    {
        if ($seconds >= 60) {
            $minutes = floor($seconds / 60);
            $secs = $seconds % 60;
            return "{$minutes}m {$secs}s";
        }
        
        return "{$seconds}s";
    }

    /**
     * Get OTP data without revealing the code
     *
     * @param string $identifier
     * @param string $purpose
     * @return array|null
     */
    public static function getInfo(string $identifier, string $purpose = 'general'): ?array
    {
        $cacheKey = self::getCacheKey($identifier, $purpose);
        $data = Cache::get($cacheKey);
        
        if (!$data) {
            return null;
        }
        
        return [
            'exists' => true,
            'created_at' => $data['created_at'],
            'attempts' => $data['attempts'],
            'remaining_seconds' => self::getRemainingTime($identifier, $purpose),
        ];
    }
}
