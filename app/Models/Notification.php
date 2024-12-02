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
        'channels', // email, sms, push, in_app
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

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function relatable()
    {
        return $this->morphTo();
    }

    // Scopes
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

    // Methods
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
        // Process each channel
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
            // Integrate with SMS service (Twilio, etc.)
            // Implementation will depend on SMS service provider
        }
    }

    protected function sendPushNotification()
    {
        if ($this->user && $this->user->push_token) {
            // Integrate with push notification service
            // Implementation will depend on service (Firebase, etc.)
        }
    }

    protected function sendInApp()
    {
        // Broadcast event for real-time notification
        broadcast(new \App\Events\NewNotification($this))->toOthers();
    }

    // Notifications can be related to various models
    public static function createForMeeting($meeting, $type, $recipients)
    {
        foreach ($recipients as $recipient) {
            self::create([
                'type' => $type,
                'user_id' => $recipient->id,
                'title' => "Meeting: {$meeting->title}",
                'message' => self::getMeetingMessage($type, $meeting),
                'data' => [
                    'meeting_id' => $meeting->id,
                    'start_time' => $meeting->start_time,
                    'location' => $meeting->location
                ],
                'channels' => ['in_app', 'email'],
                'relatable_type' => Meeting::class,
                'relatable_id' => $meeting->id
            ]);
        }
    }

    protected static function getMeetingMessage($type, $meeting)
    {
        switch ($type) {
            case 'meeting_scheduled':
                return "New meeting scheduled: {$meeting->title} on " . $meeting->start_time->format('M d, Y h:i A');
            case 'meeting_updated':
                return "Meeting details updated: {$meeting->title}";
            case 'meeting_cancelled':
                return "Meeting cancelled: {$meeting->title}";
            case 'meeting_reminder':
                return "Reminder: Meeting {$meeting->title} starts in 30 minutes";
            default:
                return $meeting->title;
        }
    }
}
