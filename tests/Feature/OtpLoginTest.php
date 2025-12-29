<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class OtpLoginTest extends TestCase
{
    public function test_guest_can_request_otp_and_verify(): void
    {
        Mail::fake();

        $user = User::factory()->create(['email' => 'customer@example.com']);

        // Request OTP
        $response = $this->post('/login/otp/send', ['email' => $user->email]);
        $response->assertRedirect('/login/otp/verify');

        // OTP exists in cache
        $this->assertTrue(Cache::has('otp:login:' . hash('sha256', $user->email)));

        // Extract code from cache for testing
        $data = Cache::get('otp:login:' . hash('sha256', $user->email));
        $code = $data['code'] ?? null;
        $this->assertNotNull($code);

        // Verify OTP
        $response = $this->post('/login/otp/verify', [
            'email' => $user->email,
            'otp' => $code,
        ]);
        $response->assertRedirect();
    }
}
