<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendPushNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(
        public User $user,
        public string $title,
        public string $body,
        public array $data = []
    ) {}

    public function handle(): void
    {
        $subscriptions = $this->user->pushSubscriptions;
        if ($subscriptions->isEmpty()) {
            return;
        }

        $key = config('services.firebase.server_key') ?? env('FIREBASE_SERVER_KEY');
        if (!$key) {
            Log::debug('Push notification skipped: no FIREBASE_SERVER_KEY');
            return;
        }

        foreach ($subscriptions as $sub) {
            try {
                Http::withHeaders([
                    'Authorization' => 'key=' . $key,
                    'Content-Type' => 'application/json',
                ])->post('https://fcm.googleapis.com/fcm/send', [
                    'to' => $sub->endpoint,
                    'notification' => [
                        'title' => $this->title,
                        'body' => $this->body,
                    ],
                    'data' => array_merge($this->data, ['user_id' => (string) $this->user->id]),
                ]);
            } catch (\Throwable $e) {
                Log::warning('Push failed', ['user' => $this->user->id, 'error' => $e->getMessage()]);
            }
        }
    }
}
