<?php

namespace App\Jobs;

use App\Models\Live;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class NotifySubscribersLiveStartedJob implements ShouldQueue
{
    use Queueable;

    public function __construct(private Live $live) {}

    public function handle(): void
    {
        try {
            // Get all subscribers of the channel
            $subscribers = User::whereHas('subscriptions', function ($q) {
                $q->where('channel_id', $this->live->user_id);
            })->with('notificationPreference')->get();

            foreach ($subscribers as $subscriber) {
                $this->notifySubscriber($subscriber);
            }

            Log::info("Notified {$subscribers->count()} subscribers about live {$this->live->id}");
        } catch (\Exception $e) {
            Log::error('NotifySubscribersLiveStartedJob failed: ' . $e->getMessage());
        }
    }

    private function notifySubscriber(User $subscriber): void
    {
        // In-app notification
        Notification::create([
            'user_id' => $subscriber->id,
            'type' => 'live_started',
            'data' => json_encode([
                'live_id' => $this->live->id,
                'channel_name' => $this->live->user->name,
                'live_title' => $this->live->title,
                'message' => "{$this->live->user->name} está ao vivo agora!",
            ]),
        ]);
    }
}
