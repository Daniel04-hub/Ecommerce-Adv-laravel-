<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class OtpLoginController extends Controller
{
    /**
     * Show OTP login request page
     *
     * @return \Illuminate\View\View
     */
    public function showRequestForm()
    {
        return view('auth.otp-request');
    }

    /**
     * Send OTP to user's email
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $email = $request->email;

        // Check if OTP already sent recently (prevent spam)
        if (OtpService::exists($email, 'login')) {
            $remaining = OtpService::getRemainingTime($email, 'login');
            
            return back()->withErrors([
                'email' => "OTP already sent. Please wait {$remaining} seconds before requesting again."
            ])->withInput();
        }

        // Generate and send OTP
        $code = OtpService::generate($email, 'login', OtpService::LOGIN_EXPIRY);
        $sent = OtpService::sendViaEmail($email, $code, 'login');

        if (!$sent) {
            return back()->withErrors(['email' => 'Failed to send OTP. Please try again.'])->withInput();
        }

        // Store email in session for verification page
        session(['otp_email' => $email]);
        
        // Redirect to verification page
        return redirect()->route('otp.verify.form')
                         ->with('success', 'OTP sent to your email. Valid for 5 minutes.');
    }

    /**
     * Show OTP verification form
     *
     * @return \Illuminate\Contracts\Support\Renderable|\Illuminate\Http\RedirectResponse
     */
    public function showVerifyForm()
    {
        $email = session('otp_email');
        
        if (!$email) {
            return redirect()->route('otp.request')->withErrors(['email' => 'Please request OTP first.']);
        }

        return view('auth.otp-verify', compact('email'));
    }

    /**
     * Verify OTP and login user
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|digits:6',
        ]);

        $email = $request->email;
        $code = $request->otp;

        // Verify OTP
        $valid = OtpService::verify($email, $code, 'login', true);

        if (!$valid) {
            return back()->withErrors([
                'otp' => 'Invalid or expired OTP. Please try again.'
            ])->withInput();
        }

        // OTP verified, login user
        $user = User::where('email', $email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'User not found.']);
        }

        // Clear OTP email from session
        session()->forget('otp_email');
        
        // Regenerate session to prevent fixation attacks
        $request->session()->regenerate();
        
        Auth::login($user, true); // Remember user

        // Redirect based on role
        return $this->redirectAfterLogin($user);
    }

    /**
     * Resend OTP
     * 
     * @OA\Post(
     *     path="/login/otp/resend",
     *     tags={"OTP Authentication"},
     *     summary="Resend OTP for login",
     *     description="Request a new OTP if the previous one expired or was not received",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(property="email", type="string", format="email", example="customer@example.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="New OTP sent successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="New OTP sent to your email."),
     *             @OA\Property(property="expires_in", type="integer", example=300, description="Expiry time in seconds")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to send OTP",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Failed to send OTP.")
     *         )
     *     )
     * )
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $email = $request->email;

        // Respect cooldown: if an active OTP exists, don't resend
        if (OtpService::exists($email, 'login')) {
            $remaining = OtpService::getRemainingTime($email, 'login') ?? (OtpService::LOGIN_EXPIRY * 60);
            return response()->json([
                'success' => false,
                'message' => 'OTP already sent. Please wait before requesting again.',
                'expires_in' => $remaining,
            ], 429);
        }

        // Generate new OTP
        $code = OtpService::generate($email, 'login', OtpService::LOGIN_EXPIRY);
        $sent = OtpService::sendViaEmail($email, $code, 'login');

        if (!$sent) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP.'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'New OTP sent to your email.',
            'expires_in' => OtpService::LOGIN_EXPIRY * 60, // seconds
        ]);
    }

    /**
     * Redirect user after successful login
     *
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function redirectAfterLogin(User $user)
    {
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard')->with('success', 'Welcome back, Admin!');
        } elseif ($user->role === 'vendor') {
            return redirect()->route('vendor.dashboard')->with('success', 'Welcome back, Vendor!');
        } else {
            return redirect()->route('dashboard')->with('success', 'Login successful!');
        }
    }
}
