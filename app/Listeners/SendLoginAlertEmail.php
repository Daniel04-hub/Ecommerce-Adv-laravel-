<?php

namespace App\Listeners;

use App\Jobs\SendLoginAlertEmailJob;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Str;

class SendLoginAlertEmail
{
    public function handle(Login $event): void
    {
        $user = $event->user;
        if (!$user) {
            return;
        }

        $request = request();
        $ipAddress = $request?->ip();
        $loginAt = now()->toDateTimeString();
        $dedupeKey = Str::uuid()->toString();

        // Dispatch ONE job (idempotency handled by job via cache)
        SendLoginAlertEmailJob::dispatch($user->id, $ipAddress, $dedupeKey, $loginAt);
    }
}
