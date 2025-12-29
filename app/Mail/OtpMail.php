<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $code;
    public $purpose;
    public $expiryMinutes;

    /**
     * Create a new message instance.
     *
     * @param string $code
     * @param string $purpose
     */
    public function __construct(string $code, string $purpose = 'general')
    {
        $this->code = $code;
        $this->purpose = $purpose;
        
        // Set expiry based on purpose
        $this->expiryMinutes = match($purpose) {
            'login' => 5,
            'cod_verification' => 15,
            default => 10,
        };
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = match($this->purpose) {
            'login' => 'Your Login OTP Code',
            'cod_verification' => 'COD Order Verification Code',
            default => 'Your OTP Verification Code',
        };

        return $this->subject($subject)
                    ->view('emails.otp')
                    ->with([
                        'code' => $this->code,
                        'purpose' => $this->purpose,
                        'expiryMinutes' => $this->expiryMinutes,
                    ]);
    }
}
