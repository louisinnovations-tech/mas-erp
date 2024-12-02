<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationSystem extends Component
{
    public $notifications = [];
    public $unreadCount = 0;

    protected $listeners = [
        'echo:notifications,NotificationCreated' => 'handleNewNotification',
        'markAsRead' => 'markNotificationAsRead',
        'refreshNotifications' => '$refresh'
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
            ->unreadNotifications()
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

    public function markNotificationAsRead($notificationId)
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
        Auth::user()->unreadNotifications()->update(['read_at' => now()]);
        $this->loadNotifications();
    }

    public function render()
    {
        return view('livewire.notification-system');
    }
}
