<?php

namespace App\Jobs;

use App\Mail\LoginAlertMail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendLoginAlertEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $userId;
    public ?string $ipAddress;

    public function __construct(int $userId, ?string $ipAddress = null)
    {
        $this->userId = $userId;
        $this->ipAddress = $ipAddress;
    }

    public function handle(): void
    {
        $user = User::find($this->userId);

        if (!$user) {
            Log::warning('Login alert email skipped: user missing', [
                'user_id' => $this->userId,
            ]);
            return;
        }

        $data = [
            'userName' => $user->name ?? 'Customer',
            'ipAddress' => $this->ipAddress,
        ];

        Mail::to($user->email)->send(new LoginAlertMail($data));
    }
}
