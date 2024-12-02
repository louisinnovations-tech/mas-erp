<?php

namespace App\Events;

use App\Models\Meeting;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MeetingEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $meeting;
    public $type;
    public $data;

    public function __construct(Meeting $meeting, string $type, array $data = [])
    {
        $this->meeting = $meeting;
        $this->type = $type;
        $this->data = $data;
    }

    public function broadcastOn()
    {
        return new PresenceChannel('meeting.' . $this->meeting->id);
    }

    public function broadcastAs()
    {
        return 'meeting.' . $this->type;
    }

    public function broadcastWith()
    {
        return array_merge([
            'meeting_id' => $this->meeting->id,
            'type' => $this->type,
            'timestamp' => now()->toIso8601String()
        ], $this->data);
    }
}