<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Meeting;
use App\Models\Notification;

class MeetingNotifications extends Component
{
    public $meeting;
    public $notifications = [];

    protected $listeners = [
        'echo:meeting.*,MeetingUpdated' => '$refresh',
        'notificationCreated' => '$refresh'
    ];

    public function mount(Meeting $meeting)
    {
        $this->meeting = $meeting;
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        $this->notifications = $this->meeting
            ->notifications()
            ->with('user')
            ->latest()
            ->get();
    }

    public function createNotification($type, $message)
    {
        Notification::createForMeeting(
            $this->meeting,
            $type,
            $this->meeting->participants
        );

        $this->loadNotifications();
    }

    public function render()
    {
        return view('livewire.meeting-notifications');
    }
}
