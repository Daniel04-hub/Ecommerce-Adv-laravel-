<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\OtpService;
use Illuminate\Support\Facades\Cache;

class OtpServiceTest extends TestCase
{
    public function test_generate_and_verify_otp_with_attempt_limits(): void
    {
        Cache::flush();
        $email = 'user@example.com';
        $code = OtpService::generate($email, 'login', 1);
        $this->assertNotEmpty($code);
        $this->assertTrue(OtpService::exists($email, 'login'));

        // Wrong attempts
        $this->assertFalse(OtpService::verify($email, '000000', 'login', true));
        $this->assertFalse(OtpService::verify($email, '111111', 'login', true));
        $this->assertFalse(OtpService::verify($email, '222222', 'login', true));

        // After max attempts, OTP should be deleted
        $this->assertFalse(OtpService::exists($email, 'login'));
    }
}
