<?php

namespace App\Services;

use Illuminate\Support\Facades\URL;
use Carbon\Carbon;

class SignedUrlService
{
    /**
     * Generate temporary signed URL for invoice download
     *
     * @param int $orderId
     * @param int $expiresInMinutes
     * @return string
     */
    public static function generateInvoiceUrl(int $orderId, int $expiresInMinutes = 60): string
    {
        return URL::temporarySignedRoute(
            'signed.invoice.download',
            Carbon::now()->addMinutes($expiresInMinutes),
            ['order' => $orderId]
        );
    }

    /**
     * Generate temporary signed URL for shipping label download
     *
     * @param int $orderId
     * @param int $expiresInMinutes
     * @return string
     */
    public static function generateShippingLabelUrl(int $orderId, int $expiresInMinutes = 60): string
    {
        return URL::temporarySignedRoute(
            'signed.shipping-label.download',
            Carbon::now()->addMinutes($expiresInMinutes),
            ['order' => $orderId]
        );
    }

    /**
     * Generate signed URL for password reset (Laravel handles this natively)
     *
     * @param string $email
     * @param string $token
     * @return string
     */
    public static function generatePasswordResetUrl(string $email, string $token): string
    {
        // Laravel's built-in password reset uses signed URLs
        return URL::signedRoute('password.reset', [
            'token' => $token,
            'email' => $email
        ]);
    }

    /**
     * Generate temporary access link for any resource
     *
     * @param string $routeName
     * @param array $parameters
     * @param int $expiresInMinutes
     * @return string
     */
    public static function generateTemporaryLink(
        string $routeName,
        array $parameters = [],
        int $expiresInMinutes = 60
    ): string {
        return URL::temporarySignedRoute(
            $routeName,
            Carbon::now()->addMinutes($expiresInMinutes),
            $parameters
        );
    }

    /**
     * Generate permanent signed URL (no expiry)
     *
     * @param string $routeName
     * @param array $parameters
     * @return string
     */
    public static function generatePermanentSignedUrl(string $routeName, array $parameters = []): string
    {
        return URL::signedRoute($routeName, $parameters);
    }

    /**
     * Generate one-time use token for sensitive operations
     *
     * @param string $identifier
     * @param int $expiresInMinutes
     * @return string
     */
    public static function generateOneTimeToken(string $identifier, int $expiresInMinutes = 15): string
    {
        // Store in cache for one-time verification
        $token = bin2hex(random_bytes(32));
        $key = "one_time_token:{$token}";
        
        \Illuminate\Support\Facades\Cache::put($key, [
            'identifier' => $identifier,
            'created_at' => now(),
        ], Carbon::now()->addMinutes($expiresInMinutes));
        
        return $token;
    }

    /**
     * Verify and consume one-time token
     *
     * @param string $token
     * @return array|null
     */
    public static function verifyOneTimeToken(string $token): ?array
    {
        $key = "one_time_token:{$token}";
        
        $data = \Illuminate\Support\Facades\Cache::get($key);
        
        if (!$data) {
            return null;
        }
        
        // Delete token after verification (one-time use)
        \Illuminate\Support\Facades\Cache::forget($key);
        
        return $data;
    }

    /**
     * Check if signed URL is valid (helper for validation)
     *
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    public static function isValidSignedUrl(\Illuminate\Http\Request $request): bool
    {
        return $request->hasValidSignature();
    }

    /**
     * Check if signed URL has expired
     *
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    public static function hasExpired(\Illuminate\Http\Request $request): bool
    {
        return $request->hasValidSignature(false) && !$request->hasValidSignature();
    }
}
