<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Meeting;
use App\Services\Calendar\GoogleCalendarService;
use App\Services\Calendar\OutlookCalendarService;

class MeetingCalendarSync extends Component
{
    public $meeting;

    public function mount(Meeting $meeting)
    {
        $this->meeting = $meeting;
    }

    public function syncWithGoogle(GoogleCalendarService $service)
    {
        try {
            $eventId = $service->createEvent($this->meeting, auth()->user());
            
            $this->meeting->update([
                'google_event_id' => $eventId,
                'calendar_metadata' => array_merge($this->meeting->calendar_metadata ?? [], [
                    'google_synced_at' => now()->toIso8601String()
                ])
            ]);

            $this->emit('notify', [
                'type' => 'success',
                'message' => 'Meeting synced with Google Calendar'
            ]);
        } catch (\Exception $e) {
            report($e);
            $this->emit('notify', [
                'type' => 'error',
                'message' => 'Failed to sync with Google Calendar'
            ]);
        }
    }

    public function syncWithOutlook(OutlookCalendarService $service)
    {
        try {
            $eventId = $service->createEvent($this->meeting, auth()->user());
            
            $this->meeting->update([
                'outlook_event_id' => $eventId,
                'calendar_metadata' => array_merge($this->meeting->calendar_metadata ?? [], [
                    'outlook_synced_at' => now()->toIso8601String()
                ])
            ]);

            $this->emit('notify', [
                'type' => 'success',
                'message' => 'Meeting synced with Outlook Calendar'
            ]);
        } catch (\Exception $e) {
            report($e);
            $this->emit('notify', [
                'type' => 'error',
                'message' => 'Failed to sync with Outlook Calendar'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.meeting-calendar-sync');
    }
}