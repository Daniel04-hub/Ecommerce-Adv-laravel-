<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateSignedUrl
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if signature is valid
        if (!$request->hasValidSignature()) {
            // Check if it's expired vs invalid
            if ($request->hasValidSignature(false)) {
                // Signature is valid but expired
                return response()->view('errors.link-expired', [
                    'message' => 'This link has expired for security reasons.',
                    'hint' => 'Please request a new link or log into your account.',
                ], 403);
            }
            
            // Invalid signature
            return response()->view('errors.link-invalid', [
                'message' => 'This link is invalid or has been tampered with.',
                'hint' => 'Please ensure you used the complete link from your email.',
            ], 403);
        }

        return $next($request);
    }
}
