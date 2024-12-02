<?php

namespace App\Mail;

use App\Models\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $notification;

    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
    }

    public function build()
    {
        return $this->markdown('emails.notification')
            ->subject($this->notification->title)
            ->with([
                'notification' => $this->notification,
                'actionUrl' => $this->notification->data['action_url'] ?? null,
                'actionText' => $this->notification->data['action_text'] ?? null
            ]);
    }
}