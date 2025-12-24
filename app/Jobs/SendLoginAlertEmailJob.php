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
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class SendLoginAlertEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $userId;
    public ?string $ipAddress;
    public string $dedupeKey;
    public string $loginAt;

    public function __construct(int $userId, ?string $ipAddress = null, ?string $dedupeKey = null, ?string $loginAt = null)
    {
        $this->userId = $userId;
        $this->ipAddress = $ipAddress;
        $this->dedupeKey = $dedupeKey ?? Str::uuid()->toString();
        $this->loginAt = $loginAt ?? now()->toDateTimeString();
        $this->onQueue(config('queues.shipping'));
    }

    public function handle(): void
    {
        try {
            // Idempotency check: prevent duplicate email if the same job is retried
            $cacheKey = "login_alert_sent_{$this->userId}_{$this->dedupeKey}";
            if (Cache::has($cacheKey)) {
                Log::info('Login alert email already sent (idempotent skip)', [
                    'user_id' => $this->userId,
                ]);
                return;
            }

            $user = User::find($this->userId);

            if (!$user) {
                Log::warning('Login alert email skipped: user missing', [
                    'user_id' => $this->userId,
                ]);
                return;
            }

            // Build validated data array
            $data = [
                'userName' => $user->name ?? 'User',
                'userEmail' => $user->email,
                'ipAddress' => $this->ipAddress ?? 'Unknown',
                'loginAt' => $this->loginAt,
            ];

            // Guard: ensure all required keys exist
            if (!isset($data['userName']) || !isset($data['userEmail'])) {
                Log::error('Login alert email skipped: required data missing', [
                    'user_id' => $this->userId,
                    'data_keys' => array_keys($data),
                ]);
                return;
            }

            Log::info('Sending login alert email', [
                'user_id' => $this->userId,
                'ip_address' => $data['ipAddress'],
            ]);

            // Mark as processed (10-minute TTL per login event)
            Cache::put($cacheKey, true, 600);

            // ONLY place where mail is sent
            Mail::to($user->email)->send(new LoginAlertMail($data));

            Log::info('Login alert email sent successfully', [
                'user_id' => $this->userId,
            ]);
        } catch (\Exception $e) {
            Log::error('Login alert email failed', [
                'user_id' => $this->userId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
