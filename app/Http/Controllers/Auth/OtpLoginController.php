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

        // Redirect to verification page
        return redirect()->route('otp.verify.form')
                         ->with('email', $email)
                         ->with('success', 'OTP sent to your email. Valid for 5 minutes.');
    }

    /**
     * Show OTP verification form
     *
     * @return \Illuminate\View\View
     */
    public function showVerifyForm()
    {
        $email = session('email');
        
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

        Auth::login($user, true); // Remember user

        // Redirect based on role
        return $this->redirectAfterLogin($user);
    }

    /**
     * Resend OTP
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

        // Delete existing OTP
        OtpService::delete($email, 'login');

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
            return redirect()->route('customer.dashboard')->with('success', 'Login successful!');
        }
    }
}
