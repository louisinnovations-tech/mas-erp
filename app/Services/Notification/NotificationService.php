<?php

namespace App\Services\Notification;

use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    public function send(User $user, string $type, string $title, string $message, array $data = [])
    {
        $notification = Notification::create([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data
        ]);

        $this->pushNotification($notification);

        if ($user->notification_preferences['email'][$type] ?? true) {
            $this->sendEmail($notification);
        }

        return $notification;
    }

    protected function pushNotification(Notification $notification)
    {
        broadcast(new \App\Events\NotificationCreated($notification));
    }

    protected function sendEmail(Notification $notification)
    {
        Mail::to($notification->user->email)
            ->queue(new \App\Mail\NotificationMail($notification));
    }

    public function markAsRead(Notification $notification)
    {
        $notification->update(['read_at' => now()]);
        return $notification;
    }

    public function markAllAsRead(User $user)
    {
        return $user->notifications()
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }
}