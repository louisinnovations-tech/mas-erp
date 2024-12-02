<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Mail;
use App\Mail\NotificationMail;

class Notification extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'type',
        'user_id',
        'title',
        'message',
        'data',
        'read_at',
        'channels',
        'scheduled_for',
        'sent_at'
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
        'sent_at' => 'datetime',
        'scheduled_for' => 'datetime',
        'channels' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function relatable()
    {
        return $this->morphTo();
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopePending($query)
    {
        return $query->whereNull('sent_at')
            ->where(function ($q) {
                $q->whereNull('scheduled_for')
                    ->orWhere('scheduled_for', '<=', now());
            });
    }

    public function markAsRead()
    {
        $this->update(['read_at' => now()]);
    }

    public function markAsSent()
    {
        $this->update(['sent_at' => now()]);
    }

    public function send()
    {
        foreach ($this->channels as $channel) {
            switch ($channel) {
                case 'email':
                    $this->sendEmail();
                    break;
                case 'sms':
                    $this->sendSMS();
                    break;
                case 'push':
                    $this->sendPushNotification();
                    break;
                case 'in_app':
                    $this->sendInApp();
                    break;
            }
        }

        $this->markAsSent();
    }

    protected function sendEmail()
    {
        if ($this->user && $this->user->email) {
            Mail::to($this->user->email)->send(new NotificationMail($this));
        }
    }

    protected function sendSMS()
    {
        if ($this->user && $this->user->phone) {
            // SMS integration placeholder
        }
    }

    protected function sendPushNotification()
    {
        if ($this->user && $this->user->push_token) {
            // Push notification integration placeholder
        }
    }

    protected function sendInApp()
    {
        broadcast(new \App\Events\NewNotification($this))->toOthers();
    }
}
