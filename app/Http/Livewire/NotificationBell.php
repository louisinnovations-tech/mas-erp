<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationBell extends Component
{
    public $unreadCount = 0;
    public $notifications = [];
    public $showDropdown = false;

    protected $listeners = [
        'echo:private-notifications.*,new-notification' => 'handleNewNotification',
        'notificationRead' => 'handleNotificationRead'
    ];

    public function mount()
    {
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        $this->notifications = Auth::user()
            ->notifications()
            ->latest()
            ->take(10)
            ->get();

        $this->unreadCount = Auth::user()
            ->notifications()
            ->whereNull('read_at')
            ->count();
    }

    public function handleNewNotification($notification)
    {
        $this->loadNotifications();
        $this->dispatchBrowserEvent('new-notification', [
            'title' => $notification['title'],
            'message' => $notification['message']
        ]);
    }

    public function markAsRead($notificationId)
    {
        $notification = Auth::user()
            ->notifications()
            ->find($notificationId);

        if ($notification) {
            $notification->markAsRead();
            $this->loadNotifications();
        }
    }

    public function markAllAsRead()
    {
        Auth::user()
            ->notifications()
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $this->loadNotifications();
    }

    public function render()
    {
        return view('livewire.notification-bell');
    }
}
