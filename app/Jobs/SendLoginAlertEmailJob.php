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

    public int $tries = 5;

    /**
     * Backoff in seconds between attempts.
     * Helps with SMTP transient failures / Mailtrap rate limiting.
     *
     * @var array<int>
     */
    public array $backoff = [10, 30, 60, 120];

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
        $this->onQueue(config('queues.payment'));
    }

    public function handle(): void
    {
        try {
            // Idempotency check: prevent duplicate email if the same job is retried
            $cacheKey = "login_alert_sent_{$this->userId}_{$this->dedupeKey}";
            if (Cache::has($cacheKey)) {
                Log::debug('Login alert email already sent (idempotent skip)', [
                    'user_id' => $this->userId,
                ]);
                return;
            }

            // Prevent concurrent duplicate sends (multiple workers / retries)
            $processingKey = $cacheKey . '_processing';
            if (! Cache::add($processingKey, true, 60)) {
                Log::debug('Login alert email already being processed (concurrent skip)', [
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

            // Queue mail (do not send synchronously)
            $mailable = (new LoginAlertMail($data))
                ->onQueue(config('queues.payment'));
            Mail::to($user->email)->queue($mailable);

            Log::info('Email queued: LoginAlertMail', [
                'user_id' => $this->userId,
                'to' => $user->email,
            ]);

            // Mark as processed (10-minute TTL per login event) AFTER a successful send
            Cache::put($cacheKey, true, 600);
        } catch (\Exception $e) {
            Log::error('Login alert email failed', [
                'user_id' => $this->userId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        } finally {
            if (isset($processingKey)) {
                Cache::forget($processingKey);
            }
        }
    }
}
